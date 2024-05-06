<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Versement>
     */
    #[ORM\OneToMany(targetEntity: Versement::class, mappedBy: 'facture')]
    private Collection $versements;

    #[ORM\Column(length: 255)]
    private ?string $montant = null;

    #[ORM\Column(length: 255)]
    private ?string $soldeFacture = null;

    #[ORM\ManyToOne(inversedBy: 'factures')]
    private ?Contrat $contrat = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(length: 255)]
    private ?string $libelleFacture = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datePaiementTotal = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numero = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateLimitePaiment = null;

    public function __construct()
    {
        $this->versements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Versement>
     */
    public function getVersements(): Collection
    {
        return $this->versements;
    }

    public function addVersement(Versement $versement): static
    {
        if (!$this->versements->contains($versement)) {
            $this->versements->add($versement);
            $versement->setFacture($this);
        }

        return $this;
    }

    public function removeVersement(Versement $versement): static
    {
        if ($this->versements->removeElement($versement)) {
            // set the owning side to null (unless already changed)
            if ($versement->getFacture() === $this) {
                $versement->setFacture(null);
            }
        }

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getSoldeFacture(): ?string
    {
        return $this->soldeFacture;
    }

    public function setSoldeFacture(string $soldeFacture): static
    {
        $this->soldeFacture = $soldeFacture;

        return $this;
    }

    public function getContrat(): ?Contrat
    {
        return $this->contrat;
    }

    public function setContrat(?Contrat $contrat): static
    {
        $this->contrat = $contrat;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getLibelleFacture(): ?string
    {
        return $this->libelleFacture;
    }

    public function setLibelleFacture(string $libelleFacture): static
    {
        $this->libelleFacture = $libelleFacture;

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

    public function getDatePaiementTotal(): ?\DateTimeInterface
    {
        return $this->datePaiementTotal;
    }

    public function setDatePaiementTotal(\DateTimeInterface $datePaiementTotal): static
    {
        $this->datePaiementTotal = $datePaiementTotal;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getDateLimitePaiment(): ?\DateTimeInterface
    {
        return $this->dateLimitePaiment;
    }

    public function setDateLimitePaiment(?\DateTimeInterface $dateLimitePaiment): static
    {
        $this->dateLimitePaiment = $dateLimitePaiment;

        return $this;
    }
}
