<?php


namespace App\Service;

use Gitlab\Client;

/**
 * Class GitlabService
 * @package App\Service
 *
 * Communique avec l'API Gitlab pour aller récupérer les merge requests des différents projets.
 *
 * Variable d'environnement:
 * GITLAB_TOKEN : Personal Access Token de Gitlab.
 */
class GitlabService
{
    private Client $client;

    public function __construct(
    )
    {
        $token = env('GITLAB_TOKEN');
        $this->client = new Client();
        $this->client->authenticate($token, Client::AUTH_HTTP_TOKEN);
    }

    public function getMergeMessage(array $projects): string
    {
        $projectMessage = [];

        foreach ($projects as $project) {

            $mergeRequests = $this->getMergeRequests($project);

            if( !empty($mergeRequests)) {

                foreach ($mergeRequests as $mergeRequest) {
                    if($mergeRequest['upvotes'] < 2 && !$mergeRequest['work_in_progress']) {
                        $projectMessage[$project][] =  $this->buildMergeRequestMessage($mergeRequest);
                    }
                }
            }
        }

        return $this->buildMergeMessage($projectMessage);
    }

    private function buildMergeMessage(array $projectsMessage): string
    {
        $finalMessage = "";

        foreach ($projectsMessage as $projectName => $messages) {
            if( !empty($messages)) {
                $finalMessage .= "\n :file_folder: **{$projectName}**";

                foreach ($messages as $message) {
                    $finalMessage .= $message;
                }
            }
        }

        return $finalMessage;
    }

    private function buildMergeRequestMessage($mergeRequest): string
    {
        $title = $mergeRequest['title'];
        $author = $mergeRequest['author'];
        $upvotes = $mergeRequest['upvotes'];
        $url = $mergeRequest['web_url'];

        return "\n > **{$upvotes}** :thumbsup: `{$title}`
         > {$url} - _(par {$author['name']})_ \n";
    }

    private function getMergeRequests(string $projectName = '')
    {
        // TODO: Filtrer par WIP = no et upvotes < 2 pour retirer le if dans getMergeMessage() ligne 31.

        return $this->client->mergeRequests()->all($projectName, [
            'state' => 'opened',
        ]);
    }
}
