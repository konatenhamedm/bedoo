<?php

namespace App\Entity;

use App\Repository\BatisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BatisRepository::class)]
class Batis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;


    #[ORM\Column(length: 255)]
    private ?string $lot = null;

    #[ORM\Column(length: 255)]
    private ?string $ilot = null;

    #[ORM\ManyToOne(inversedBy: 'batis')]
    private ?Proprietaire $proprietaire = null;

    #[ORM\Column(length: 255)]
    private ?string $titreFoncier = null;

    #[ORM\ManyToOne(inversedBy: 'batis')]
    private ?TypeMaison $typeMaison = null;

    #[ORM\Column(length: 255)]
    private ?string $adresseMaison = null;

    /**
     * @var Collection<int, Appartement>
     */
    #[ORM\OneToMany(targetEntity: Appartement::class, mappedBy: 'batis')]
    private Collection $appartements;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $quartier = null;

    #[ORM\ManyToOne(inversedBy: 'batis')]
    private ?Ville $ville = null;

    public function __construct()
    {
        $this->appartements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }


    public function getLot(): ?string
    {
        return $this->lot;
    }

    public function setLot(string $lot): static
    {
        $this->lot = $lot;

        return $this;
    }

    public function getIlot(): ?string
    {
        return $this->ilot;
    }

    public function setIlot(string $ilot): static
    {
        $this->ilot = $ilot;

        return $this;
    }

    public function getProprietaire(): ?Proprietaire
    {
        return $this->proprietaire;
    }

    public function setProprietaire(?Proprietaire $proprietaire): static
    {
        $this->proprietaire = $proprietaire;

        return $this;
    }

    public function getTitreFoncier(): ?string
    {
        return $this->titreFoncier;
    }

    public function setTitreFoncier(string $titreFoncier): static
    {
        $this->titreFoncier = $titreFoncier;

        return $this;
    }

    public function getTypeMaison(): ?TypeMaison
    {
        return $this->typeMaison;
    }

    public function setTypeMaison(?TypeMaison $typeMaison): static
    {
        $this->typeMaison = $typeMaison;

        return $this;
    }

    public function getAdresseMaison(): ?string
    {
        return $this->adresseMaison;
    }

    public function setAdresseMaison(string $adresseMaison): static
    {
        $this->adresseMaison = $adresseMaison;

        return $this;
    }

    /**
     * @return Collection<int, Appartement>
     */
    public function getAppartements(): Collection
    {
        return $this->appartements;
    }

    public function addAppartement(Appartement $appartement): static
    {
        if (!$this->appartements->contains($appartement)) {
            $this->appartements->add($appartement);
            $appartement->setBatis($this);
        }

        return $this;
    }

    public function removeAppartement(Appartement $appartement): static
    {
        if ($this->appartements->removeElement($appartement)) {
            // set the owning side to null (unless already changed)
            if ($appartement->getBatis() === $this) {
                $appartement->setBatis(null);
            }
        }

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getQuartier(): ?string
    {
        return $this->quartier;
    }

    public function setQuartier(string $quartier): static
    {
        $this->quartier = $quartier;

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): static
    {
        $this->ville = $ville;

        return $this;
    }
}
