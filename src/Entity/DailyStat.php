<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DailyStatRepository")
 */
class DailyStat
{
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", unique=true)
     */
    private $day;

    /**
     * @var integer
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $total;

    /**
     * @var integer
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $deaths;

    /**
     * @var integer
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $recovered;

    /**
     * @var integer
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $newCases;

    /**
     * @var integer
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $newDeaths;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": null})
     */
    private $newRecovered;

    /**
     * @var integer
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $serious;

    /**
     * @var integer
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $active;

    /**
     * @ORM\OneToOne(targetEntity="DailyChange", inversedBy="dailyStat", cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $dailyChange;

    /**
     * DailyStat constructor.
     */
    public function __construct(?\DateTimeInterface $day = null)
    {
        $this->total = 0;
        $this->deaths = 0;
        $this->recovered = 0;
        $this->newCases = 0;
        $this->newDeaths = 0;
        $this->newRecovered = 0;
        $this->serious = 0;
        $this->active = 0;

        if (!is_null($day)) {
            $this->day = $day;
        }
    }


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDay(): \DateTimeInterface
    {
        return $this->day;
    }

    /**
     * @param \DateTime $day
     */
    public function setDay($day): self
    {
        $this->day = $day;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTotal(): ?int
    {
        return $this->total;
    }

    /**
     * @param int $total
     * @return $this
     */
    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return int
     */
    public function getDeaths(): int
    {
        return $this->deaths;
    }

    /**
     * @param mixed $deaths
     */
    public function setDeaths(int $deaths): self
    {
        $this->deaths = $deaths;

        return $this;
    }

    /**
     * @return int
     */
    public function getRecovered(): int
    {
        return $this->recovered;
    }

    /**
     * @param mixed $recovered
     */
    public function setRecovered(int $recovered): self
    {
        $this->recovered = $recovered;

        return $this;
    }

    /**
     * @return int
     */
    public function getNewDeaths(): int
    {
        return $this->newDeaths;
    }

    /**
     * @param mixed $newDeaths
     */
    public function setNewDeaths(int $newDeaths): self
    {
        $this->newDeaths = $newDeaths;

        return $this;
    }

    /**
     * @return int
     */
    public function getNewCases(): int
    {
        return $this->newCases;
    }

    /**
     * @param mixed $newCases
     */
    public function setNewCases(int $newCases): self
    {
        $this->newCases = $newCases;

        return $this;
    }

    /**
     * @return int
     */
    public function getSerious(): int
    {
        return $this->serious;
    }

    /**
     * @param mixed $serious
     */
    public function setSerious(int $serious): self
    {
        $this->serious = $serious;

        return $this;
    }

    /**
     * @return int
     */
    public function getNewRecovered(): int
    {
        return $this->newRecovered;
    }

    /**
     * @param mixed $newRecovered
     */
    public function setNewRecovered(int $newRecovered): self
    {
        $this->newRecovered = $newRecovered;

        return $this;
    }


    /**
     * @return int
     */
    public function getActive(): int
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive(int $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return DailyChange
     */
    public function getDailyChange(): ?DailyChange
    {
        return $this->dailyChange;
    }

    /**
     * @param mixed $dailyChange
     */
    public function setDailyChange($dailyChange): void
    {
        $this->dailyChange = $dailyChange;
    }

    public function loadJSON(object $jsonResponse, callable $numberFormatFunction)
    {
        $this->total = $numberFormatFunction($jsonResponse->total_cases);
        $this->deaths = $numberFormatFunction($jsonResponse->total_deaths);
        $this->recovered = $numberFormatFunction($jsonResponse->total_recovered);
        $this->newCases = $numberFormatFunction($jsonResponse->new_cases);
        $this->newDeaths = $numberFormatFunction($jsonResponse->new_deaths);

        $date = new \DateTime($jsonResponse->statistic_taken_at);
        $this->day = $date;
    }
}
