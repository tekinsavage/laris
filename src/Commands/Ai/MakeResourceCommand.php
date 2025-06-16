<?php

namespace Laris\Commands\Ai;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeResourceCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('laris:ai:make:resource')
            ->setDescription('Generate a Laravel API Resource using OpenRouter AI');
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

        $name = $io->ask('What is the name of the resource class?', 'ExampleResource');

        $prompt = <<<PROMPT
{$defaultPrompt}

You are an expert Laravel developer. Please generate a Laravel API Resource class named "{$name}". Assume the resource wraps a model with common fields like id, name, created_at, updated_at. Follow PSR-12 and Laravel 10 standards.
PROMPT;

        $io->section('🧠 Generating resource using AI...');
        $io->text('Please wait...');
        sleep(3);

        $code = $this->callOpenRouter($apiKey, $prompt, $maxTokens, $model);

        if (!$code) {
            $io->error('Failed to fetch response from OpenRouter.');
            return Command::FAILURE;
        }

        $io->section('✅ AI Generated Resource Code');
        $io->writeln("<info>$code</info>");

        $save = $io->confirm('Do you want to save this resource to app/Http/Resources?', true);
        if ($save) {
            $path = getcwd() . "/app/Http/Resources/{$name}.php";
            file_put_contents($path, $code);
            $io->success("Resource saved to app/Http/Resources/{$name}.php");
        }

        return Command::SUCCESS;
    }

    private function callOpenRouter(string $apiKey, string $prompt, int $maxTokens, string $model): ?string
    {
        $postData = [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
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
