<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Sortie;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Image;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('password', PasswordType::class, [
                'required' => true,
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'new-password','class' => 'form-control','required' => 'required'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre mot de passe']),
                    new Length(['min' => 4,
                        'minMessage'=> 'Votre mot de passe doit avoir au minimum {{limit}} charactéres',
                        'max' => 4096,
                        ]),
                ]])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prenom',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('telephone', TextType::class, [
                'label'=>'Numéro de téléphone',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('pseudo', TextType::class, [
                'label' => 'Votre pseudo',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('image', FileType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
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
