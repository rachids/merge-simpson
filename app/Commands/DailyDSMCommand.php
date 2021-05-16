<?php

namespace App\Commands;

use App\Service\DiscordService;
use Discord\Discord;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class DailyDSMCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'bot:dsm';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Annonce le DSM.';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Discord\Exceptions\IntentException
     */
    public function handle()
    {
        $config = [
            'token' => env('DISCORD_TOKEN'),
        ];

        $discord = new Discord($config);

        $discord->on('ready', function (Discord $discord) {

            $guild = $discord->guilds->get('name', env('DISCORD_SERVER_NAME'));

            $discordService = new DiscordService($guild);

            $channel = $discordService->getChannelByName('work-chat');

            $roleDev = $discordService->getRoleByName('Dev');
            $roleUx = $discordService->getRoleByName('UI/UX');

            $channel->sendMessage(":folksy: Doh! C'est l'heure du DSM! <@&{$roleDev->id}> <@&{$roleUx->id}>")
                ->then(function() use($discord) {
                    $discord->close();
                });
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
        $schedule->command(static::class)->dailyAt('9:30');
    }
}
