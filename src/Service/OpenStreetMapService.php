<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;


class OpenStreetMapService
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getVillesDeFrance(): array
    {
        // Utiliser une requête pour obtenir les villes de France
        $response = $this->client->request(
            'GET',
            'https://nominatim.openstreetmap.org/search', [
                'query' => [
                    'country' => 'France',
                    'city' => '',
                    'format' => 'json',
                    'addressdetails' => 1,
                    'countrycodes' => 'FR',
                    'limit' => 1000 // Ajuster la limite si nécessaire
                ]
            ]
        );

        return $response->toArray();
    }

    public function getRuesPrincipales(string $ville): array
    {
        $response = $this->httpClient->request(
            'GET',
            'https://nominatim.openstreetmap.org/search', [
            'query' =>[
                'city' => $ville,
                'format' => 'json',
                'addressDetails'=>1,
                'countrycodes'=>'FR',
                'limit'=>100
            ]
        ]);
        $lieux = $response->toArray();
        $ruesPrincipales = [];

        foreach ($lieux as $lieu) {
            if (isset($lieu['formatted'])) {}
        }

        return $response->toArray();
    }

}