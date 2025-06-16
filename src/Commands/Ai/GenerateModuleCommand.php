<?php

namespace Laris\Commands\Ai;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateModuleCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('laris:ai:generate:module')
            ->setDescription('Generate a full Laravel module (model, migration, controller, service, etc.) using AI');
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
        $maxTokens = (int)($config['max_tokens'] ?? 1500);
        $model = $config['model'] ?? 'deepseek/deepseek-r1-0528-qwen3-8b:free';
        $defaultPrompt = $config['default_prompt'] ?? '';

        if ($provider !== 'openrouter' || !$apiKey) {
            $io->error('OpenRouter provider or API key not configured.');
            return Command::FAILURE;
        }

        $moduleName = $io->ask('What is the name of the module?', 'Product');
        $moduleName = ucfirst(trim($moduleName));

        $prompt = <<<PROMPT
{$defaultPrompt}

You are a senior Laravel developer. Please generate a full Laravel module named "{$moduleName}" with the following components:
- Eloquent Model
- Migration for creating the related table
- RESTful Controller with CRUD methods and route-model binding
- Form Request for validation
- Resource class for formatting output
- Service class to handle business logic
- Web or API route definition (choose API by default)

Follow Laravel 10 conventions, use PSR-12, and make sure the code is clean and production-ready.
PROMPT;

        $io->section('🧠 Generating Laravel module with AI...');
        $io->text('Please wait...');

        for ($i = 0; $i < 3; $i++) {
            sleep(1);
            $io->write('.');
        }
        $io->newLine(2);

        $response = $this->callOpenRouter($apiKey, $prompt, $maxTokens, $model);

        if (!$response) {
            $io->error('Failed to get response from OpenRouter.');
            return Command::FAILURE;
        }

        $io->section("✅ AI Generated Code for {$moduleName} Module");
        $io->writeln("<info>$response</info>");

        $save = $io->confirm('Do you want to save the files manually?', false);

        if ($save) {
            $path = getcwd() . "/ai-modules/{$moduleName}.md";
            file_put_contents($path, $response);
            $io->success("Module generated and saved to: {$path}");
        } else {
            $io->note('Module was not saved.');
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
