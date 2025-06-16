<?php

namespace Laris\Commands\Ai;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RefactorClassCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('laris:ai:refactor:class')
            ->setDescription('Refactor a Laravel PHP class using OpenRouter AI')
            ->addArgument('file', InputArgument::REQUIRED, 'Relative path to the PHP class file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = getcwd() . '/' . $input->getArgument('file');

        // Check if file exists
        if (!file_exists($filePath)) {
            $io->error("File not found: {$filePath}");
            return Command::FAILURE;
        }

        // Load config
        $configPath = getcwd() . '/.laris-ai.json';
        if (!file_exists($configPath)) {
            $io->error('AI config file (.laris-ai.json) not found. Run `laris:ai:config` first.');
            return Command::FAILURE;
        }

        $config = json_decode(file_get_contents($configPath), true);
        $apiKey = $config['api_key'] ?? null;
        $provider = $config['provider'] ?? null;
        $maxTokens = (int) ($config['max_tokens'] ?? 2000);
        $model = $config['model'] ?? 'deepseek/deepseek-r1-0528-qwen3-8b:free';
        $defaultPrompt = $config['default_prompt'] ?? '';

        if ($provider !== 'openrouter' || !$apiKey) {
            $io->error('OpenRouter provider or API key not configured.');
            return Command::FAILURE;
        }

        // Read file content
        $originalCode = file_get_contents($filePath);

        $prompt = <<<PROMPT
{$defaultPrompt}

You are a senior Laravel developer. Refactor the following PHP class according to best practices, clean architecture, SOLID principles, and PSR-12 coding style. Improve naming, remove code smells, and make it production-grade:

```php
{$originalCode}
PROMPT;
        $io->section('🔍 Refactoring class using AI...');
        $io->text('Please wait while we contact the AI...');

        sleep(2);

        $refactored = $this->callOpenRouter($apiKey, $prompt, $maxTokens, $model);

        if (!$refactored) {
            $io->error('AI service failed to return a result.');
            return Command::FAILURE;
        }

        $io->section('✅ Refactored Class');
        $io->writeln("<info>$refactored</info>");

        if ($io->confirm('Do you want to replace the original file with the refactored version?', false)) {
            file_put_contents($filePath, $refactored);
            $io->success("File successfully updated: {$filePath}");
        } else {
            $io->note('Refactoring was not saved.');
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

        if (!$result) {
            return null;
        }

        $json = json_decode($result, true);
        return $json['choices'][0]['message']['content'] ?? null;
    }
}
