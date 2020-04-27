<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CountryCaseRepository")
 * @ORM\Table(name="country_cases", uniqueConstraints={@UniqueConstraint(columns={"case_date", "country_id"})})
 * @UniqueEntity(fields={"caseDate", "country"})
 */
class CountryCase
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country", inversedBy="countryCase", cascade={"persist"})
     * @ORM\JoinColumn(name="country_id", onDelete="CASCADE")
     */
    private $country;

    /**
     * @ORM\Column(name="case_date", type="datetime")
     */
    private $caseDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $total;

    /**
     * @ORM\Column(type="integer")
     */
    private $deaths;

    /**
     * @ORM\Column(type="integer")
     */
    private $recovered;

    /**
     * @ORM\Column(type="integer")
     */
    private $newDeaths;

    /**
     * @ORM\Column(type="integer")
     */
    private $newCases;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": null})
     */
    private $newRecovered;

    /**
     * @ORM\Column(type="integer")
     */
    private $serious;

    /**
     * @ORM\Column(type="integer")
     */
    private $active;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalPer1m;

    /**
     * @ORM\OneToOne(targetEntity="CasesChange", inversedBy="countryCase", cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $casesChange;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCaseDate(): \DateTime
    {
        return $this->caseDate;
    }

    /**
     * @param \DateTime $caseDate
     */
    public function setCaseDate(\DateTime $caseDate): void
    {
        $this->caseDate = $caseDate;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
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
     * @return mixed
     */
    public function getNewDeaths()
    {
        return $this->newDeaths;
    }

    /**
     * @param mixed $newDeaths
     */
    public function setNewDeaths($newDeaths): void
    {
        $this->newDeaths = $newDeaths;
    }

    /**
     * @return mixed
     */
    public function getNewCases()
    {
        return $this->newCases;
    }

    /**
     * @param mixed $newCases
     */
    public function setNewCases($newCases): void
    {
        $this->newCases = $newCases;
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
     * @return mixed
     */
    public function getNewRecovered()
    {
        return $this->newRecovered;
    }

    /**
     * @param mixed $newRecovered
     */
    public function setNewRecovered($newRecovered): void
    {
        $this->newRecovered = $newRecovered;
    }


    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active): void
    {
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getTotalPer1m()
    {
        return $this->totalPer1m;
    }

    /**
     * @param mixed $totalPer1m
     */
    public function setTotalPer1m($totalPer1m): void
    {
        $this->totalPer1m = $totalPer1m;
    }


    public function loadJSON(Country $country, \DateTime $statDate, object $jsonResponse, callable $numberFormatFunction)
    {
        $this->country = $country;
        $this->caseDate = $statDate;

        $this->total = $numberFormatFunction((isset($jsonResponse->cases) ? $jsonResponse->cases : $jsonResponse->total_cases));
        $this->deaths = $numberFormatFunction((isset($jsonResponse->deaths) ? $jsonResponse->deaths : $jsonResponse->total_deaths));

        $this->recovered = $numberFormatFunction($jsonResponse->total_recovered);
        $this->newDeaths = $numberFormatFunction($jsonResponse->new_deaths);
        $this->newCases = $numberFormatFunction($jsonResponse->new_cases);
        $this->serious = $numberFormatFunction($jsonResponse->serious_critical);
        $this->active = $numberFormatFunction($jsonResponse->active_cases);

        $this->totalPer1m = $numberFormatFunction((isset($jsonResponse->total_cases_per1m) ? $jsonResponse->total_cases_per1m : $jsonResponse->total_cases_per_1m_population));
    }

    public function getCasesChange(): ?CasesChange
    {
        return $this->casesChange;
    }

    public function setCasesChange(?CasesChange $casesChange): self
    {
        $this->casesChange = $casesChange;

        return $this;
    }
}
