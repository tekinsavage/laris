<?php

namespace Laris\Commands\Ai;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateDocsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('laris:ai:generate:docs')
            ->setDescription('Generate Markdown documentation for a PHP class using OpenRouter AI')
            ->addArgument('file', InputArgument::REQUIRED, 'Relative path to the PHP file (e.g., app/Services/MyService.php)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = getcwd() . '/' . $input->getArgument('file');

        if (!file_exists($filePath)) {
            $io->error("File not found: {$filePath}");
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
        $maxTokens = (int) ($config['max_tokens'] ?? 1500);
        $model = $config['model'] ?? 'deepseek/deepseek-r1-0528-qwen3-8b:free';
        $defaultPrompt = $config['default_prompt'] ?? '';

        if ($provider !== 'openrouter' || !$apiKey) {
            $io->error('OpenRouter provider or API key not configured.');
            return Command::FAILURE;
        }

        $originalCode = file_get_contents($filePath);

        $prompt = <<<PROMPT
{$defaultPrompt}

You are a Laravel documentation expert. Analyze the following PHP class and generate complete documentation in Markdown format. The documentation should include:

- Overview
- Purpose
- Dependencies
- Method descriptions
- Example usage if possible
- Any relevant Laravel-specific behaviors

Output only valid Markdown content:

```php
{$originalCode}
PROMPT;

        $io->section('📝 Generating documentation using AI...');
        sleep(2);

        $markdown = $this->callOpenRouter($apiKey, $prompt, $maxTokens, $model);

        if (!$markdown) {
            $io->error('Failed to receive documentation from AI.');
            return Command::FAILURE;
        }

        $io->section('✅ Generated Documentation Preview');
        $io->writeln($markdown);

        if ($io->confirm('Do you want to save this documentation as a Markdown file?', true)) {
            $docsPath = preg_replace('/\.php$/', '.md', $filePath);
            file_put_contents($docsPath, $markdown);
            $io->success("Documentation saved to: {$docsPath}");
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