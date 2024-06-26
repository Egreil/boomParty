<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles')
            ->add('password')
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('pseudo')
            ->add('dateCreation', null, [
                'widget' => 'single_text',
            ])
            ->add('dateModification', null, [
                'widget' => 'single_text',
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'constraints' => [
                    new Image(
                        [
                            'maxSize' => '10000k',
                            'mimeTypesMessage' => 'Image format not allowed !',
                            'maxSizeMessage' => 'The file is too large !'
                        ]
                    )
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
            'required'=>false
        ]);
    }
}
