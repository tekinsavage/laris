<?php

namespace Laris\Commands\Ai;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateReadmeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('laris:ai:generate:readme')
            ->setDescription('Generate a professional README.md file for your Laravel project using AI');
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

        $description = $io->ask('Briefly describe your project (optional)', '');

        $prompt = <<<PROMPT
{$defaultPrompt}

You are a senior Laravel developer and documentation writer.
Generate a full README.md file for this Laravel project. 
Include title, description, installation, usage, environment setup, features, testing, and license.
Project Description: {$description}
PROMPT;

        $io->section('📄 Generating README.md using OpenRouter AI...');
        $response = $this->callOpenRouter($apiKey, $prompt, $maxTokens, $model);

        if (!$response) {
            $io->error('Failed to fetch README.md content from AI.');
            return Command::FAILURE;
        }

        $io->section('✅ AI Generated README.md');
        $io->writeln("<info>$response</info>");

        $save = $io->confirm('Do you want to save this as README.md?', true);
        if ($save) {
            file_put_contents(getcwd() . '/README.md', $response);
            $io->success('README.md file has been saved.');
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