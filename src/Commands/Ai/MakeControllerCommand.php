<?php

namespace Laris\Commands\Ai;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeControllerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('laris:ai:make:controller')
            ->setDescription('Generate a Laravel controller using OpenRouter AI');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Check Laravel project
        if (!file_exists(getcwd() . '/artisan')) {
            $io->error('This command must be run inside a Laravel project.');
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
        $maxTokens = $config['max_tokens'] ?? 1000;
        $model = $config['model'] ?? 'deepseek/deepseek-r1-0528-qwen3-8b:free';
        $defaultPrompt = $config['default_prompt'] ?? '';

        if ($provider !== 'openrouter' || !$apiKey) {
            $io->error('OpenRouter provider or API key not configured.');
            return Command::FAILURE;
        }

        // Ask for controller name
        $controllerName = $io->ask('What is the name of the controller?', 'ExampleController');
        if (!str_ends_with($controllerName, 'Controller')) {
            $controllerName .= 'Controller';
        }

        // Build AI prompt
        $prompt = <<<PROMPT
{$defaultPrompt}

You are an expert Laravel assistant. Please generate a Laravel controller named "{$controllerName}" with full CRUD methods using Request and route-model binding where appropriate. Make sure the code is clean, PSR-12 compliant, and suitable for Laravel 10.
PROMPT;

        $io->section('🧠 Generating code using OpenRouter AI...');
        $io->text('Please wait while we contact the AI service...');

        // Simulate loading...
        for ($i = 0; $i < 3; $i++) {
            sleep(1);
            $io->write('.');
        }
        $io->newLine(2);

        // Make request to OpenRouter
        $response = $this->callOpenRouter($apiKey,  $prompt, $maxTokens, $model);

        if (!$response) {
            $io->error('Failed to fetch response from OpenRouter.');
            return Command::FAILURE;
        }

        $code = $response;

        $io->section('✅ AI Generated Controller Code');
        $io->writeln("<info>$code</info>");

        // Ask user what to do
        $save = $io->confirm('Do you want to save this controller to your Laravel project?', true);

        if ($save) {
            $path = getcwd() . "/app/Http/Controllers/{$controllerName}.php";
            file_put_contents($path, $code);
            $io->success("Controller saved to app/Http/Controllers/{$controllerName}.php");
        } else {
            $io->note('Controller was not saved.');
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
            "X-Title: Laris AI CLI", // optional
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
