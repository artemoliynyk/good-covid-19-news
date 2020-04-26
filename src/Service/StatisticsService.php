<?php
/**
 * Artem Oliynyk, <artem@readmire.com>
 * Date: 03/04/20
 */

namespace App\Service;


use App\Entity\CasesChange;
use App\Entity\Country;
use App\Entity\CountryCase;
use App\Entity\DailyChange;
use App\Entity\DailyStat;
use App\Repository\DailyStatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class StatisticsService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var \App\Repository\DailyStatRepository
     */
    private $repoDailyStat;

    /**
     * @var \App\Repository\CountryRepository
     */
    private $repoCountry;

    /**
     * @var \App\Repository\CountryCaseRepository
     */
    private $repoCountryCase;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * StatisticsService constructor.
     *
     * @param EntityManagerInterface $em
     * @param LoggerInterface $statsLogger
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $statsLogger)
    {
        $this->em = $em;

        // repos
        $this->repoDailyStat = $this->em->getRepository(DailyStat::class);
        $this->repoCountry = $this->em->getRepository(Country::class);
        $this->repoCountryCase = $this->em->getRepository(CountryCase::class);

        $this->logger = $statsLogger;
    }


    public function calculateNewRecoveredByCountry()
    {
        $countries = $this->repoCountry->getAllOrdered();

        /** @var Country $country */
        foreach ($countries as $country) {
            $noDataRecords = $this->repoCountryCase->getNoRecoveredRecords($country);

            if (!empty($noDataRecords)) {

                /** @var CountryCase $noDataDay */
                foreach ($noDataRecords as $noDataDay) {
                    $prevDay = $this->repoCountryCase->getCasesPrevDay($noDataDay);

                    $recoveredChange = 0;
                    if (!is_null($prevDay)) {
                        $dailyChange = $noDataDay->getRecovered() - $prevDay->getRecovered();
                        $recoveredChange = ($dailyChange >= 0 ? $dailyChange : 0);
                    }
                    $noDataDay->setNewRecovered($recoveredChange);

                    $this->logger->info("{$country->getName()} recovered change for {$noDataDay->getCaseDate()->format('r')}: {$recoveredChange} staged");
                    $this->em->persist($noDataDay);
                }

                try {
                    $this->em->flush();
                    $this->em->clear();

                    $this->logger->info("{$country->getName()} recovered changes SAVED!\n");
                } catch (\Exception $exception) {
                    $this->logger->error("Unable to save {$country->getName()} recovered changes due to an error: {$exception->getMessage()}");

                    return false;
                }
            }
        }

        return true;
    }

    public function calculateCountryChange()
    {
        $noRecovered = $this->repoCountryCase->count(['newRecovered' => null]);

        // do not calculate changes if no recovered data available
        if( $noRecovered > 0) {
            $this->logger->error("Unable to calculate totals, some countries has missing new recovered data. Please run `app:stats:calculate recovered` first");
            return false;
        }

        $countries = $this->repoCountry->getAllOrdered();

        /** @var Country $country */
        foreach ($countries as $country) {
            $casesWithoutChange = $this->repoCountryCase->getNoChangeRecords($country);

            /** @var CountryCase $currentDay */
            $changes = 0;
            foreach ($casesWithoutChange as $currentDay) {
                $prevDay = $this->repoCountryCase->getCasesPrevDay($currentDay);

                $caseChange = new CasesChange($currentDay);
                $currentDate = $currentDay->getCaseDate();

                if (!is_null($prevDay)) {
                    // new cases change
                    $newChange = $currentDay->getNewCases() - $prevDay->getNewCases();
                    $newChangePerc = self::calculatePercentChange($currentDay->getNewCases(), $prevDay->getNewCases());
                    $caseChange->setNewData($newChange, $newChangePerc);

                    // new death change
                    $deathChange = $currentDay->getNewDeaths() - $prevDay->getNewDeaths();
                    $deathChangePerc = self::calculatePercentChange($currentDay->getNewDeaths(), $prevDay->getNewDeaths());
                    $caseChange->setDeathsData($deathChange, $deathChangePerc);

                    // new recovered change
                    $recoveredChange = $currentDay->getNewRecovered() - $prevDay->getNewRecovered();
                    $recoveredChangePerc = self::calculatePercentChange($currentDay->getNewRecovered(), $prevDay->getNewRecovered());
                    $caseChange->setRecoveredData($recoveredChange, $recoveredChangePerc);

                    // new serious change
                    $seriousChange = $currentDay->getSerious() - $prevDay->getSerious();
                    $seriousChangePerc = self::calculatePercentChange($currentDay->getSerious(), $prevDay->getSerious());
                    $caseChange->setSeriousData($seriousChange, $seriousChangePerc);
                }


                $dayStat = $this->repoDailyStat->findOneBy(['day' => $currentDate]);
                if (is_null($dayStat)) {
                    $dayStat = new DailyStat($currentDate);
                    $this->em->persist($dayStat);
                }

                $changes++;
                $this->em->persist($currentDay);
                $this->logger->info("{$country->getName()} daily change for {$currentDate->format('r')} staged");
            }

            if (0 < $changes) {
                try {
                    $this->em->flush();
                    $this->em->clear();

                    $this->logger->info("{$country->getName()} daily changes SAVED!\n");
                } catch (\Exception $exception) {
                    $this->logger->error("Unable to save {$country->getName()} daily changes due to an error: {$exception->getMessage()}");

                    return false;
                }
            }
        }

        return true;
    }

    public function calculateDailyTotal()
    {
        $noRecovered = $this->repoCountryCase->count(['newRecovered' => null]);

        // do not calculate changes if no recovered data available
        if( $noRecovered > 0) {
            $this->logger->error("Unable to calculate totals, some countries has missing new recovered data. Please run `app:stats:calculate recovered` first");
            return false;
        }

        /** @var DailyStatRepository $noChangeDays */
        $noChangeDays = $this->repoDailyStat->getNoChangeDays();

        /** @var DailyStat $currentDay */
        foreach ($noChangeDays as $currentDay) {
            $currentDate = $currentDay->getDay();

            $dayTotals = $this->repoCountryCase->getDayTotals($currentDate);

            $currentDay->setTotal($dayTotals['total']);
            $currentDay->setRecovered($dayTotals['recovered']);
            $currentDay->setDeaths($dayTotals['deaths']);
            $currentDay->setNewDeaths($dayTotals['newDeaths']);
            $currentDay->setNewCases($dayTotals['newCases']);
            $currentDay->setNewRecovered($dayTotals['newRecovered']);
            $currentDay->setSerious($dayTotals['serious']);
            $currentDay->setActive($dayTotals['active']);

            try {
                $this->em->persist($currentDay);
                $this->em->flush();
                $this->em->clear();

                $this->logger->info("Daily total for {$currentDate->format('r')} SAVED!\n");
            } catch (\Exception $exception) {
                $this->logger->error("Unable to save daily total for {$currentDate->format('r')} due to an error: {$exception->getMessage()}");

                return false;
            }
        }

        return true;
    }

    public function calculateDailyChange()
    {
        /** @var DailyStat[] $noChangeDays */
        $noChangeDays = $this->repoDailyStat->getNoChangeDays();

        /** @var DailyStat $currentDay */
        $changes = 0;
        foreach ($noChangeDays as $currentDay) {
            $dailyChange = new DailyChange($currentDay);

            $currentDate = $currentDay->getDay();
            $prevDay = $this->repoDailyStat->getPrevDayStat($currentDay);

            if (!is_null($prevDay)) {

                // new cases change
                $newChange = $currentDay->getNewCases() - $prevDay->getNewCases();
                $newChangePerc = self::calculatePercentChange($currentDay->getNewCases(), $prevDay->getNewCases());
                $dailyChange->setNewData($newChange, $newChangePerc);

                // new death change
                $deathChange = $currentDay->getNewDeaths() - $prevDay->getNewDeaths();
                $deathChangePerc = self::calculatePercentChange($currentDay->getNewDeaths(), $prevDay->getNewDeaths());
                $dailyChange->setDeathsData($deathChange, $deathChangePerc);

                // new recovered change
                $recoveredChange = $currentDay->getNewRecovered() - $prevDay->getNewRecovered();
                $recoveredChangePerc = self::calculatePercentChange($currentDay->getNewRecovered(), $prevDay->getNewRecovered());
                $dailyChange->setRecoveredData($recoveredChange, $recoveredChangePerc);

                // new serious change
                $seriousChange = $currentDay->getSerious() - $prevDay->getSerious();
                $seriousChangePerc = self::calculatePercentChange($currentDay->getSerious(), $prevDay->getSerious());
                $dailyChange->setSeriousData($seriousChange, $seriousChangePerc);
            }

            $this->em->persist($currentDay);
            $changes++;

            $this->logger->info("Daily change for {$currentDate->format('r')} staged");

        }

        if (0 < $changes) {
            try {
                $this->em->flush();
                $this->em->clear();

                $this->logger->info("Daily change for {$currentDate->format('r')} SAVED!\n");
            } catch (\Exception $exception) {
                $this->logger->error("Unable to save daily chane for {$currentDate->format('r')} due to an error: {$exception->getMessage()}");

                return false;
            }
        }

        return true;
    }

    public static function calculatePercentChange(int $currentDay, int $prevDay, ?LoggerInterface $logger = null)
    {
        $modifier = 1;

        $original = $currentDay;
        $change = $prevDay;

        if ($prevDay == $currentDay) {
            $perc = 0;
        } else {
            if ($prevDay > $currentDay) {
                $modifier = -1;

                $original = $prevDay;
                $change = $currentDay;
            }

            // no division by zero
            if (0 === $change) {
                $perc = 0;
            } else {
                $perc = ceil(($original / $change - 1) * 100) * $modifier;
            }
        }

        if (!is_null($logger)) {
            $logger->info("{$currentDay} vs {$prevDay}: {$original} / {$change} = {$perc}%");
        }

        return $perc;
    }
}