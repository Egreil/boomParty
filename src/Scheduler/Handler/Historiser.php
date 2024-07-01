<?php

namespace App\Scheduler\Handler;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Service\ActualiserEtatService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;


/**
 * Service qui est invoqué par le scheduler pour Historiser les sorties.
 */
#[AsCronTask('1 0 * * *',timezone:"EUROPE/PARIS")]
final class Historiser
{
    public function __construct (private EntityManagerInterface $entityManager) {

    }
    public function __invoke():string {
        //Appel de la fonction Historiser de ActualiserEtatService
        (new ActualiserEtatService($this->entityManager))->historiser();
        return "ok";
    }
}