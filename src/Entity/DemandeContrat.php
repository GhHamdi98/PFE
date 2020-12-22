<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
/**
 * @ORM\Entity(repositoryClass="App\Repository\DemandeContratRepository")
 */
class DemandeContrat
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $contrat;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Demande", cascade={"persist"})
     * @ORM\JoinColumn( onDelete="CASCADE")
     */
    private $demande;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $etat;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContrat(): ?string
    {
        return $this->contrat;
    }

    public function setContrat(?string $contrat): self
    {
        $this->contrat = $contrat;

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

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }
}
