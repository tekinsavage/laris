<?php

namespace Laris\Commands\Ai;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeFactoryCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('laris:ai:make:factory')
            ->setDescription('Generate a Laravel factory class using OpenRouter AI');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!file_exists(getcwd() . '/artisan')) {
            $io->error('This command must be run inside a Laravel project.');
            return Command::FAILURE;
        }

        $configPath = getcwd() . '/.laris-ai.json';
        if (!file_exists($configPath)) {
            $io->error('AI config file not found. Run `laris:ai:config` first.');
            return Command::FAILURE;
        }

        $config = json_decode(file_get_contents($configPath), true);
        $apiKey = $config['api_key'] ?? null;
        $provider = $config['provider'] ?? null;
        $maxTokens = (int)($config['max_tokens'] ?? 1000);
        $model = $config['model'] ?? 'deepseek/deepseek-r1-0528-qwen3-8b:free';
        $defaultPrompt = $config['default_prompt'] ?? '';

        if ($provider !== 'openrouter' || !$apiKey) {
            $io->error('OpenRouter provider or API key not configured.');
            return Command::FAILURE;
        }

        $name = $io->ask('What is the name of the factory?', 'PostFactory');

        $prompt = <<<PROMPT
{$defaultPrompt}

You are a Laravel 10 expert. Generate a factory class named "{$name}" for the Post model. Each post should have a random title (sentence), body (paragraph), and user_id (random number from 1 to 10). Use Faker and Laravel factory conventions.
PROMPT;

        $io->section('🧠 Generating factory using AI...');
        sleep(3);

        $code = $this->callOpenRouter($apiKey, $prompt, $maxTokens, $model);

        if (!$code) {
            $io->error('Failed to fetch response from OpenRouter.');
            return Command::FAILURE;
        }

        $io->section('✅ AI Generated Factory Code');
        $io->writeln("<info>$code</info>");

        $save = $io->confirm('Do you want to save this factory to database/factories?', true);
        if ($save) {
            $path = getcwd() . "/database/factories/{$name}.php";
            file_put_contents($path, $code);
            $io->success("Factory saved to database/factories/{$name}.php");
        }

        return Command::SUCCESS;
    }

    private function callOpenRouter(string $apiKey, string $prompt, int $maxTokens, string $model): ?string
    {
        $postData = [
            'model' => $model,
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'max_tokens' => $maxTokens,
        ];

        $ch = curl_init('https://openrouter.ai/api/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Authorization: Bearer {$apiKey}",
            "X-Title: Laris AI CLI",
        ]);

        $result = curl_exec($ch);
        curl_close($ch);

        if (!$result) return null;
        $json = json_decode($result, true);
        return $json['choices'][0]['message']['content'] ?? null;
    }
}
