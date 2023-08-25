<?php

namespace App\Entity;

use App\Repository\TournamentRepository;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TournamentRepository::class)]
class Tournament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $rules = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\GreaterThanOrEqual(propertyPath:"dateCreation")]
    private ?\DateTimeInterface $dateBeginTournament;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\GreaterThanOrEqual(propertyPath:"dateCreation")]
    private ?\DateTimeInterface $dateEndTournament;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\LessThanOrEqual(propertyPath:"dateBeginTournament")]
    private ?\DateTimeInterface $dateLimitRegistration;

    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(value:2, message: "Minimum 2 équipes")]
    private ?int $nbTeamMax = null;

    #[ORM\Column(length: 255)]
    private ?string $tournamentInfo = null;

    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(value:0, message: "Le prix d'entrée est gratuit 😊 ou supérieur")]
    private ?int $entryPrice = null;

    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(value:0, message: "La récompense ne peux pas être négative 🙃")]
    private ?int $reward = null;

    #[ORM\ManyToOne(inversedBy: 'tournaments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?VideoGame $videogame = null;

    #[ORM\ManyToOne(inversedBy: 'tournaments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $location = null;

    #[ORM\ManyToOne(inversedBy: 'tournaments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: Team::class, inversedBy: 'tournaments')]
    private Collection $team;

    #[ORM\ManyToOne(inversedBy: 'tournaments')]
    private ?Game $game = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    public function __construct()
    {
        //Initialisation des dates en fonction du fuseau horaire Paris
        $timezoneParis = new DateTimeZone('Europe/Paris');

        //$this->game = new ArrayCollection();
        $this->team = new ArrayCollection();
        $this->setStatus('open');;

        $this->dateCreation = new \DateTime('now', $timezoneParis);
        $this->dateBeginTournament = new \DateTime('now', $timezoneParis);
        $this->setDateEndTournament($this->getDateBeginTournament()->add(new \DateInterval('P1D')));
        $this->setDateLimitRegistration($this->getDateBeginTournament());
    }

    public function __toString(): string
    {
        return $this->name . ' - ' . ' début du tournois le ' . $this->getDateBeginTournament()->format('d/m/Y ' . ' à ' . ' H:i');
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

    public function getRules(): ?string
    {
        return $this->rules;
    }

    public function setRules(string $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    public function getDateBeginTournament(): ?\DateTimeInterface
    {
        return $this->dateBeginTournament;
    }

    public function setDateBeginTournament(\DateTimeInterface $dateBeginTournament): static
    {
        $this->dateBeginTournament = $dateBeginTournament;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateEndTournament(): ?\DateTimeInterface
    {
        return $this->dateEndTournament;
    }

    public function setDateEndTournament(\DateTimeInterface $dateEndTournament): static
    {
        $this->dateEndTournament = $dateEndTournament;

        return $this;
    }

    public function getDateLimitRegistration(): ?\DateTimeInterface
    {
        return $this->dateLimitRegistration;
    }

    public function setDateLimitRegistration(\DateTimeInterface $dateLimitRegistration): static
    {
        $this->dateLimitRegistration = $dateLimitRegistration;

        return $this;
    }

    public function getNbTeamMax(): ?int
    {
        return $this->nbTeamMax;
    }

    public function setNbTeamMax(int $nbTeamMax): static
    {
        $this->nbTeamMax = $nbTeamMax;

        return $this;
    }

    public function getTournamentInfo(): ?string
    {
        return $this->tournamentInfo;
    }

    public function setTournamentInfo(string $tournamentInfo): static
    {
        $this->tournamentInfo = $tournamentInfo;

        return $this;
    }

    public function getEntryPrice(): ?int
    {
        return $this->entryPrice;
    }

    public function setEntryPrice(int $entryPrice): static
    {
        $this->entryPrice = $entryPrice;

        return $this;
    }

    public function getReward(): ?int
    {
        return $this->reward;
    }

    public function setReward(int $reward): static
    {
        $this->reward = $reward;

        return $this;
    }

    public function getVideogame(): ?VideoGame
    {
        return $this->videogame;
    }

    public function setVideogame(?VideoGame $videogame): static
    {
        $this->videogame = $videogame;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;

        return $this;
    }


    /**
     * @return Collection<int, Game>
     */
    public function getGame(): Collection
    {
        return $this->game;
    }

    public function addGame(Game $game): static
    {
        if (!$this->game->contains($game)) {
            $this->game->add($game);
            $game->setTournament($this);
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        if ($this->game->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getTournament() === $this) {
                $game->setTournament(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getTeam(): Collection
    {
        return $this->team;
    }

    public function addTeam(Team $team): static
    {
        if (!$this->team->contains($team)) {
            $this->team->add($team);
        }

        return $this;
    }

    public function removeTeam(Team $team): static
    {
        $this->team->removeElement($team);

        return $this;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
