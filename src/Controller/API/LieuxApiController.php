<?php

namespace App\Controller\API;

use App\Entity\Lieu;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use App\Service\GeoNamesService;
use App\Service\OpenStreetMapService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function PHPUnit\Framework\throwException;

#[Route('/api/lieux', name: 'api_')]
class LieuxApiController extends AbstractController

{
    private $httpClient;

    // la premiere méthode que j'ai laissée tomber (creation d'API interne , why ? car je dois l'alimenter moi meme and I don't feel like :)
    //fonction qui  va nous renvoyer la liste des lieux d'une ville séléctionner
    /**
     * @param $httpClient
     */
    public function __construct(httpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('', name: 'all', methods: ['GET'])]
    public function getAllPlaces(
        LieuRepository $lieuRepository,
    ) : Response
    {
        $lieux = $lieuRepository->findAll();
        return $this->json($lieux, Response::HTTP_OK, [] , ['groups' => 'lieu']);
    }

    // je cree une fonction pour appeler les coord. d'un lieu sélectionné
    #[Route('/{id}', name:'one', methods: ['GET'])]
    public function getOnePlace(
        int $id,
        LieuRepository $lieuRepository,
        SerializerInterface $serializer,
    ) : Response
    {
        $lieu = $lieuRepository->find($id); // on va la transformer en json
//        $json=$serializer->serialize($lieu, 'json', ['groups'=>'lieu']);
//        dd($json);
        return $this->json($lieu, Response::HTTP_OK, [], ['groups' => 'lieu']);

    }


    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) : Response
    {
//          ajouter un lieu en base en format json
        // extraire le corps de la requete http
        $data=$request->getContent();
        //recuperation d objet
        //$data=json_decode($data);
        //recuperer une instance de lieu
        $lieu = $serializer->deserialize($data, Lieu::class, 'json');
        $errors=$validator->validate($lieu);

        if(count($errors)>0){
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }
        //dd($errors);
        $entityManager->persist($lieu);
        $entityManager->flush();
        //dd($lieu);

        try {
            $response = $this->httpClient->request('GET', 'https://geo.api.gouv.fr/communes?fields=nom&format=json');

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Impossible de récupérer les villes.');
            }

            $villes = $response->toArray();
        } catch (\Exception $e) {
            $villes=[];
            $this->addFlash('warning', 'Erreur lors de la récupération des villes : '.$e->getMessage());
        }

        return $this->render('sortie/create.html.twig', [
            'lieu' => $lieu,
            'villes' => $villes,
        ]);

//        dd($villes);

        // retourner l objet cree
//        return $this->json(
//            [
//                'lieu' => $lieu,
//                'villes' => $villes,
//
//            ],
//            Response::HTTP_CREATED,
//            [
//                "Location" => $this->generateUrl(
//                    'api_one',
//                    ['id' => $lieu->getId()],
//                    UrlGeneratorInterface::ABSOLUTE_URL
//                )
//            ], //le header ; url pour acceder à la source crée
//            ['groups' => 'lieu']);

    }

    #[Route('/{id}', name: 'update', methods: ['PUT' , 'PATCH'])]
    public function update(
        int $id,
        Request $request,
        LieuRepository $lieuRepository,
        EntityManagerInterface $entityManager,

    ) : Response
    {
        $json=$request->getContent();
        $data=json_decode($json,true);

        $lieu=$lieuRepository->find($id);
 // je peux ajouter mes sets

        $entityManager->persist($lieu);
        $entityManager->flush();

        return $this->json($lieu, Response::HTTP_OK, [], ['groups' => 'lieu']);



//dd("data");

    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete( $id) : Response
    {


    }

}

