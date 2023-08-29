<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email]
    #[Assert\NotBlank(message: "Le champs ne peu pas etre vide !")] // Contraint de validation not null
    #[Assert\NotNull(message: "Le champs ne poeu pas etre null !")]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    //TODO exige au moins une lettre Majuscule
/*    #[Assert\Regex(
        pattern: "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/",
        message: "Le mot de passe doit contenir au moins 8 caractères, dont au moins une lettre et un chiffre.")] // Contraint de validation Regex*/
    private ?string $password = null;

    #[ORM\Column(length: 30)]
    #[Assert\Length(min: 4, max: 30, minMessage: "Trop court ! doit contenir minimum 2 caractère !", maxMessage: "Trop long !")] // Contraint de validation
    #[Assert\NotBlank(message: "Le champs ne peu pas etre vide !")] // Contraint de validation not null
    #[Assert\NotNull(message: "Le champs ne poeu pas etre null !")]
    private ?string $username = null;

    #[ORM\Column(length: 30)]
    #[Assert\Length(min: 3, max: 30, minMessage: "Trop court ! doit contenir minimum 2 caractère !", maxMessage: "Trop long !")] // Contraint de validation
    #[Assert\NotBlank(message: "Le champs ne peu pas etre vide !")] // Contraint de validation not null
    #[Assert\NotNull(message: "Le champs ne poeu pas etre null !")]
    private ?string $lastname = null;

    #[ORM\Column(length: 30)]
    #[Assert\Length(min: 2, max: 30, minMessage: "Trop court ! doit contenir minimum 2 caractère !", maxMessage: "Trop long !")] // Contraint de validation
    #[Assert\NotBlank(message: "Le champs ne peu pas etre vide !")] // Contraint de validation not null
    #[Assert\NotNull(message: "Le champs ne poeu pas etre null !")]
    private ?string $firstname = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Le champs ne peu pas etre vide !")] // Contraint de validation not null
    #[Assert\NotNull(message: "Le champs ne poeu pas etre null !")]
    private ?string $phoneNumber = null;

    #[ORM\Column]
    private ?bool $isAdmin = false;

    #[ORM\Column]
    private ?bool $isActive = false;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Tournament::class)]
    private Collection $tournaments;

    #[ORM\ManyToMany(targetEntity: Team::class, mappedBy: 'user')]
    private Collection $teams;

    #[ORM\Column(length: 255)]
    private ?string $picture = 'build/images/avatar_1.svg';

    public function __construct()
    {
        $this->tournaments = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function addOrganizerRole()
    {
        $roles = $this->getRoles();
        if (!in_array('ROLE_ORGANIZER', $roles)) {
            $roles[] = 'ROLE_ORGANIZER';
            $this->setRoles($roles);
            return $this;
        }
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function isIsAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): static
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function __toString(): string
    {
        return $this->username;
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
            $tournament->setUser($this);
        }

        return $this;
    }

    public function removeTournament(Tournament $tournament): static
    {
        if ($this->tournaments->removeElement($tournament)) {
            // set the owning side to null (unless already changed)
            if ($tournament->getUser() === $this) {
                $tournament->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): static
    {
        if (!$this->teams->contains($team)) {
            $this->teams->add($team);
            $team->addUser($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): static
    {
        if ($this->teams->removeElement($team)) {
            $team->removeUser($this);
        }

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
