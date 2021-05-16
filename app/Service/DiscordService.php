<?php


namespace App\Service;

use Discord\Parts\Channel\Channel;
use Discord\Parts\Guild\Guild;
use Discord\Parts\Guild\Role;

/**
 * Class DiscordService
 * @package App\Service
 *
 * Wrapper autour de DiscordPHP pour obtenir le nom d'un channel, d'un rôle.
 */
class DiscordService
{

    public function __construct(
        private Guild $guild
    )
    {
    }

    public function getChannelByName(string $name): Channel
    {
        return $this->guild->channels->get('name', $name);
    }

    public function getRoleByName(string $name): Role
    {
        return  $this->guild->roles->get('name', $name);
    }

}
