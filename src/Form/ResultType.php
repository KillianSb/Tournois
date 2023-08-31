<?php

namespace App\Form;

use App\Entity\Result;
use App\Entity\Team;
use App\Entity\Tournament;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResultType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('teamWinner', EntityType::class, [
                'class' => Team::class,
                'label' => 'Equipe gagnante par round du tournois',
                'multiple' => false,
                'autocomplete' => true,
            ])

            ->add('nbPartie', ChoiceType::class, [
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                ],
                'label' => 'Numéro du round : ',
                'label_attr' => [
                    'class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white'
                ],
                'attr' => [
                    'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500',
                    'type' => 'hidden',
                ]
            ])

            ->add('idTournament', EntityType::class, [
                'class' => Tournament::class,
                'multiple' => true,
                'attr' => [
                    'type' => 'hidden'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Result::class,

        ]);
    }
}
