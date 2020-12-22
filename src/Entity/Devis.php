<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
/**
 * @ORM\Entity(repositoryClass="App\Repository\DevisRepository")
 */
class Devis
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_creation;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $Totale;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $reduction;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $statut;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_confirmation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_souhaitee;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Demande", cascade={"persist"})
     * @ORM\JoinColumn( onDelete="CASCADE")
     */
    private $demande;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $informations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DevisOptions", mappedBy="devis")
     * @ORM\JoinColumn( onDelete="CASCADE")

     */
    private $devisOptions;

    public function __construct()
    {
        $this->devisOptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(?\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getTotale(): ?float
    {
        return $this->Totale;
    }

    public function setTotale(?float $Totale): self
    {
        $this->Totale = $Totale;

        return $this;
    }

    public function getReduction(): ?float
    {
        return $this->reduction;
    }

    public function setReduction(?float $reduction): self
    {
        $this->reduction = $reduction;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDateConfirmation(): ?\DateTimeInterface
    {
        return $this->date_confirmation;
    }

    public function setDateConfirmation(?\DateTimeInterface $date_confirmation): self
    {
        $this->date_confirmation = $date_confirmation;

        return $this;
    }

    public function getDateSouhaitee(): ?\DateTimeInterface
    {
        return $this->date_souhaitee;
    }

    public function setDateSouhaitee(?\DateTimeInterface $date_souhaitee): self
    {
        $this->date_souhaitee = $date_souhaitee;

        return $this;
    }

    public function getDemande(): ?Demande
    {
        return $this->demande;
    }

    public function setDemande(?Demande $demande): self
    {
        $this->demande = $demande;

        return $this;
    }

    public function getInformations(): ?string
    {
        return $this->informations;
    }

    public function setInformations(?string $informations): self
    {
        $this->informations = $informations;

        return $this;
    }

    /**
     * @return Collection|DevisOptions[]
     */
    public function getDevisOptions(): Collection
    {
        return $this->devisOptions;
    }

    public function addDevisOption(DevisOptions $devisOption): self
    {
        if (!$this->devisOptions->contains($devisOption)) {
            $this->devisOptions[] = $devisOption;
            $devisOption->setDevis($this);
        }

        return $this;
    }

    public function removeDevisOption(DevisOptions $devisOption): self
    {
        if ($this->devisOptions->contains($devisOption)) {
            $this->devisOptions->removeElement($devisOption);
            // set the owning side to null (unless already changed)
            if ($devisOption->getDevis() === $this) {
                $devisOption->setDevis(null);
            }
        }

        return $this;
    }
}
