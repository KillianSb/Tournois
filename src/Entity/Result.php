<?php

namespace App\Entity;

use App\Repository\ResultRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ResultRepository::class)]
class Result
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(mappedBy: 'result', cascade: ['persist', 'remove'])]
    private ?Game $game = null;

    #[ORM\Column]
    #[Groups(['teamWinner'])]
    private ?int $nbPartie = null;

    #[ORM\ManyToOne(inversedBy: 'results')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['teamWinner'])]
    private ?Team $teamWinner = null;

    #[ORM\Column]
    private ?int $idTournament = null;

    public function __toString(): string
    {
        return $this->teamWinner;
    }
    public function getId(): ?int
    {
        return $this->id;
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

    public function getTeamWinner(): ?Team
    {
        return $this->teamWinner;
    }

    public function setTeamWinner(?Team $teamWinner): static
    {
        $this->teamWinner = $teamWinner;

        return $this;
    }

    public function __serialize(): array
    {
        // TODO: Implement __serialize() method.
        return [
            'id' => $this->getId(),
            'nbPartie' => $this->getNbPartie(),
            'equipe_position' => $this->getTeamWinner(),
            'winners' => $this->getTeamWinner()
        ];
    }

    public function getIdTournament(): ?int
    {
        return $this->idTournament;
    }

    public function setIdTournament(int $idTournament): static
    {
        $this->idTournament = $idTournament;

        return $this;
    }


}
