# Merge Simpson

Simple Discord bot made with [Laravel Zero](https://laravel-zero.com/) and [DiscordPHP](https://github.com/discord-php/DiscordPHP).

## Documentation

- Clone this project
- `cp .env.example .env`
- Fill the `DISCORD_TOKEN` with your token.
- Run `composer install`

For more, you can read the [Laravel Zero](https://laravel-zero.com/) and 
[DiscordPHP](https://github.com/discord-php/DiscordPHP) documentations.

## Available commands

**!mr**
This command will check for opened merge requests with less than 2 upvotes. You need to edit the `DiscordCommand.php` in
order to set the channel in which you want the bot to put its answer. You also need to set your `GITLAB_TOKEN`.

**!meteo `{city_name}`**
This command will fetch latitude and longitude of the city passed from the [Teleport API](https://developers.teleport.org/api/) 
and then use those to fetch weather data from [7Timer! API](http://www.7timer.info/doc.php). _(Those two services don't require authentication)_.

## License

Merge Simpson is an open-source software licensed under the MIT license.
