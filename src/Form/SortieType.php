<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('id')
            ->add('nom', null,[
                'label'=> 'Nom de la sortie : '
            ] )
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie :',
                'widget' => 'single_text',
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée :',
                'attr' => ['min' => 0]
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => 'Date limite d\'inscription :',
                'widget' => 'single_text',
            ])
            ->add('nbInscriptionMax', IntegerType::class, [
                'label' => 'Nombre de place :',
                'attr' => ['min' => 0]
            ])
            ->add('infosSortie', null, [
                'label' => 'Description et infos :',
                'attr' => ['class' => 'form-control']
            ])


            ->add('campus', EntityType::class, [
                'class' => Campus::class,
//                'mapped' => true,
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-control'],
                'query_builder' => function (CampusRepository $campusRepository) {
                    return $campusRepository->createQueryBuilder('c')
                        ->orderBy('c.nom', 'ASC');
                }
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
//                'mapped' => false,
                'choice_label' => 'nom',
                'label' => 'Lieu :',
                'attr' => ['class' => 'form-control'],
                'query_builder' => function (LieuRepository $lieuRepository) {
                    return $lieuRepository->createQueryBuilder('l')
                        ->orderBy('l.nom', 'ASC');
                }, 'required' => true,
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'mapped'=>false,
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-control'],
                'query_builder' => function (VilleRepository $villeRepository) {
                    return $villeRepository->createQueryBuilder('v')
                        ->orderBy('v.nom', 'ASC');
                }
            ])


//            ->add('enregistrer', SubmitType::class, [
//                'label' => 'Enregistrer',
//                'attr' => ['class' => 'btn btn-primary mt-3']
//            ])
//            ->add('publier', SubmitType::class, [
//                'label' => 'Publier la sortie',
//                'attr' => ['class' => 'btn btn-success mt-3']
//            ])
//            ->add('supprimer', SubmitType::class, [
//                'label' => 'Supprimer la sortie',
//                'attr' => ['class' => 'btn btn-success mt-3']
//            ])
//            ->add('annuler', SubmitType::class, [
//                'label' => 'Annuler',
//                'attr' => ['class' => 'btn btn-danger mt-3']
//            ])
//            ->add('action', HiddenType::class, [
//                'mapped' => false,
//            ])

//            ->add('etat', EntityType::class, [
//                'class' => Etat::class,
//                'mapped' => false,
//                'choice_label'=>'nom',
//                'attr' => ['class' => 'form-control'],
//            ])
            ->add('lieuRue', null, [
                'mapped' => false,
                'label'=>'Rue :',
                'required' => false,
            ])
            ->add('lieuLatitude', null, [
                'mapped' => false,
                'label'=>'Latitude :',
            ])
            ->add('lieuLongitude', null, [
                'mapped' => false,
                'label'=>'Longitude :',
            ])
            ->add('villeCodePostal', null, [
                'mapped' => false,
                'label'=>'Code postal :',
            ])
//




//
//            ->add('participant', EntityType::class, [
//                'class' => Participant::class,
//                'mapped' => false,
//                'choice_label' => 'pseudo',
//                'query_builder' => function (ParticipantRepository $participantRepository) {
//                    return $participantRepository
//                        ->createQueryBuilder('p')
//                        ->addOrderBy('p.pseudo', 'ASC')
//                        ->addOrderBy('p.nom', 'ASC');
//                }
//            ])

            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'required' => false,
        ]);
    }
}

