<?php
/**
 * Artem Oliynyk, <artem@readmire.com>
 * Date: 03/04/20
 */

namespace App\Service;


use App\Entity\Country;
use App\Entity\CountryCase;
use App\Entity\CountryCasesChange;
use App\Entity\DailyChange;
use App\Entity\DailyStat;
use App\Repository\DailyStatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class StatisticsService
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

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
     * @param LoggerInterface        $statsLogger
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
            // update records with no new recovery data
            $noDataRecords = $this->repoCountryCase->getNoRecoveredRecords($country);

            // also update last records
            $lastRecords = $this->repoCountryCase->getLastByCountryWithChange($country);
            if ($lastRecords instanceof CountryCase) {
                array_push($noDataRecords, $lastRecords);
            }
            $processCases = array_unique($noDataRecords);

            if (!empty($processCases)) {

                /** @var CountryCase $noDataDay */
                foreach ($processCases as $noDataDay) {
                    $prevDay = $this->repoCountryCase->getCasesPrevDay($noDataDay);

                    $recoveredChange = 0;
                    if (!is_null($prevDay)) {
                        $dailyChange = $noDataDay->getRecovered() - $prevDay->getRecovered();
                        $recoveredChange = ($dailyChange >= 0 ? $dailyChange : 0);
                    }
                    $noDataDay->setNewRecovered($recoveredChange);
                    $this->em->persist($noDataDay);

                    $this->logger->info("{$country->getName()} recovered change for {$noDataDay->getCaseDate()->format(StatisticsService::DATE_FORMAT)}: {$recoveredChange} staged");
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
        if ($noRecovered > 0) {
            $this->logger->error("Unable to calculate totals, some countries has missing new recovered data. Please run `app:stats:calculate recovered` first");

            return false;
        }

        $countries = $this->repoCountry->getAllOrdered();

        /** @var Country $country */
        foreach ($countries as $country) {
            // update records with no new change data
            $casesWithoutChange = $this->repoCountryCase->getNoChangeRecords($country);

            // also update last records
            $lastRecord = $this->repoCountryCase->getLastByCountryWithChange($country);
            if ($lastRecord instanceof CountryCase) {
                array_push($casesWithoutChange, $lastRecord);
            }
            $processCases = array_unique($casesWithoutChange);

            /** @var CountryCase $currentDay */
            $changes = 0;
            foreach ($processCases as $currentDay) {
                $prevDay = $this->repoCountryCase->getCasesPrevDay($currentDay);

                $caseChange = $currentDay->getCasesChange() ?? new CountryCasesChange($currentDay);

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


                $dayStat = $this->repoDailyStat->getByDate($currentDate);
                if (is_null($dayStat)) {
                    $dayStat = new DailyStat($currentDate);
                    $this->em->persist($dayStat);
                }

                $changes++;
                $this->em->persist($currentDay);
                $this->logger->info("{$country->getName()} daily change for {$currentDate->format(StatisticsService::DATE_FORMAT)} staged");
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
        if ($noRecovered > 0) {
            $this->logger->error("Unable to calculate totals, some countries has missing new recovered data. Please run `app:stats:calculate recovered` first");

            return false;
        }

        /** @var DailyStatRepository[] $noChangeDays */
        $noChangeDays = (array) $this->repoDailyStat->getNoChangeDays();

        // add current day
        $lastDay = $this->repoDailyStat->getLastRecord();
        if ($lastDay instanceof DailyStat) {
            array_push($noChangeDays, $lastDay);
        }
        $processDays = array_unique($noChangeDays);

        /** @var DailyStat $currentDay */
        foreach ($processDays as $currentDay) {
            $currentDate = $currentDay->getDailyDate();

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

                $this->logger->info("Daily total for {$currentDate->format(StatisticsService::DATE_FORMAT)} SAVED!\n");
            } catch (\Exception $exception) {
                $this->logger->error("Unable to save daily total for {$currentDate->format(StatisticsService::DATE_FORMAT)} due to an error: {$exception->getMessage()}");

                return false;
            }
        }

        return true;
    }

    public function calculateDailyChange()
    {
        /** @var DailyStat[] $noChangeDays */
        $noChangeDays = (array) $this->repoDailyStat->getNoChangeDays();

        // add current day
        $lastDay = $this->repoDailyStat->getLastRecord();
        if ($lastDay instanceof DailyStat) {
            array_push($noChangeDays, $lastDay);
        }
        $processDays = array_unique($noChangeDays);

        /** @var DailyStat $currentDay */
        $changes = 0;
        foreach ($processDays as $currentDay) {
            $dailyChange = $currentDay->getDailyChange() ?? new DailyChange($currentDay);

            $currentDate = $currentDay->getDailyDate();
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

            $this->logger->info("Daily change for {$currentDate->format(StatisticsService::DATE_FORMAT)} staged");

        }

        if (0 < $changes) {
            try {
                $this->em->flush();
                $this->em->clear();

                $this->logger->info("Daily change for {$currentDate->format(StatisticsService::DATE_FORMAT)} SAVED!\n");
            } catch (\Exception $exception) {
                $this->logger->error("Unable to save daily chane for {$currentDate->format(StatisticsService::DATE_FORMAT)} due to an error: {$exception->getMessage()}");

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