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

            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisir le campus',
                'attr' => ['class' => 'form-select'],
                'query_builder' => function ( CampusRepository $campusRepository) {
                    return $campusRepository
                        ->createQueryBuilder('c')
                        ->addOrderBy('c.nom');
                }

            ])
            ->add('nom', TextType::class,[
                'label' => 'Le nom de la sortie contient : ',
                'label_attr' => ['class' => 'col-form-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Entre',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control', 'type' => 'date'],
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'et',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control', 'type' => 'date'],
            ])

            ->add('organisateur', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur/trice',
                'required' => false,
                'attr' => ['class' => 'form-check-input'], // Ajoutez une classe pour le style Bootstrap
            ])

            ->add('inscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'required' => false,
                'attr' => ['class' => 'form-check-input'], // Ajoutez une classe pour le style Bootstrap
            ])

            ->add('nonInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
                'required' => false,
                'attr' => ['class' => 'form-check-input'], // Ajoutez une classe pour le style Bootstrap
            ])

            ->add('sortiePasse', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
                'attr' => ['class' => 'form-check-input'], // Ajoutez une classe pour le style Bootstrap
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,

        ]);
    }
}
