<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    /**
     * @var Collection<int, Quartier>
     */
    #[ORM\OneToMany(targetEntity: Quartier::class, mappedBy: 'ville')]
    private Collection $quartiers;

    /**
     * @var Collection<int, Batis>
     */
    #[ORM\OneToMany(targetEntity: Batis::class, mappedBy: 'ville')]
    private Collection $batis;

    public function __construct()
    {
        $this->quartiers = new ArrayCollection();
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
            $quartier->setVille($this);
        }

        return $this;
    }

    public function removeQuartier(Quartier $quartier): static
    {
        if ($this->quartiers->removeElement($quartier)) {
            // set the owning side to null (unless already changed)
            if ($quartier->getVille() === $this) {
                $quartier->setVille(null);
            }
        }

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
            $bati->setVille($this);
        }

        return $this;
    }

    public function removeBati(Batis $bati): static
    {
        if ($this->batis->removeElement($bati)) {
            // set the owning side to null (unless already changed)
            if ($bati->getVille() === $this) {
                $bati->setVille(null);
            }
        }

        return $this;
    }
}
