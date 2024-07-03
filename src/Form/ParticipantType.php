<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\CampusRepository;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
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
use Symfony\Component\Validator\Constraints\Regex;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => ['class' => 'form-control'],
                ],
                'second_options' => [
                    'label' => 'Confirmer mot de passe',
                    'attr' => ['class' => 'form-control'],
                ],
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'mapped' => false,
                'constraints' => [
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Votre mot de passe doit avoir au minimum {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9]+$/',
                        'htmlPattern' => '[a-zA-Z0-9]+$',
                        'message' => 'Votre mot de passe ne doit contenir que des lettres et des chiffres.',
                    ]),
                ],
            ])
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
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-control'],
                'query_builder' => function ( CampusRepository $campusRepository) {
                    return $campusRepository
                        ->createQueryBuilder('c')
                        ->addOrderBy('c.nom');
                }

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
