<?php

namespace Laris\Commands\Ai;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Exception\RuntimeException;

class ConfigCommand extends Command
{
    protected static $defaultName = 'laris:ai:config';
    protected static $defaultDescription = 'Configure your AI assistant (OpenAI or OpenRouter) for Laris CLI.';

    protected function configure()
    {
        $this
            ->setName('laris:ai:config')
            ->setDescription('Configure your AI assistant (OpenAI or OpenRouter) for Laris CLI.')
            ->setHelp('This command lets you set up and store configuration for AI-based assistant commands in Laris CLI.');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        $io->title('🤖 Laris AI Configuration Wizard');
        $io->text([
            'This setup will configure your AI provider, API key, and default behavior.',
            'You can change these settings anytime by running this command again.',
            '',
        ]);

        // Choose provider
        $provider = $io->choice(
            'Select your AI provider',
            ['openai', 'openrouter'],
            'openrouter'
        );

        $model = $io->ask('Select Ai Model', 'deepseek/deepseek-r1-0528-qwen3-8b:free');
        // API Key
        $apiKey = $io->askHidden("Enter your API key for [$provider]", function ($key) {
            if (empty($key)) {
                throw new \RuntimeException('API key cannot be empty.');
            }
            return $key;
        });

        // Max Tokens
        $maxTokens = $io->ask('Maximum tokens per completion?', '1000', function ($value) {
            if (!is_numeric($value) || (int) $value <= 0) {
                throw new \RuntimeException('Please enter a valid positive number.');
            }
            return (int) $value;
        });

        // Default Prompt Prefix
        $defaultPrompt = $io->ask(
            'Default prompt prefix for AI assistant?',
            'You are a Laravel assistant that helps generate code, commands, and resources.'
        );

        // Prepare config array
        $config = [
            'provider' => $provider,
            'api_key' => $apiKey,
            'max_tokens' => $maxTokens,
            'default_prompt' => $defaultPrompt,
            'moedl' => $model
        ];

        $configPath = getcwd() . '/.laris-ai.json';

        try {
            file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } catch (\Exception $e) {
            $io->error("Failed to write configuration file: " . $e->getMessage());
            return Command::FAILURE;
        }

        // Output summary
        $io->success('AI configuration saved successfully! 🎉');
        $io->note("Path: $configPath");

        $io->section('Configuration Summary');
        $io->listing([
            "Provider       : $provider",
            "Max Tokens     : $maxTokens",
            "Model Ai       : $model",  
            "Prompt Preview : " . substr($defaultPrompt, 0, 50) . (strlen($defaultPrompt) > 50 ? '...' : ''),
        ]);

        return Command::SUCCESS;
    }
}
