<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\Tournament;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;


class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('name', null, [
                'label' => "Nom de l'équipe",
                'label_attr' => [
                    'class' => 'block mb-2 text-lg font-medium text-gray-900 dark:text-white'
                ],
                'attr' => [
                    'class' => 'inputForm block mb-2 text-sm font-medium text-gray-900 dark:text-white border-b-2'
                ],
            ])

            ->add('nbPlayer', HiddenType::class, [
                'mapped' => false,
            ])

            ->add('bio', null, [
                "label" => "Ajoute une bio à ton équipe",
                'label_attr' => [
                    'class' => 'block mb-2 text-lg font-medium text-gray-900 dark:text-white'
                ],
                'attr' => [
                    'class' => 'inputForm block mb-2 text-sm font-medium text-gray-900 dark:text-white border-b-2'
                ]
            ])

            ->add('user', EntityType::class, [
                "class" => User::class,
                'label' => "Pseudo",
                'multiple' => true,
                'autocomplete' => true,
                'label_attr' => [
                    'class' => 'block mb-2 text-lg font-medium text-gray-900 dark:text-white'
                ],
                'attr' => [
                    'class' => 'inputForm block mb-2 text-sm font-medium text-gray-900 dark:text-white'
                ]
            ])

            ->add('picture', null, [
                "label" => "Image Equipe",
                'label_attr' => [
                    'class' => 'block mb-2 text-lg font-medium text-gray-900 dark:text-white'
                ],
                'attr' => [
                    'class' => 'inputForm block mb-2 text-sm font-medium text-gray-900 dark:text-white'
                ]
            ])

            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $team = $event->getData();

                // Get selected users and update nbPlayer
                $selectedUsers = $form->get('user')->getData();
                $nbPlayers = count($selectedUsers);
                $team->setNbPlayer($nbPlayers);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
