<?php

namespace App\Entity;

use App\Entity\Traits\ChangeTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DailyChangeRepository")
 * @ORM\Table()
 */
class DailyChange
{
    use ChangeTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @var int
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
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
