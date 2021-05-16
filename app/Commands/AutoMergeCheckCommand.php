<?php

namespace App\Commands;

use App\Service\DiscordService;
use App\Service\GitlabService;
use Discord\Discord;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class AutoMergeCheckCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'bot:check-mr';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Check the merge requests automatically';

    /**
     * Execute the console command.
     *
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

            $channel = $discordService->getChannelByName('pull-request');
            $role = $discordService->getRoleByName('Peanut');

            $gitlabService = new GitlabService();

            $projects = explode(',', env('GITLAB_PROJECTS'));

            $channel->broadcastTyping();
            $message = $gitlabService->getMergeMessage($projects);

            if(! empty($message)) {
                $channel->sendMessage("Chers <@&{$role->id}>! Je crois que ces merge-requests ont besoin de votre amour :blue_heart: \n ");
                $channel->sendMessage($message)->then(function() use($discord) {
                    $discord->close();
                });
            } else {
                $discord->close();
            }
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
        $schedule->command(static::class)->dailyAt('17:30');
    }
}
