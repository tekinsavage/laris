<?php

namespace Laris\Commands\Ai;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeRuleCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('laris:ai:make:rule')
            ->setDescription('Generate a Laravel Rule using OpenRouter AI');
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
        $model = $config['model'] ?? 'deepseek/deepseek-r1-0528-qwen3-8b:free';
        $maxTokens = (int) ($config['max_tokens'] ?? 1000);
        $defaultPrompt = $config['default_prompt'] ?? '';

        if (!$apiKey) {
            $io->error('API key not found in .laris-ai.json');
            return Command::FAILURE;
        }

        $ruleName = $io->ask('What is the name of the Rule?', 'StrongPasswordRule');

        $prompt = <<<PROMPT
{$defaultPrompt}

You are a Laravel expert. Generate a Laravel Rule class named "{$ruleName}" that validates strong passwords. The rule should implement the 'passes' method and include a message. Follow Laravel 10 and PSR-12 standards.
PROMPT;

        $io->section('🧠 Generating rule using OpenRouter AI...');
        sleep(2);

        $code = $this->callOpenRouter($apiKey, $prompt, $maxTokens, $model);

        if (!$code) {
            $io->error('Failed to get response from OpenRouter.');
            return Command::FAILURE;
        }

        $io->writeln("<info>$code</info>");
        if ($io->confirm('Save this rule to your Laravel app?', true)) {
            $path = getcwd() . "/app/Rules/{$ruleName}.php";
            file_put_contents($path, $code);
            $io->success("Rule saved to app/Rules/{$ruleName}.php");
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
