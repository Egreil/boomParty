<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\CampusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisissez un campus',
                'query_builder' => function ( CampusRepository $campusRepository) {
                    return $campusRepository
                        ->createQueryBuilder('c')
                        ->addOrderBy('c.nom');
                }

            ])
            ->add('nom', TextType::class,[
                'label' => 'Le nom de la sortie contient : '
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'html5' => false,
                'mapped' => false,
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'html5' => false,
                'mapped' => false,
            ])

            ->add('organisateur', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur/trice',
                'required' => false,
                'attr' => ['class' => 'form-check-input'], // Ajoutez une classe pour le style Bootstrap
            ])

            ->add('inscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'form-check-input'], // Ajoutez une classe pour le style Bootstrap
            ])

            ->add('nonInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'form-check-input'], // Ajoutez une classe pour le style Bootstrap
            ])

            ->add('sortiePasse', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'form-check-input'], // Ajoutez une classe pour le style Bootstrap
            ])
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
