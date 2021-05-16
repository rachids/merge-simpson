<?php


namespace App\Service;

use Illuminate\Support\Facades\Http;

/**
 * Class TeleportApiService
 * @package App\Service
 *
 * Communique avec l'API Teleport pour obtenir des informations sur une ville donnée.
 */
class TeleportApiService
{

    public function getLatitudeLongitudeForCity(string $city): array
    {
        $geoName = $this->getGeoName($city);

        $infos = $this->getCityInfo($geoName);

        return $infos["location"]["latlon"];
    }

    private function getGeoName(string $city): string
    {
        $response = Http::baseUrl("https://api.teleport.org/api/")->get("cities/?search={$city}");

        $result = json_decode($response->body(), associative: true);

        $rawData = $result['_embedded']['city:search-results'][0]['_links']['city:item']['href'];

        return str_replace(
            "/",
            "",
            explode(':', $rawData)[2]
        );
    }

    private function getCityInfo(string $geoName): array
    {
        $response = Http::baseUrl("https://api.teleport.org/api/")->get("cities/geonameid:{$geoName}/");

        return json_decode($response->body(), associative: true);
    }
}
