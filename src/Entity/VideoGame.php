<?php

namespace App\Entity;

use App\Repository\VideoGameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VideoGameRepository::class)]
class VideoGame
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: "Veuillez indiquer le nombre d'équipe")]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, minMessage: "Veuillez indiquer au moins 3 caractères")]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $isOnline = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Veuillez indiquer le nombre d'équipe")]
    #[Assert\NotNull]
    private ?int $nbTeam = null;

    #[ORM\OneToMany(mappedBy: 'videogame', targetEntity: Tournament::class)]
    private Collection $tournaments;

    #[ORM\Column(length: 255)]
    private ?string $rules = null;

    #[ORM\Column(length: 255)]
    private ?string $picture = null;

    public function __construct()
    {
        $this->tournaments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isIsOnline(): ?bool
    {
        return $this->isOnline;
    }

    public function setIsOnline(bool $isOnline): static
    {
        $this->isOnline = $isOnline;

        return $this;
    }

    public function getNbTeam(): ?int
    {
        return $this->nbTeam;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function setNbTeam(int $nbTeam): static
    {
        $this->nbTeam = $nbTeam;

        return $this;
    }

    /**
     * @return Collection<int, Tournament>
     */
    public function getTournaments(): Collection
    {
        return $this->tournaments;
    }

    public function addTournament(Tournament $tournament): static
    {
        if (!$this->tournaments->contains($tournament)) {
            $this->tournaments->add($tournament);
            $tournament->setVideogame($this);
        }

        return $this;
    }



    public function removeTournament(Tournament $tournament): static
    {
        if ($this->tournaments->removeElement($tournament)) {
            // set the owning side to null (unless already changed)
            if ($tournament->getVideogame() === $this) {
                $tournament->setVideogame(null);
            }
        }

        return $this;
    }

    public function getRules(): ?string
    {
        return $this->rules;
    }

    public function setRules(string $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }
}
