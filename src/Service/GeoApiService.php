<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeoApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getAllCities(): array
    {
        $response = $this->client->request('GET', 'https://geo.api.gouv.fr/communes?fields=nom&format=json');

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Erreur lors de la récupération des villes.');
        }

        $cities = $response->toArray();
        usort($cities, function ($a, $b) {
            return strcmp($a['nom'], $b['nom']);
        });

        return $cities;
    }




    public function getLocationsByCity(string $cityName): array
    {
        $response = $this->client->request('GET', 'https://geo.api.gouv.fr/communes/' . urlencode($cityName) . '/lieux?format=json');

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Erreur lors de la récupération des lieux pour la ville ' . $cityName);
        }

        $locations = $response->toArray();

        return [
            'rue' => $locations['rue'] ?? null,
            'codePostal' => $locations['codePostal'] ?? null,
            'latitude' => $locations['centre']['coordinates'][1] ?? null,
            'longitude' => $locations['centre']['coordinates'][0] ?? null,
        ];
    }

}
