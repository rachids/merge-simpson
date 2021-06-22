<?php

namespace App\Commands;

use App\BotCommands\MergeRequestBot;
use App\BotCommands\WeatherBot;
use Discord\DiscordCommandClient;
use LaravelZero\Framework\Commands\Command;

class DiscordCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'bot:start';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Démarre le bot Discord.';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $config = [
            'token' => env('DISCORD_TOKEN'),
            'prefix' => '!',
        ];

        $discord = new DiscordCommandClient($config);

        // Register every commands
        $this->registerCommands($discord);

        $discord->run();
    }

    private function registerCommands(DiscordCommandClient $discord): void
    {
        foreach ($this->getCommands() as $command) {
            $discord->registerCommand($command['name'], $command['callable'], $command['options']);
        }
    }

    /**
     * Return an array containing every discord commands available.
     * Refer to DiscordPHP documentation for available keys.
     *
     * @return array[]
     */
    private function getCommands(): array
    {
        return [
            [
                'name' => 'Merge Requests',
                'callable' => new MergeRequestBot(),
                'options' => [
                    'aliases' => [
                        'mr',
                        'merge-request',
                        'merge-requests',
                        'mergerequest',
                        'mergerequests',
                        'merge',
                    ],
                    'cooldown' => 10,
                    'cooldownMessage' => 'Please wait a few seconds before sending the merge command.',
                    'description' => 'Lists merge requests waiting for approval',
                    'longDescription' => 'Give merge requests that are opened and without two thumbsups so that they can be reviewed.',
                ],
            ],
            [
                'name' => 'Weather',
                'callable' => new WeatherBot(),
                'options' => [
                    'aliases' => [
                        'weather',
                        'meteo',
                    ],
                    'cooldown' => 10,
                    'cooldownMessage' => 'Please wait a few seconds before sending the weather command.',
                    'description' => 'Provide the weather for a given location.',
                    'longDescription' => 'Give the weather for a location, use it like this: `!weather {city-name}`',
                ],
            ],
        ];
    }
}
