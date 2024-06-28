<?php

namespace App\Scheduler\Handler;

use App\Service\Historiser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class HistoriserHandler
{

    public function __invoke(Historiser $historiser,EntityManagerInterface $em){

    }
}