<?php

namespace Laris\Commands\Ai;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateTestCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('laris:ai:test:generate')
            ->setDescription('Generate PHPUnit test for a Laravel class using OpenRouter AI');
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
            $io->error('AI config file (.laris-ai.json) not found. Run `laris:ai:config` first.');
            return Command::FAILURE;
        }

        $config = json_decode(file_get_contents($configPath), true);
        $apiKey = $config['api_key'] ?? null;
        $provider = $config['provider'] ?? null;
        $maxTokens = (int) ($config['max_tokens'] ?? 1000);
        $model = $config['model'] ?? 'deepseek/deepseek-r1-0528-qwen3-8b:free';
        $defaultPrompt = $config['default_prompt'] ?? '';

        if ($provider !== 'openrouter' || !$apiKey) {
            $io->error('OpenRouter provider or API key not configured.');
            return Command::FAILURE;
        }

        $path = $io->ask('Enter the path to the class (e.g., app/Services/MyService.php)');
        if (!file_exists($path)) {
            $io->error('File not found: ' . $path);
            return Command::FAILURE;
        }

        $originalCode = file_get_contents($path);
        $prompt = <<<PROMPT
{$defaultPrompt}

You are an expert Laravel developer. Generate a full PHPUnit test class for the following PHP class. Use Laravel testing best practices and Mock dependencies if necessary. Output just the code block.

```php
{$originalCode}
PROMPT;
        $io->section('🧪 Generating test class using OpenRouter AI...');
        $io->text('Please wait...');

        $response = $this->callOpenRouter($apiKey, $prompt, $maxTokens, $model);

        if (!$response) {
            $io->error('Failed to get response from AI.');
            return Command::FAILURE;
        }

        $io->section('✅ AI Generated Test');
        $io->writeln("<info>$response</info>");

        $save = $io->confirm('Do you want to save this test?', true);
        if ($save) {
            $testName = basename($path, '.php') . 'Test.php';
            $testPath = getcwd() . '/tests/Feature/' . $testName;
            file_put_contents($testPath, $response);
            $io->success("Test saved to tests/Feature/{$testName}");
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

        if (!$result)
            return null;
        $json = json_decode($result, true);
        return $json['choices'][0]['message']['content'] ?? null;
    }
}