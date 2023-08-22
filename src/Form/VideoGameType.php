<?php

namespace App\Form;

use App\Entity\VideoGame;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoGameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
            'name',
            TextType::class , [
                    'label' => 'Nom du jeu ',
                    'required' => true,
                ]
            )
            ->add(
                'isOnline',
                CheckboxType::class, [
                    'label' => 'Jeu en ligne ',
                    'required' => true
                ])
            ->add(
                'nbTeam',
                NumberType::class, [
                    'label' => 'Nombre d\'equipes par partie ',
                    'required' => true,
                    'attr' => [
                        'min' => 1,
                        'default' => 2,
                    ]
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VideoGame::class,
        ]);
    }
}