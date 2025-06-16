<?php

namespace Laris\Commands\Ai;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeObserverCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('laris:ai:make:observer')
            ->setDescription('Generate a Laravel model observer using OpenRouter AI');
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

        $observerName = $io->ask('What is the name of the observer?', 'PostObserver');

        $prompt = <<<PROMPT
{$defaultPrompt}

You are a Laravel 10 expert. Generate an observer class named "{$observerName}" that listens to the following model events: creating, updating, deleting. Add example logic for logging each action using Laravel's Log facade. Follow PSR-12 standards.
PROMPT;

        $io->section('🧠 Generating observer using AI...');
        sleep(2);

        $code = $this->callOpenRouter($apiKey, $prompt, $maxTokens, $model);

        if (!$code) {
            $io->error('Failed to fetch response from OpenRouter.');
            return Command::FAILURE;
        }

        $io->section('✅ AI Generated Observer Code');
        $io->writeln("<info>$code</info>");

        $save = $io->confirm('Do you want to save this observer to app/Observers?', true);
        if ($save) {
            @mkdir(getcwd() . "/app/Observers", 0755, true);
            $path = getcwd() . "/app/Observers/{$observerName}.php";
            file_put_contents($path, $code);
            $io->success("Observer saved to app/Observers/{$observerName}.php");
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
