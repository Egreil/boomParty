<?php

namespace App\Service;

use App\Entity\Lieu;
use Doctrine\ORM\EntityManagerInterface;

class LieuService


{
    private EntityManagerInterface $entityManager;
public function __construct(EntityManagerInterface $entityManager)
{
    $this->entityManager = $entityManager;
}

public function createLieu(string $nom, string $rue, float $lat, float $lng) : Lieu
{
    $lieu = new Lieu();
    $lieu->setNom($nom);
    $lieu->setRue($rue);
    $lieu->setLatitude($lat);
    $lieu->setLongitude($lng);

    $this->entityManager->persist($lieu);
    $this->entityManager->flush();

    return $lieu;
}

}