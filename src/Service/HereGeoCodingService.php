<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class HereGeoCodingService
{
    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    public function getCoordinatesForPlace(string $place): ?array
    {
        $url = sprintf(
            'https://geocode.search.hereapi.com/v1/geocode?q=%s&apiKey=%s',
            urlencode($place),
            $this->apiKey
        );

        try {
            $response = $this->client->request('GET', $url);
            $data = $response->toArray();

            if (isset($data['items'][0]['position'])) {
                return $data['items'][0]['position'];
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }
}
