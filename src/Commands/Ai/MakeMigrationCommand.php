<?php

namespace Laris\Commands\Ai;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeMigrationCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('laris:ai:make:migration')
            ->setDescription('Generate a Laravel migration file using OpenRouter AI');
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

        $name = $io->ask('What is the name of the migration?', 'create_posts_table');

        $prompt = <<<PROMPT
{$defaultPrompt}

You are a Laravel 10 expert. Generate a migration named "{$name}" that creates the posts table with these columns: id, title (string), body (text), user_id (foreign key), timestamps. Use Laravel schema builder and PSR-12.
PROMPT;

        $io->section('🧠 Generating migration using AI...');
        sleep(3);

        $code = $this->callOpenRouter($apiKey, $prompt, $maxTokens, $model);

        if (!$code) {
            $io->error('Failed to fetch response from OpenRouter.');
            return Command::FAILURE;
        }

        $io->section('✅ AI Generated Migration Code');
        $io->writeln("<info>$code</info>");

        $save = $io->confirm('Do you want to save this migration to database/migrations?', true);
        if ($save) {
            $filename = now()->format('Y_m_d_His') . "_{$name}.php";
            $path = getcwd() . "/database/migrations/{$filename}";
            file_put_contents($path, $code);
            $io->success("Migration saved to database/migrations/{$filename}");
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
