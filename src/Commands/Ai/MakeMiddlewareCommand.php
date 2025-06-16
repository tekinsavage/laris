namespace Laris\Commands\Ai;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeMiddlewareCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('laris:ai:make:middleware')
            ->setDescription('Generate a Laravel middleware using OpenRouter AI');
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

        $middlewareName = $io->ask('What is the name of the middleware?', 'ExampleMiddleware');

        $prompt = <<<PROMPT
{$defaultPrompt}

Generate a Laravel HTTP Middleware class named "{$middlewareName}". It should be PSR-12 compliant, compatible with Laravel 10, and demonstrate how to modify requests or responses.
PROMPT;

        $io->section('🧠 Generating middleware class...');
        $io->write('Thinking'); sleep(1); $io->write('.'); sleep(1); $io->write('.'); sleep(1); $io->writeln('.');

        $code = $this->callOpenRouter($apiKey, $prompt, $maxTokens, $model);
        if (!$code) {
            $io->error('Failed to get response from OpenRouter.');
            return Command::FAILURE;
        }

        $io->writeln("<info>$code</info>");

        $save = $io->confirm('Save this middleware?', true);
        if ($save) {
            file_put_contents(getcwd() . "/app/Http/Middleware/{$middlewareName}.php", $code);
            $io->success("Middleware saved to app/Http/Middleware/{$middlewareName}.php");
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

        $json = json_decode($result, true);
        return $json['choices'][0]['message']['content'] ?? null;
    }
}
