<?php

namespace Laris\Commands\Ai;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeRequestCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('laris:ai:make:request')
            ->setDescription('Generate a Laravel Form Request using OpenRouter AI');
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
        $maxTokens = (int)($config['max_tokens'] ?? 1000);
        $model = $config['model'] ?? 'deepseek/deepseek-r1-0528-qwen3-8b:free';
        $defaultPrompt = $config['default_prompt'] ?? '';

        if ($provider !== 'openrouter' || !$apiKey) {
            $io->error('OpenRouter provider or API key not configured.');
            return Command::FAILURE;
        }

        // Ask for request name
        $requestName = $io->ask('What is the name of the Form Request?', 'StoreExampleRequest');
        if (!str_ends_with($requestName, 'Request')) {
            $requestName .= 'Request';
        }

        // Prompt
        $prompt = <<<PROMPT
{$defaultPrompt}

You are a Laravel expert. Generate a Laravel Form Request class named "{$requestName}".
- Include validation rules.
- Include `authorize()` method that returns true.
- Follow PSR-12 and Laravel 10 standards.
PROMPT;

        $io->section('🧠 Generating code using OpenRouter AI...');
        $io->text('Please wait while we contact the AI service...');
        for ($i = 0; $i < 3; $i++) {
            sleep(1);
            $io->write('.');
        }
        $io->newLine(2);

        // Make request
        $response = $this->callOpenRouter($apiKey, $prompt, $maxTokens, $model);

        if (!$response) {
            $io->error('Failed to fetch response from OpenRouter.');
            return Command::FAILURE;
        }

        $code = $response;

        $io->section('✅ AI Generated Request Code');
        $io->writeln("<info>$code</info>");

        $save = $io->confirm('Do you want to save this Form Request to your Laravel project?', true);
        if ($save) {
            $path = getcwd() . "/app/Http/Requests/{$requestName}.php";
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }
            file_put_contents($path, $code);
            $io->success("Request saved to app/Http/Requests/{$requestName}.php");
        } else {
            $io->note('Request was not saved.');
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
