<?php

namespace App\Controller;

use App\Service\LieuService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/lieu', name:'create')]
class LieuController extends AbstractController
{
    #[Route('/create', name: 'lieu_create')]
    public function create( LieuService $lieuService): Response
    {

    }
}
