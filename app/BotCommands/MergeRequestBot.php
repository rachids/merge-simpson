<?php


namespace App\BotCommands;


use App\Service\GitlabService;
use Discord\Parts\Channel\Message;

class MergeRequestBot
{
    public function __invoke(Message $message)
    {
        $message->channel->broadcastTyping();

        try {
            $gitlabService = new GitlabService();
            $projects = explode(',', env('GITLAB_PROJECTS'));
            $mergeMessage = $gitlabService->getMergeMessage($projects);

            if (! empty($mergeMessage)) {
                $message->channel->sendMessage("Those are the merge requests awaiting for reviews:");
                $message->channel->sendMessage($mergeMessage);
            } else {
                $message->channel->sendMessage("There are no merge requests awaiting for reviews. :partying_face:");
            }

        } catch (\Exception $e) {
            $message->channel->sendMessage("I couldn't get the merge requests. :grimacing:");
        }
    }
}
