<?php

namespace App\Entity;

use App\Repository\AppartementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppartementRepository::class)]
class Appartement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numeroEtage = null;

    #[ORM\Column(length: 255)]
    private ?string $numeroAppartement = null;

    #[ORM\Column]
    private ?int $nombrePiece = null;

    #[ORM\Column(length: 255)]
    private ?string $loyer = null;

    #[ORM\ManyToOne(inversedBy: 'appartements')]
    private ?Batis $batis = null;

    #[ORM\Column]
    private ?bool $occupe = null;

    #[ORM\Column(length: 255)]
    private ?string $details = null;

    /**
     * @var Collection<int, Contrat>
     */
    #[ORM\OneToMany(targetEntity: Contrat::class, mappedBy: 'Appartement')]
    private Collection $contrats;

    public function __construct()
    {
        $this->contrats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroEtage(): ?string
    {
        return $this->numeroEtage;
    }

    public function setNumeroEtage(?string $numeroEtage): static
    {
        $this->numeroEtage = $numeroEtage;

        return $this;
    }

    public function getNumeroAppartement(): ?string
    {
        return $this->numeroAppartement;
    }

    public function setNumeroAppartement(string $numeroAppartement): static
    {
        $this->numeroAppartement = $numeroAppartement;

        return $this;
    }

    public function getNombrePiece(): ?int
    {
        return $this->nombrePiece;
    }

    public function setNombrePiece(int $nombrePiece): static
    {
        $this->nombrePiece = $nombrePiece;

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

    public function getBatis(): ?Batis
    {
        return $this->batis;
    }

    public function setBatis(?Batis $batis): static
    {
        $this->batis = $batis;

        return $this;
    }

    public function isOccupe(): ?bool
    {
        return $this->occupe;
    }

    public function setOccupe(bool $occupe): static
    {
        $this->occupe = $occupe;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(string $details): static
    {
        $this->details = $details;

        return $this;
    }

    /**
     * @return Collection<int, Contrat>
     */
    public function getContrats(): Collection
    {
        return $this->contrats;
    }

    public function addContrat(Contrat $contrat): static
    {
        if (!$this->contrats->contains($contrat)) {
            $this->contrats->add($contrat);
            $contrat->setAppartement($this);
        }

        return $this;
    }

    public function removeContrat(Contrat $contrat): static
    {
        if ($this->contrats->removeElement($contrat)) {
            // set the owning side to null (unless already changed)
            if ($contrat->getAppartement() === $this) {
                $contrat->setAppartement(null);
            }
        }

        return $this;
    }
}
