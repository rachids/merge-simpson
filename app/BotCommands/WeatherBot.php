<?php


namespace App\BotCommands;


use App\Service\WeatherService;
use Discord\Parts\Channel\Message;

class WeatherBot
{
    public function __invoke(Message $message, array $parameters)
    {
        $message->channel->broadcastTyping();

        $city = $parameters[0] ?? 'Québec';

        try {
            $weather = WeatherService::getWeatherForCity($city)[0];
            $emoji = match ($weather['prec_type']) {
                'rain' => '🌧️',
                'snow' => '🌨️',
                default => '☀️'
            };

            $message->channel->sendMessage("In {$city}, the temperature is **{$weather['temp2m']} ℃** and the sky is {$emoji}.");
        } catch (\Exception $e) {
            $message->channel->sendMessage("I couldn't get the weather properly. :grimacing:");
        }
    }
}
