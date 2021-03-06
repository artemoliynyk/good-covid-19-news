<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CountryRepository")
 */
class Country
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="CountryCase", mappedBy="country", orphanRemoval=true)
     */
    private $countryCase;

    /**
     * @ORM\OneToMany(targetEntity="CountryCasesChange", mappedBy="country", orphanRemoval=true)
     */
    private $casesChanges;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true, options={"default": true})
     */
    private $dataRecovered;


    public function __construct($name)
    {
        $this->countryCase = new ArrayCollection();

        $this->name = $name;
        $this->casesChanges = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return Collection|CountryCase[]
     */
    public function getCountryCase(): Collection
    {
        return $this->countryCase;
    }

    public function addCountryCase(CountryCase $countryCase): self
    {
        if (!$this->countryCase->contains($countryCase)) {
            $this->countryCase[] = $countryCase;
            $countryCase->setCountry($this);
        }

        return $this;
    }

    public function removeCountryCase(CountryCase $countryCase): self
    {
        if ($this->countryCase->contains($countryCase)) {
            $this->countryCase->removeElement($countryCase);
            // set the owning side to null (unless already changed)
            if ($countryCase->getCountry() === $this) {
                $countryCase->setCountry(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CountryCasesChange[]
     */
    public function getCasesChanges(): Collection
    {
        return $this->casesChanges;
    }

    public function addCasesChange(CountryCasesChange $casesChange): self
    {
        if (!$this->casesChanges->contains($casesChange)) {
            $this->casesChanges[] = $casesChange;
            $casesChange->setCountry($this);
        }

        return $this;
    }

    public function removeCasesChange(CountryCasesChange $casesChange): self
    {
        if ($this->casesChanges->contains($casesChange)) {
            $this->casesChanges->removeElement($casesChange);
            // set the owning side to null (unless already changed)
            if ($casesChange->getCountry() === $this) {
                $casesChange->setCountry(null);
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasDataRecovered(): ?bool
    {
        return $this->dataRecovered;
    }

    /**
     * @param bool $dataRecovered
     */
    public function setDataRecovered(bool $dataRecovered): void
    {
        $this->dataRecovered = $dataRecovered;
    }

}
