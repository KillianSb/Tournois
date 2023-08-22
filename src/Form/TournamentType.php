<?php

namespace App\Form;

use App\Entity\Tournament;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TournamentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null,
            ['label' => 'Nom du tournois']
            )
            ->add('rules', null,
                ['label' => 'Regles du tournois']
            )
            ->add('dateBeginTournament', null,
                ['label' => 'Date de debut du tournois']
            )
            ->add('dateCreation', null,
                ['label' => 'Date de creation du tournois']
            )
            ->add('dateEndTournament', null,
                ['label' => 'Date de fin du tournois']
            )
            ->add('dateLimitRegistration', null,
                ['label' => 'Date limite d\'inscription du tournois']
            )
            ->add('nbTeamMax', null,
                ['label' => 'Nombre de team maximum du tournois']
            )
            ->add('tournamentInfo', null,
                ['label' => 'Infos du tournois']
            )
            ->add('entryPrice', null,
                ['label' => 'Prix d\'entrée du tournois']
            )
            ->add('reward', null,
                ['label' => 'Recompense du tournois']
            )
            ->add('videogame', null,
                ['label' => 'Jeu du tournois']
            )
            ->add('location', null,
                ['label' => 'Lieu du tournois']
            )
            ->add('status', null,
                ['label' => 'Statut du tournois']
            )
            ->add('team', null,
                ['label' => 'Equipe du tournois']
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournament::class,
        ]);
    }
}
