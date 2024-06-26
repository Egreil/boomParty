<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\ParticipantRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idSortie')
            ->add('nom')
            ->add('dateHeureDebut', null, [
                'widget' => 'single_text',
            ])
            ->add('duree')
            ->add('dateLimiteInscription', null, [
                'widget' => 'single_text',
            ])
            ->add('nbInscriptionMax')
            ->add('infosSortie')
            ->add('etat')
            ->add('button', SubmitType::class,[
                'label' => 'Ajouter la sortie'
            ])
            ->add('participant', EntityType::class, [
                'class' => Participant::class,
                'choice_label' => 'pseudo',
                'query_builder' => function (ParticipantRepository $participantRepository) {
                    return $participantRepository
                        ->createQueryBuilder('p')
                        ->addOrderBy('p.pseudo', 'ASC')
                        ->addOrderBy('p.nom', 'ASC');
                }
                ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'required'=>false
        ]);
    }
}
