<?php

namespace App\Service;
use GuzzleHttp\Client;


class GeoNamesService
{
    private $client;
    private $username;

    public function __construct(string $geonamesUsername)
    {
        $this->client = new Client([
            'base_uri' => 'https://api.geonames.org/',
        ]);
        $geonamesUsername= "meriembhb";
        $this->username = $geonamesUsername;
    }

    public function getVillesDeFrance(): array
    {
        $response = $this->client->request('GET', 'searchJSON', [
            'query' => [
                'country' => 'FR',
                'featureClass' => 'P',
                'maxRows' => 1000,
                'username' => $this->username
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['geonames'] ?? [];
    }

    public function getRuesPrincipales(string $ville): array
    {
        // Implémentation pour obtenir les rues principales d'une ville donnée
        // Cette partie peut nécessiter une API différente ou des paramètres spécifiques
        // GeoNames ne fournit pas directement les rues, il faut utiliser Nominatim pour cela

        // Requête vers Nominatim pour obtenir les rues principales d'une ville
        $response = $this->client->request('GET', 'https://nominatim.openstreetmap.org/search', [
            'query' => [
                'city' => $ville,
                'format' => 'json',
                'addressdetails' => 1,
                'countrycodes' => 'FR',
                'limit' => 100
            ]
        ]);

        $lieux = json_decode($response->getBody()->getContents(), true);
        $ruesPrincipales = [];

        foreach ($lieux as $lieu) {
            if (isset($lieu['address']['road'])) {
                $ruesPrincipales[] = $lieu['address']['road'];
            }
        }

        return array_unique($ruesPrincipales); // Éviter les doublons
    }
}