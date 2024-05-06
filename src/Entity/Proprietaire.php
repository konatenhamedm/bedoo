<?php

namespace App\Entity;

use App\Repository\ProprietaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProprietaireRepository::class)]
class Proprietaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenoms = null;

    /**
     * @var Collection<int, Batis>
     */
    #[ORM\OneToMany(targetEntity: Batis::class, mappedBy: 'proprietaire')]
    private Collection $batis;

    /**
     * @var Collection<int, Quartier>
     */
    #[ORM\OneToMany(targetEntity: Quartier::class, mappedBy: 'proprietaire')]
    private Collection $quartiers;

    public function __construct()
    {
        $this->batis = new ArrayCollection();
        $this->quartiers = new ArrayCollection();
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenoms(): ?string
    {
        return $this->prenoms;
    }

    public function setPrenoms(string $prenoms): static
    {
        $this->prenoms = $prenoms;

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
            $bati->setProprietaire($this);
        }

        return $this;
    }

    public function removeBati(Batis $bati): static
    {
        if ($this->batis->removeElement($bati)) {
            // set the owning side to null (unless already changed)
            if ($bati->getProprietaire() === $this) {
                $bati->setProprietaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Quartier>
     */
    public function getQuartiers(): Collection
    {
        return $this->quartiers;
    }

    public function addQuartier(Quartier $quartier): static
    {
        if (!$this->quartiers->contains($quartier)) {
            $this->quartiers->add($quartier);
            $quartier->setProprietaire($this);
        }

        return $this;
    }

    public function removeQuartier(Quartier $quartier): static
    {
        if ($this->quartiers->removeElement($quartier)) {
            // set the owning side to null (unless already changed)
            if ($quartier->getProprietaire() === $this) {
                $quartier->setProprietaire(null);
            }
        }

        return $this;
    }
}
