<?php

namespace Laris\Commands\Ai;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeModelCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('laris:ai:make:model')
            ->setDescription('Generate a Laravel model (with migration and factory) using OpenRouter AI');
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

        $modelName = $io->ask('What is the name of the model?', 'Post');
        if (!preg_match('/^[A-Z][A-Za-z0-9_]*$/', $modelName)) {
            $io->error('Invalid model name. It should be in StudlyCase.');
            return Command::FAILURE;
        }

        $prompt = <<<PROMPT
{$defaultPrompt}

You are a Laravel expert. Please generate a Laravel Eloquent model named "{$modelName}" including:
- corresponding migration with typical fields,
- factory with realistic dummy data using Faker,
- proper relationships if the model is common (e.g. User, Post, Comment),
- all code should be PSR-12 compliant and compatible with Laravel 10.
PROMPT;

        $io->section('🧠 Generating model using OpenRouter AI...');
        $io->text('Please wait while we contact the AI service...');

        for ($i = 0; $i < 3; $i++) {
            sleep(1);
            $io->write('.');
        }
        $io->newLine(2);

        $response = $this->callOpenRouter($apiKey, $prompt, $maxTokens, $model);

        if (!$response) {
            $io->error('Failed to fetch response from OpenRouter.');
            return Command::FAILURE;
        }

        $io->section('✅ AI Generated Model Code');
        $io->writeln("<info>$response</info>");

        $save = $io->confirm('Do you want to save this model to your Laravel project?', true);

        if ($save) {
            $path = getcwd() . "/app/Models/{$modelName}.php";
            file_put_contents($path, $response);
            $io->success("Model saved to app/Models/{$modelName}.php");
        } else {
            $io->note('Model was not saved.');
        }

        return Command::SUCCESS;
    }

    private function callOpenRouter(string $apiKey, string $prompt, int $maxTokens, string $model = 'deepseek/deepseek-r1-0528-qwen3-8b:free'): ?string
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
