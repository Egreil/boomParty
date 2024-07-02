<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]



#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['pseudo'], message: 'There is already an account with this pseudo')]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Email(message: "Adresse mail invalide")]
    #[Assert\Length(max: 180,maxMessage:"max 180 caractères")]
    #[ORM\Column(length: 180, /*unique:true*/)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */

    #[Assert\NotBlank]
    #[Assert\Length(max: 50, maxMessage: "Max 50 !")]
    #[Assert\Regex(pattern: '/^[a-zA-Z0-9]+$/i',htmlPattern:"[a-zA-Z0-9]+$")]
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50, maxMessage: "Max 50 !")]
    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50, maxMessage: "Max 50 !")]
    #[ORM\Column(length: 50)]
    private ?string $prenom = null;


    #[Assert\Length(max: 10, maxMessage: "Max 10 !")]
    #[ORM\Column(length: 10, nullable: true)]
    private ?string $telephone = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50, maxMessage: "Max 50 !",min:4,minMessage: "le pseudo doit avoir minimum 4 caractères")]
    #[ORM\Column(length: 50, nullable: false,/*unique:true*/ )]
    private ?string $pseudo = null;

    #[ORM\Column]
    private ?string $image = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateModification = null;

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\ManyToMany(targetEntity: Sortie::class, inversedBy: 'participants')]
    private Collection $sortie;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    #[ORM\Column]
    private ?bool $actif = null;

    /**
     * @var Collection<int, sortie>
     */
    #[ORM\OneToMany(targetEntity: sortie::class, mappedBy: 'organisateur', orphanRemoval: true)]
    private Collection $sortiesOrganisees;

    public function __construct()
    {
        $this->sortie = new ArrayCollection();
        $this->sortiesOrganisees = new ArrayCollection();
    }

    public function getImage(): ?string
    {
        return $this->image;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
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
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): static
    {
        $this->pseudo = $pseudo;

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

    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setDateModification(?\DateTimeInterface $dateModification): static
    {
        $this->dateModification = $dateModification;

        return $this;
    }





    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortie(): Collection
    {
        return $this->sortie;
    }

    public function addSortie(Sortie $sortie): static
    {
        if (!$this->sortie->contains($sortie)) {
            $this->sortie->add($sortie);
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): static
    {
        $this->sortie->removeElement($sortie);

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, sortie>
     */
    public function getSortiesOrganisees(): Collection
    {
        return $this->sortiesOrganisees;
    }

    public function addSortiesOrganisees(sortie $sortiesOrganisees): static
    {
        if (!$this->sortiesOrganisees->contains($sortiesOrganisees)) {
            $this->sortiesOrganisees->add($sortiesOrganisees);
            $sortiesOrganisees->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesOrganisees(sortie $sortiesOrganisees): static
    {
        if ($this->sortiesOrganisees->removeElement($sortiesOrganisees)) {
            // set the owning side to null (unless already changed)
            if ($sortiesOrganisees->getOrganisateur() === $this) {
                $sortiesOrganisees->setOrganisateur(null);
            }
        }

        return $this;
    }

}
