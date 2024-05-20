<?php

namespace App\Entity;

use App\Repository\ContratRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContratRepository::class)]
class Contrat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'contrats')]
    private ?Locataire $locataire = null;

    #[ORM\ManyToOne(inversedBy: 'contrats')]
    private ?Appartement $Appartement = null;

    #[ORM\Column]
    private ?int $NombreMoisCaution = null;

    #[ORM\Column(length: 255)]
    private ?string $MontantCaution = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fraisAnnexe = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEntree = null;

    #[ORM\Column]
    private ?bool $paiementPartiel = null;

    #[ORM\Column]
    private ?int $jourMoisPaiement = null;

    #[ORM\Column(length: 255)]
    private ?string $loyer = null;

    #[ORM\Column]
    private ?int $NombreMoisAvance = null;

    #[ORM\Column(length: 255)]
    private ?string $MontantAvance = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateProchainVersement = null;

    #[ORM\ManyToOne(inversedBy: 'contrats')]
    private ?Nature $nature = null;

    #[ORM\ManyToOne(inversedBy: 'contrats')]
    private ?MotifResiliation $motif = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFinContrat = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;



    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Fichier $scanContrat = null;

    #[ORM\Column(length: 255)]
    private ?string $montantTotal = null;

    #[ORM\Column(length: 255)]
    private ?string $regime = null;

    /**
     * @var Collection<int, Facture>
     */
    #[ORM\OneToMany(targetEntity: Facture::class, mappedBy: 'contrat')]
    private Collection $factures;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numero = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateValidation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateResiliation = null;

    #[ORM\Column]
    private ?bool $firstPay = null;

    #[ORM\Column(nullable: true)]
    private ?int $jourReceptionFacture = null;

    public function __construct()
    {
        $this->factures = new ArrayCollection();
        $this->etat = "pas_actif";
        $this->firstPay = false;
        $this->dateCreation = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocataire(): ?Locataire
    {
        return $this->locataire;
    }

    public function setLocataire(?Locataire $locataire): static
    {
        $this->locataire = $locataire;

        return $this;
    }

    public function getAppartement(): ?Appartement
    {
        return $this->Appartement;
    }

    public function setAppartement(?Appartement $Appartement): static
    {
        $this->Appartement = $Appartement;

        return $this;
    }

    public function getNombreMoisCaution(): ?int
    {
        return $this->NombreMoisCaution;
    }

    public function setNombreMoisCaution(int $NombreMoisCaution): static
    {
        $this->NombreMoisCaution = $NombreMoisCaution;

        return $this;
    }

    public function getMontantCaution(): ?string
    {
        return $this->MontantCaution;
    }

    public function setMontantCaution(string $MontantCaution): static
    {
        $this->MontantCaution = $MontantCaution;

        return $this;
    }

    public function getFraisAnnexe(): ?string
    {
        return $this->fraisAnnexe;
    }

    public function setFraisAnnexe(?string $fraisAnnexe): static
    {
        $this->fraisAnnexe = $fraisAnnexe;

        return $this;
    }

    public function getDateEntree(): ?\DateTimeInterface
    {
        return $this->dateEntree;
    }

    public function setDateEntree(\DateTimeInterface $dateEntree): static
    {
        $this->dateEntree = $dateEntree;

        return $this;
    }

    public function isPaiementPartiel(): ?bool
    {
        return $this->paiementPartiel;
    }

    public function setPaiementPartiel(bool $paiementPartiel): static
    {
        $this->paiementPartiel = $paiementPartiel;

        return $this;
    }

    public function getJourMoisPaiement(): ?int
    {
        return $this->jourMoisPaiement;
    }

    public function setJourMoisPaiement(int $jourMoisPaiement): static
    {
        $this->jourMoisPaiement = $jourMoisPaiement;

        return $this;
    }

    public function getLoyer(): ?string
    {
        return $this->loyer;
    }

    public function setLoyer(string $loyer): static
    {
        $this->loyer = $loyer;

        return $this;
    }

    public function getNombreMoisAvance(): ?int
    {
        return $this->NombreMoisAvance;
    }

    public function setNombreMoisAvance(int $NombreMoisAvance): static
    {
        $this->NombreMoisAvance = $NombreMoisAvance;

        return $this;
    }

    public function getMontantAvance(): ?string
    {
        return $this->MontantAvance;
    }

    public function setMontantAvance(string $MontantAvance): static
    {
        $this->MontantAvance = $MontantAvance;

        return $this;
    }

    public function getDateProchainVersement(): ?\DateTimeInterface
    {
        return $this->dateProchainVersement;
    }

    public function setDateProchainVersement(\DateTimeInterface $dateProchainVersement): static
    {
        $this->dateProchainVersement = $dateProchainVersement;

        return $this;
    }

    public function getNature(): ?Nature
    {
        return $this->nature;
    }

    public function setNature(?Nature $nature): static
    {
        $this->nature = $nature;

        return $this;
    }

    public function getMotif(): ?MotifResiliation
    {
        return $this->motif;
    }

    public function setMotif(?MotifResiliation $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getDateFinContrat(): ?\DateTimeInterface
    {
        return $this->dateFinContrat;
    }

    public function setDateFinContrat(\DateTimeInterface $dateFinContrat): static
    {
        $this->dateFinContrat = $dateFinContrat;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getMontantTotal(): ?string
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(string $montantTotal): static
    {
        $this->montantTotal = $montantTotal;

        return $this;
    }
    public function getScanContrat(): ?Fichier
    {
        return $this->scanContrat;
    }

    public function setScanContrat(Fichier $scanContrat): static
    {
        $this->scanContrat = $scanContrat;

        return $this;
    }

    public function getRegime(): ?string
    {
        return $this->regime;
    }

    public function setRegime(string $regime): static
    {
        $this->regime = $regime;

        return $this;
    }

    /**
     * @return Collection<int, Facture>
     */
    public function getFactures(): Collection
    {
        return $this->factures;
    }

    public function addFacture(Facture $facture): static
    {
        if (!$this->factures->contains($facture)) {
            $this->factures->add($facture);
            $facture->setContrat($this);
        }

        return $this;
    }

    public function removeFacture(Facture $facture): static
    {
        if ($this->factures->removeElement($facture)) {
            // set the owning side to null (unless already changed)
            if ($facture->getContrat() === $this) {
                $facture->setContrat(null);
            }
        }

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;

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

    public function getDateValidation(): ?\DateTimeInterface
    {
        return $this->dateValidation;
    }

    public function setDateValidation(?\DateTimeInterface $dateValidation): static
    {
        $this->dateValidation = $dateValidation;

        return $this;
    }

    public function getDateResiliation(): ?\DateTimeInterface
    {
        return $this->dateResiliation;
    }

    public function setDateResiliation(\DateTimeInterface $dateResiliation): static
    {
        $this->dateResiliation = $dateResiliation;

        return $this;
    }

    public function isFirstPay(): ?bool
    {
        return $this->firstPay;
    }

    public function setFirstPay(bool $firstPay): static
    {
        $this->firstPay = $firstPay;

        return $this;
    }

    public function getJourReceptionFacture(): ?int
    {
        return $this->jourReceptionFacture;
    }

    public function setJourReceptionFacture(?int $jourReceptionFacture): static
    {
        $this->jourReceptionFacture = $jourReceptionFacture;

        return $this;
    }
}
