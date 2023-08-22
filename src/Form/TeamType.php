<?php

namespace App\Form;

use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            //TODO : En attente de creation du formulaire pour les utilisateurs
            ->add('user', null, [
                "label" => "Inscription des joueurs",
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
