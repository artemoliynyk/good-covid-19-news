<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DailyChangeRepository")
 * @ORM\Table()
 */
class DailyChange
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var DailyStat
     *
     * @ORM\OneToOne(targetEntity="DailyStat", mappedBy="dailyChange")
     * @ORM\JoinColumn(name="day_id", onDelete="CASCADE", unique=true)
     */
    private $dailyStat;

    /**
     * @ORM\Column(type="integer")
     */
    private $new;

    /**
     * @ORM\Column(type="float")
     */
    private $newPercent;

    /**
     * @ORM\Column(type="integer")
     */
    private $deaths;

    /**
     * @ORM\Column(type="float")
     */
    private $deathsPercent;

    /**
     * @ORM\Column(type="integer")
     */
    private $recovered;

    /**
     * @ORM\Column(type="float")
     */
    private $recoveredPercent;

    /**
     * @ORM\Column(type="integer")
     */
    private $serious;

    /**
     * @ORM\Column(type="float")
     */
    private $seriousPercent;

    /**
     * DailyChange constructor.
     *
     * @param $dailyStat
     */
    public function __construct(?DailyStat $dailyStat = null)
    {
        if (!is_null($dailyStat)) {
            $dailyStat->setDailyChange($this);
            $this->dailyStat = $dailyStat;
        }

        $this->new = 0;
        $this->newPercent = 0;
        $this->deaths = 0;
        $this->deathsPercent = 0;
        $this->recovered = 0;
        $this->recoveredPercent = 0;
        $this->serious = 0;
        $this->seriousPercent = 0;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getNew(): int
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
     * @return int
     */
    public function getNewPercent(): int
    {
        return $this->newPercent;
    }

    /**
     * @return int
     */
    public function getNewValue(): ?string
    {
        $value = abs($this->new);
        if ($this->newPercent !== 0) {
            $value = abs($this->newPercent)."%";
        }

        return $value;
    }

    /**
     * @param mixed $newPercent
     */
    public function setNewPercent($newPercent): void
    {
        $this->newPercent = $newPercent;
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
     * @return int
     */
    public function getDeathsPercent(): int
    {
        return $this->deathsPercent;
    }

    /**
     * @return int
     */
    public function getDeathsValue(): ?string
    {
        $value = abs($this->deaths);
        if ($this->deathsPercent !== 0) {
            $value = abs($this->deathsPercent)."%";
        }

        return $value;
    }

    /**
     * @param mixed $deathsPercent
     */
    public function setDeathsPercent($deathsPercent): void
    {
        $this->deathsPercent = $deathsPercent;
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
     * @return int
     */
    public function getRecoveredPercent(): int
    {
        return $this->recoveredPercent;
    }

    /**
     * @return int
     */
    public function getRecoveredValue(): ?string
    {
        $value = abs($this->recovered);
        if ($this->recoveredPercent !== 0) {
            $value = abs($this->recoveredPercent)."%";
        }

        return $value;
    }

    /**
     * @param mixed $recoveredPercent
     */
    public function setRecoveredPercent($recoveredPercent): void
    {
        $this->recoveredPercent = $recoveredPercent;
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
     * @return int
     */
    public function getSeriousPercent(): int
    {
        return $this->seriousPercent;
    }

    /**
     * @return int
     */
    public function getSeriousValue(): ?string
    {
        $value = abs($this->serious);
        if ($this->seriousPercent !== 0) {
            $value = abs($this->seriousPercent)."%";
        }

        return $value;
    }

    /**
     * @param mixed $seriousPercent
     */
    public function setSeriousPercent($seriousPercent): void
    {
        $this->seriousPercent = $seriousPercent;
    }

    public function getDailyStat(): ?DailyStat
    {
        return $this->dailyStat;
    }

    public function setDailyStat(?DailyStat $dailyStat): self
    {
        $this->dailyStat = $dailyStat;

        // set (or unset) the owning side of the relation if necessary
        $newDailyChange = null === $dailyStat ? null : $this;
        if ($dailyStat->getDailyChange() !== $newDailyChange) {
            $dailyStat->setDailyChange($newDailyChange);
        }

        return $this;
    }
}
