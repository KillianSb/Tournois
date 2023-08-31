<?php

namespace App\Command;

use App\Repository\TournamentRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'check-state',
    description: 'Verfier le statut des tournois toutes les 15 minutes',
)]
class CheckStateCommand extends Command
{

    protected function __construct(
        TournamentRepository $tournamentRepository
    ) {
        parent::__construct();
        $this->tournamentRepository = $tournamentRepository;
    }

    private TournamentRepository $tournamentRepository;
    protected function configure(): void
    {
        $this
            ->addArgument('time', InputArgument::OPTIONAL, 'gap between each check')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
            $this->checkState();

        return Command::SUCCESS;
    }

    protected function checkState():void{

        $tournaments = $this->tournamentRepository->findAllTournament();
        foreach ($tournaments as $tournament){
            if ($tournament->getDateBeginTournament() > new \DateTime('now' && $tournament->getDateEndTournament() > new \DateTime('now'))){
                $tournament->setStatut("En cours");
            } elseif ($tournament->getDateEndTournament() < new \DateTime('now')){
                $tournament->setStatut("Terminé");
            } elseif (count($tournament->getTeam()) == $tournament->getNbTeamMax()){
                $tournament->setStatut("Cloturée");
            }
        }
    }
}
