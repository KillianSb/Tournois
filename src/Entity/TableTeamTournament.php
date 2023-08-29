<?php

namespace App\Entity;

use App\Repository\TableTeamTournamentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TableTeamTournamentRepository::class)]
class TableTeamTournament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $ShuffleTableTeam = [];

    #[ORM\OneToOne(inversedBy: 'tableTeamTournament', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tournament $tournaments = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShuffleTableTeam(): array
    {
        return $this->ShuffleTableTeam;
    }

    public function setShuffleTableTeam(array $ShuffleTableTeam): static
    {
        $this->ShuffleTableTeam = $ShuffleTableTeam;

        return $this;
    }

    public function getTournaments(): ?Tournament
    {
        return $this->tournaments;
    }

    public function setTournaments(Tournament $tournaments): static
    {
        $this->tournaments = $tournaments;

        return $this;
    }
}
