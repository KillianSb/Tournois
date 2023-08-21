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
    private ?int $nbPoint = null;

    #[ORM\Column]
    private ?int $winnerScore = null;

    #[ORM\Column]
    private ?int $looserScore = null;

    #[ORM\OneToOne(mappedBy: 'result', cascade: ['persist', 'remove'])]
    private ?Game $game = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbPoint(): ?int
    {
        return $this->nbPoint;
    }

    public function setNbPoint(int $nbPoint): static
    {
        $this->nbPoint = $nbPoint;

        return $this;
    }

    public function getWinnerScore(): ?int
    {
        return $this->winnerScore;
    }

    public function setWinnerScore(int $winnerScore): static
    {
        $this->winnerScore = $winnerScore;

        return $this;
    }

    public function getLooserScore(): ?int
    {
        return $this->looserScore;
    }

    public function setLooserScore(int $looserScore): static
    {
        $this->looserScore = $looserScore;

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
}
