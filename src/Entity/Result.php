<?php

namespace App\Entity;

use App\Repository\ResultRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResultRepository::class)]
class Result
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $winnerTeam = null;

    #[ORM\OneToOne(mappedBy: 'result', cascade: ['persist', 'remove'])]
    private ?Game $game = null;

    #[ORM\Column]
    private ?int $nbPartie = null;

    public function __toString(): string
    {
        return $this->winnerTeam;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWinnerTeam(): ?string
    {
        return $this->winnerTeam;
    }

    public function setWinnerTeam(string $winnerTeam): static
    {
        $this->winnerTeam = $winnerTeam;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(Game $game): static
    {
        // set the owning side of the relation if necessary
        if ($game->getResult() !== $this) {
            $game->setResult($this);
        }

        $this->game = $game;

        return $this;
    }

    public function getNbPartie(): ?int
    {
        return $this->nbPartie;
    }

    public function setNbPartie(int $nbPartie): static
    {
        $this->nbPartie = $nbPartie;

        return $this;
    }
}
