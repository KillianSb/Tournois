<?php

namespace App\Entity;

use App\Repository\ResultRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\ManyToMany(targetEntity: Tournament::class, inversedBy: 'results')]
    private Collection $idTournament;

    public function __construct()
    {
        $this->idTournament = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->teamWinner.' - '.$this->idTournament.' - '.$this->nbPartie;
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

    /**
     * @return Collection<int, Tournament>
     */
    public function getIdTournament(): Collection
    {
        return $this->idTournament;
    }

    public function addIdTournament(Tournament $idTournament): static
    {
        if (!$this->idTournament->contains($idTournament)) {
            $this->idTournament->add($idTournament);
        }

        return $this;
    }

    public function removeIdTournament(Tournament $idTournament): static
    {
        $this->idTournament->removeElement($idTournament);

        return $this;
    }


}
