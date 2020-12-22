<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $username;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $activite;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_login;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $connecte;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pays", inversedBy="users")
     */
    private $Pays;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Demande", mappedBy="Commerciale")
     * @ORM\JoinColumn( onDelete="CASCADE")
     */
    private $demandeCommerciale;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Demande", mappedBy="Partenaire")
     * @ORM\JoinColumn( onDelete="CASCADE")
     */
    private $demandePartenaire;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="users")
     * @ORM\JoinColumn( onDelete="CASCADE")
     */
    private $level;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="level")
     * @ORM\JoinColumn( onDelete="CASCADE")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Demande", mappedBy="Prospect")
     * @ORM\JoinColumn( onDelete="CASCADE")
     */
    private $DemandeProspect;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->demandeCommerciale = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->DemandeProspect = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getActivite(): ?string
    {
        return $this->activite;
    }

    public function setActivite(?string $activite): self
    {
        $this->activite = $activite;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getLastLogin(): ?DateTimeInterface
    {
        return $this->last_login;
    }

    public function setLastLogin(?DateTimeInterface $last_login): self
    {
        $this->last_login = $last_login;

        return $this;
    }

    public function getConnecte(): ?bool
    {
        return $this->connecte;
    }

    public function setConnecte(?bool $connecte): self
    {
        $this->connecte = $connecte;

        return $this;
    }

    public function __toString(): string
    {
       return (string)$this->getUsername();
    }


    public function getPays(): ?Pays
    {
        return $this->Pays;
    }

    public function setPays(?Pays $Pays): self
    {
        $this->Pays = $Pays;

        return $this;
    }

    /**
     * @return Collection|Demande[]
     */
    public function getDemandePartenaire(): Collection
    {
        return $this->demandePartenaire;
    }

    public function addDemandePartenaire(Demande $demandePartenaire): self
    {
        if (!$this->demandePartenaire->contains($demandePartenaire)) {
            $this->demandePartenaire[] = $demandePartenaire;
            $demandePartenaire->setPartenaire($this);
        }

        return $this;
    }

    public function removeDemandePartenaire(Demande $demandePartenaire): self
    {
        if ($this->demandePartenaire->contains($demandePartenaire)) {
            $this->demandePartenaire->removeElement($demandePartenaire);
            // set the owning side to null (unless already changed)
            if ($demandePartenaire->getPartenaire() === $this) {
                $demandePartenaire->setPartenaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Demande[]
     */
    public function getDemandeCommerciale(): Collection
    {
        return $this->demandeCommerciale;
    }

    public function addDemandeCommerciale(Demande $demandeCommerciale): self
    {
        if (!$this->demandeCommerciale->contains($demandeCommerciale)) {
            $this->demandeCommerciale[] = $demandeCommerciale;
            $demandeCommerciale->setCommerciale($this);
        }

        return $this;
    }

    public function removeDemandeCommerciale(Demande $demandeCommerciale): self
    {
        if ($this->demandeCommerciale->contains($demandeCommerciale)) {
            $this->demandeCommerciale->removeElement($demandeCommerciale);
            // set the owning side to null (unless already changed)
            if ($demandeCommerciale->getCommerciale() === $this) {
                $demandeCommerciale->setCommerciale(null);
            }
        }

        return $this;
    }
    public function getLevel(): ?self
    {
        return $this->level;
    }

    public function setLevel(?self $levle): self
    {
        $this->level = $levle;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(self $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setLevel($this);
        }

        return $this;
    }

    public function removeUser(self $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getLevel() === $this) {
                $user->setLevel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Demande[]
     */
    public function getDemandeProspect(): Collection
    {
        return $this->DemandeProspect;
    }

    public function addDemandeProspect(Demande $demandeProspect): self
    {
        if (!$this->DemandeProspect->contains($demandeProspect)) {
            $this->DemandeProspect[] = $demandeProspect;
            $demandeProspect->setProspect($this);
        }

        return $this;
    }

    public function removeDemandeProspect(Demande $demandeProspect): self
    {
        if ($this->DemandeProspect->contains($demandeProspect)) {
            $this->DemandeProspect->removeElement($demandeProspect);
            // set the owning side to null (unless already changed)
            if ($demandeProspect->getProspect() === $this) {
                $demandeProspect->setProspect(null);
            }
        }

        return $this;
    }

}
