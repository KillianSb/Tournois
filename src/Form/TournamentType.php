<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\Tournament;
use App\Entity\VideoGame;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
            ->add('dateEndTournament', null,
                ['label' => 'Date de fin du tournois']
            )
            ->add('dateLimitRegistration', null,
                ['label' => 'Date limite d\'inscription du tournois']
            )
            ->add('nbTeamMax', null, [
                'label' => 'Nombre d\'équipe maximum',
                'data' => '2',
                ]
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
            ->add('videogame', EntityType::class, [
                'class' => Videogame::class,
                'label' => 'Jeu du tournois'
                ]
            )
            ->add('location', null,
                ['label' => 'Lieu du tournois']
            )
            ->add('team', EntityType::class, [
                'class' => Team::class,
                'label' => 'Equipe du tournois',
                'multiple' => true,
                'autocomplete' => true
                ]
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
