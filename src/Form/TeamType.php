<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null,
                ['label' => "Nom de l'équipe",
            ])
            ->add('nbPlayer', null, [
                "label" => "Nombre de joueurs",
            ])
            ->add('bio', null, [
                "label" => "Ajoute une bio à ton équipe",
            ])
            // TODO : En attente de creation du formulaire pour les tournois
            ->add('tournaments', null, [
                "label" => "Choix du tournois",
            ])
            ->add('user', EntityType::class, [
                "class" => User::class,
                'placeholder' => 'Nom du participant',
                'multiple' => true,
                'autocomplete' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
