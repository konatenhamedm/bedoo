<?php

namespace App\Entity;

use App\Repository\QuartierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuartierRepository::class)]
class Quartier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'quartiers')]
    private ?Ville $ville = null;

    /**
     * @var Collection<int, Batis>
     */
    #[ORM\OneToMany(targetEntity: Batis::class, mappedBy: 'quartier')]
    private Collection $batis;

    #[ORM\ManyToOne(inversedBy: 'quartiers')]
    private ?Proprietaire $proprietaire = null;

    public function __construct()
    {
        $this->batis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
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

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * @return Collection<int, Batis>
     */
    public function getBatis(): Collection
    {
        return $this->batis;
    }

    public function addBati(Batis $bati): static
    {
        if (!$this->batis->contains($bati)) {
            $this->batis->add($bati);
            $bati->setQuartier($this);
        }

        return $this;
    }

    public function removeBati(Batis $bati): static
    {
        if ($this->batis->removeElement($bati)) {
            // set the owning side to null (unless already changed)
            if ($bati->getQuartier() === $this) {
                $bati->setQuartier(null);
            }
        }

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
}
