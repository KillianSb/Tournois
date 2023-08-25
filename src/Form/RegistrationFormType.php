<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse mail ',
                'attr' => [
                    'class' => 'input input-bordered',
                    'placeholder' => 'Pierre@gmail.com',
                ]
            ])
            ->add('username',TextType::class, [
                'label' => 'Pseudo ',
                'attr' => [
                    'class' => 'input input-bordered',
                    'placeholder' => 'PierreP',
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom ',
                'attr' => [
                    'class' => 'input input-bordered',
                    'placeholder' => 'Pierre',
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom ',
                'attr' => [
                    'class' => 'input input-bordered',
                    'placeholder' => 'Pierre',
                ]
            ])
            ->add('picture', TextType::class, [
                'label' => 'Avatar ',
                'attr' => [
                    'class' => 'input input-bordered',
                    'hidden' => true
                ]
            ])
            ->add('phone_number', TextType::class, [
                'label' => 'Numéro de téléphone ',
                'attr' => [
                    'class' => 'input input-bordered',
                    'placeholder' => '0422521010',
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe ',
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'input input-bordered',
                    'placeholder' => '****',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit être au moins {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
