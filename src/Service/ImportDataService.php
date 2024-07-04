<?php

namespace App\Service;

use App\Entity\Lieu;
use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;

class ImportDataService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Importe les données des villes depuis une API externe dans la base de données.
     */
public function importCitiesFromGeoApi()
{
    $httpClient = HttpClient::create();
    $response = $httpClient->request('GET', 'https://geo.api.gouv.fr/communes?fields=nom&format=json');
    $cities = $response->toArray();

    foreach ($cities as $city) {
        $ville = new Ville();
        $ville->setNom($city['nom']);
        // Vous pouvez ajouter d'autres champs comme le code postal ici si nécessaire
        $this->entityManager->persist($ville);
    }

    $this->entityManager->flush();
}
}
