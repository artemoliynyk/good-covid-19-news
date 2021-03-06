<?php

namespace App\Entity;

use App\Entity\Traits\ChangeTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CasesChangeRepository")
 * @ORM\Table(name="country_cases_change", uniqueConstraints={@UniqueConstraint(columns={"change_date", "country_id"})})
 * @UniqueEntity(fields={"changeDate", "country"})
 */
class CountryCasesChange
{
    use ChangeTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="change_date", type="date")
     */
    private $changeDate;

    /**
     * @var CountryCase
     *
     * @ORM\OneToOne(targetEntity="CountryCase", mappedBy="casesChange")
     * @ORM\JoinColumn(name="case_id", onDelete="CASCADE")
     */
    private $countryCase;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country", inversedBy="casesChanges")
     * @ORM\JoinColumn(name="country_id", onDelete="CASCADE")
     */
    private $country;

    /**
     * CasesChange constructor.
     *
     * @param $countryCase
     */
    public function __construct(?CountryCase $countryCase = null)
    {
        if (!is_null($countryCase)) {
            $countryCase->setCasesChange($this);
            $this->countryCase = $countryCase;

            $this->changeDate = $countryCase->getCaseDate();
            $this->country = $countryCase->getCountry();
        }
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country): void
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getChangeDate()
    {
        return $this->changeDate;
    }

    /**
     * @param mixed $changeDate
     */
    public function setChangeDate($changeDate): void
    {
        $this->changeDate = $changeDate;
    }


    /**
     * @return mixed
     */
    public function getNew()
    {
        return $this->new;
    }

    /**
     * @param mixed $new
     */
    public function setNew($new): void
    {
        $this->new = $new;
    }

    /**
     * @param integer $new
     * @param integer $percent
     */
    public function setNewData($new, $percent): void
    {
        $this->new = $new;
        $this->newPercent = $percent;
    }

    /**
     * @return mixed
     */
    public function getNewPercent()
    {
        return $this->newPercent;
    }

    /**
     * @param mixed $newPercent
     */
    public function setNewPercent($newPercent): void
    {
        $this->newPercent = $newPercent;
    }

    /**
     * @return mixed
     */
    public function getDeaths()
    {
        return $this->deaths;
    }

    /**
     * @param mixed $deaths
     */
    public function setDeaths($deaths): void
    {
        $this->deaths = $deaths;
    }

    /**
     * @param integer $deaths
     * @param integer $percent
     */
    public function setDeathsData($deaths, $percent): void
    {
        $this->deaths = $deaths;
        $this->deathsPercent = $percent;
    }

    /**
     * @return mixed
     */
    public function getDeathsPercent()
    {
        return $this->deathsPercent;
    }

    /**
     * @param mixed $deathsPercent
     */
    public function setDeathsPercent($deathsPercent): void
    {
        $this->deathsPercent = $deathsPercent;
    }

    /**
     * @return mixed
     */
    public function getRecovered()
    {
        return $this->recovered;
    }

    /**
     * @param mixed $recovered
     */
    public function setRecovered($recovered): void
    {
        $this->recovered = $recovered;
    }

    /**
     * @param integer $recovered
     * @param integer $percent
     */
    public function setRecoveredData($recovered, $percent): void
    {
        $this->recovered = $recovered;
        $this->recoveredPercent = $percent;
    }

    /**
     * @return mixed
     */
    public function getRecoveredPercent()
    {
        return $this->recoveredPercent;
    }

    /**
     * @param mixed $recoveredPercent
     */
    public function setRecoveredPercent($recoveredPercent): void
    {
        $this->recoveredPercent = $recoveredPercent;
    }

    /**
     * @return mixed
     */
    public function getSerious()
    {
        return $this->serious;
    }

    /**
     * @param mixed $serious
     */
    public function setSerious($serious): void
    {
        $this->serious = $serious;
    }

    /**
     * @param integer $serious
     * @param integer $percent
     */
    public function setSeriousData($serious, $percent): void
    {
        $this->serious = $serious;
        $this->seriousPercent = $percent;
    }

    /**
     * @return mixed
     */
    public function getSeriousPercent()
    {
        return $this->seriousPercent;
    }

    /**
     * @param mixed $seriousPercent
     */
    public function setSeriousPercent($seriousPercent): void
    {
        $this->seriousPercent = $seriousPercent;
    }

    public function getCountryCase(): ?CountryCase
    {
        return $this->countryCase;
    }

    public function setCountryCase(?CountryCase $countryCase): self
    {
        $this->countryCase = $countryCase;

        // set (or unset) the owning side of the relation if necessary
        $newCasesChange = null === $countryCase ? null : $this;
        if ($countryCase->getCasesChange() !== $newCasesChange) {
            $countryCase->setCasesChange($newCasesChange);
        }

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }
}
