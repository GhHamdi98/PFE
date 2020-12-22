<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DemandeRepository")
 */
class Demande
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nature_projet;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fonctionalite_projet;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $principale_projet;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $site_similaire;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $module_b2c;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $module_b2b;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $langue_projet;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $couleur_prefere;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $logo;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $charte_graphique;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $details;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="demandePartenaire",cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $Partenaire;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="demandeCommerciale",cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $Commerciale;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="DemandeProspect")
     * @ORM\JoinColumn( onDelete="CASCADE")
     */
    private $Prospect;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $echeance;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Service", inversedBy="demandes")
     */
    private $service;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Etat;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Avancement", mappedBy="demande")
     * @ORM\JoinColumn( onDelete="CASCADE")
     */
    private $avancements;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $lance;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $budget;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $refonte;
    public function __construct()
    {
        $this->avancements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNatureProjet(): ?string
    {
        return $this->nature_projet;
    }

    public function setNatureProjet(?string $nature_projet): self
    {
        $this->nature_projet = $nature_projet;

        return $this;
    }

    public function getFonctionaliteProjet(): ?string
    {
        return $this->fonctionalite_projet;
    }

    public function setFonctionaliteProjet(?string $fonctionalite_projet): self
    {
        $this->fonctionalite_projet = $fonctionalite_projet;

        return $this;
    }

    public function getPrincipaleProjet(): ?string
    {
        return $this->principale_projet;
    }

    public function setPrincipaleProjet(?string $principale_projet): self
    {
        $this->principale_projet = $principale_projet;

        return $this;
    }

    public function getSiteSimilaire(): ?string
    {
        return $this->site_similaire;
    }

    public function setSiteSimilaire(?string $site_similaire): self
    {
        $this->site_similaire = $site_similaire;

        return $this;
    }

    public function getModuleB2c(): ?string
    {
        return $this->module_b2c;
    }

    public function setModuleB2c(?string $module_b2c): self
    {
        $this->module_b2c = $module_b2c;

        return $this;
    }

    public function getModuleB2b(): ?string
    {
        return $this->module_b2b;
    }

    public function setModuleB2b(?string $module_b2b): self
    {
        $this->module_b2b = $module_b2b;

        return $this;
    }

    public function getLangueProjet(): ?string
    {
        return $this->langue_projet;
    }

    public function setLangueProjet(?string $langue_projet): self
    {
        $this->langue_projet = $langue_projet;

        return $this;
    }

    public function getCouleurPrefere(): ?string
    {
        return $this->couleur_prefere;
    }

    public function setCouleurPrefere(?string $couleur_prefere): self
    {
        $this->couleur_prefere = $couleur_prefere;

        return $this;
    }

    public function getLogo(): ?bool
    {
        return $this->logo;
    }

    public function setLogo(?bool $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getCharteGraphique(): ?bool
    {
        return $this->charte_graphique;
    }

    public function setCharteGraphique(?bool $charte_graphique): self
    {
        $this->charte_graphique = $charte_graphique;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getCommerciale(): ?User
    {
        return $this->Commerciale;
    }

    public function setCommerciale(?User $commerciale): self
    {
        $this->Commerciale = $commerciale;

        return $this;
    }

    public function getPartenaire(): ?User
    {
        return $this->Partenaire;
    }

    public function setPartenaire(?User $partenaire): self
    {
        $this->Partenaire = $partenaire;

        return $this;
    }

    public function getProspect(): ?User
    {
        return $this->Prospect;
    }

    public function setProspect(?User $Prospect): self
    {
        $this->Prospect = $Prospect;

        return $this;
    }

    public function getEcheance(): ?string
    {
        return $this->echeance;
    }

    public function setEcheance(?string $echeance): self
    {
        $this->echeance = $echeance;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->Etat;
    }

    public function setEtat(?string $Etat): self
    {
        $this->Etat = $Etat;

        return $this;
    }

    /**
     * @return Collection|Avancement[]
     */
    public function getAvancements(): Collection
    {
        return $this->avancements;
    }

    public function addAvancement(Avancement $avancement): self
    {
        if (!$this->avancements->contains($avancement)) {
            $this->avancements[] = $avancement;
            $avancement->setDemande($this);
        }

        return $this;
    }

    public function removeAvancement(Avancement $avancement): self
    {
        if ($this->avancements->contains($avancement)) {
            $this->avancements->removeElement($avancement);
            // set the owning side to null (unless already changed)
            if ($avancement->getDemande() === $this) {
                $avancement->setDemande(null);
            }
        }

        return $this;
    }

    public function getLance(): ?bool
    {
        return $this->lance;
    }

    public function setLance(?bool $lance): self
    {
        $this->lance = $lance;

        return $this;
    }

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(?float $budget): self
    {
        $this->budget = $budget;

        return $this;
    }

    public function getRefonte(): ?string
    {
        return $this->refonte;
    }

    public function setRefonte(?string $refonte): self
    {
        $this->refonte = $refonte;

        return $this;
    }

}
