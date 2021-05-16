<?php


namespace App\Service;


use Illuminate\Support\Facades\Http;

/**
 * Class WeatherService
 * @package App\Service
 *
 * Communique avec l'API 7Timer! pour obtenir des informations météorologiques à partir d'une latitude / longitude
 * (information obtenue avec Teleport, voir TeleportApiService)
 */
class WeatherService
{
    public static function getWeatherForCity(string $city = 'Québec'): array
    {
        $teleportService = new TeleportApiService();

        $latlon = $teleportService->getLatitudeLongitudeForCity($city);

        $url = "http://www.7timer.info/bin/api.pl?lon={$latlon['longitude']}&lat={$latlon['latitude']}&ac=0&unit=metric&output=json&tzshift=0&product=astro";

        $response = Http::get($url);

        return json_decode($response->body(), associative: true)['dataseries'];
    }

}
