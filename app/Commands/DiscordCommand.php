<?php

namespace App\Commands;

use App\Service\DiscordService;
use App\Service\GitlabService;
use App\Service\WeatherService;
use Discord\DiscordCommandClient;
use Discord\Parts\Channel\Message;
use Illuminate\Console\Scheduling\Schedule;
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

        $discord->registerCommand('mr', function (Message $message, array $parameters) {

            $discordService = new DiscordService($message->channel->guild);

            $channel = $discordService->getChannelByName('pull-request');
            $channel->broadcastTyping();

            $role = $discordService->getRoleByName('Dev');

            $gitlabService = new GitlabService();

            $projects = explode(',', env('GITLAB_PROJECTS'));

            $message = $gitlabService->getMergeMessage($projects);

            if(! empty($message)) {
                $channel->sendMessage("Chers <@&{$role->id}>! Je crois que ces merge-requests ont besoin de votre amour :blue_heart: \n ");
                $channel->sendMessage($message);
            }
        });

        $discord->registerCommand('meteo', function (Message $message, array $parameters) {

            $message->channel->broadcastTyping();

            $city = $parameters[0] ?? 'Québec';

            $weather = WeatherService::getWeatherForCity($city)[0];

            $emoji = match ($weather['prec_type']) {
                'rain' => '🌧️',
                'snow' => '🌨️',
                default => '☀️'
            };

            $message->channel->sendMessage("Sur {$city}, il fait **{$weather['temp2m']} ℃** et le temps est {$emoji}.");
        });

        $discord->run();
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
