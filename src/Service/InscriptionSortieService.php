<?php

namespace App\Service;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class InscriptionSortieService
{


    //A realiser si
    /**
     * @param Sortie $sortie
     * @param Participant $participant
     * @param SortieRepository $sortieRepository
     * @return void
     *
     * Le participant n'est pas déjà inscrit
     * La date d'inscription n'est pas dépassée,
     * Le nombre d'inscription max n'est pas atteint,
     * L'état de la sortie est ouvert
     */
    public function inscrire(
        Sortie $sortie,
        Participant $participant,
        SortieRepository $sortieRepository)
    {
        // faire une jointure?
        if(//Le participant n'est pas déjà inscrit
            !$sortie->getParticipants()->contains($participant)
        &&
            //La date d'inscription n'est pas dépassée,
            \DateTime::class->getTimestamp()<$sortie->getDateLimiteInscription()->getTimestamp()
        &&
            //Le nombre d'inscription max n'est pas atteint,
            $sortie->getParticipants()->count()<$sortie->getNbInscriptionMax()
        &&
            //L'état de la sortie est ouvert
            $sortie->getEtat()=="ouverte"

        ){
            $sortie->addParticipant($participant);
        }

    }

    /**
     * @param Sortie $sortie
     * @param Participant $participant
     * @return void
     *
     * Un participant peut se désister si il est présent sur la sortie
     * Si la sortie n'a pas débuté.
     *
     */
    public function desinscrire(
        Sortie $sortie,
        Participant $participant,
    ){
        $statutsImpossibles=['Activité en cours','Passée'];
        if( //Un participant peut se désister si il est présent sur la sortie
            $sortie->getParticipants()->contains($participant)
        &&
            //Si la sortie n'a pas débuté
            $bool=function($sortie,$statusImpossibles){
                foreach($statusImpossibles as $status){
                    if($status==$sortie->getEtat()){
                        return true;
                        }
                }
                return false;
            }
        ){
            $sortie->removeParticipant($participant);
        }
    }

}