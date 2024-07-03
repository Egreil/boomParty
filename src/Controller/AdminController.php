<?php

namespace App\Controller;

use App\Service\InscriptionMailingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('creerCompte', name: 'creerCompte')]
    public function creerCompte(): Response
    {
        if($file){

        }
        InscriptionMailingService();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
