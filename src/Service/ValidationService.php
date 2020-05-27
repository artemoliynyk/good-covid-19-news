<?php
/**
 * Artem Oliynyk, <artem@readmire.com>
 * Date: 03/04/20
 */

namespace App\Service;


use App\Entity\Country;
use App\Entity\CountryCase;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ValidationService
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
     * @param LoggerInterface        $validationLogger
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $validationLogger)
    {
        $this->em = $em;

        // repos
        $this->repoCountry = $this->em->getRepository(Country::class);
        $this->repoCountryCase = $this->em->getRepository(CountryCase::class);

        $this->logger = $validationLogger;
    }

    public function verifyCountryData()
    {
        $countries = $this->repoCountry->getAllOrdered();

        foreach ($countries as $country) {
            $countryCases = $this->repoCountryCase->getAll($country);

            /** @var CountryCase $yesterday */
            $yesterday = null;
            $today = null;

            $checkFields = ['total', 'deaths', 'recovered'];

            $step = 0;
            foreach ($countryCases as $tomorrow) {
                $step++;

                if ($yesterday && $today) {
                    foreach ($checkFields as $field) {
                        $valueTomorrow = $tomorrow->{"get{$field}"}();
                        $valueToday = $today->{"get{$field}"}();
                        $valueYesterday = $yesterday->{"get{$field}"}();

                        $this->logger->info("{$valueYesterday} / {$valueToday} / {$valueTomorrow}\n");

                        // set today's value to yesterday's if today greater than tomorrow and greated than yesterday
                        if ($valueYesterday < $valueTomorrow) {

                            $todayIsGreater = $valueYesterday < $valueToday && $valueToday > $valueTomorrow;
                            $todayIsLower = $valueYesterday > $valueToday && $valueToday < $valueTomorrow;

                            if ($todayIsGreater || $todayIsLower) {
                                // Log data
                                $logString = sprintf('Wrong %s data for %s %s / %s: %d vs. %d – set to %d',
                                    $country->getName(),
                                    $field,

                                    $today->getCaseDate()->format(StatisticsService::DATE_FORMAT),
                                    $tomorrow->getCaseDate()->format(StatisticsService::DATE_FORMAT),

                                    $valueToday,
                                    $valueTomorrow,
                                    $valueYesterday
                                );
                                $this->logger->info($logString);

                                $logString = sprintf("Set value %d from %s\n",
                                    $valueYesterday,
                                    $yesterday->getCaseDate()->format(StatisticsService::DATE_FORMAT)
                                );
                                $this->logger->info($logString);


                                // update data, set current same as previous
                                $today->{"set{$field}"}($valueYesterday);
                                $this->em->persist($today);
                            }
                        }
                    }
                }


                if ($step == count($countryCases) - 1) {
                    break;
                }

                if ($today) {
                    $yesterday = $today;
                }

                $today = $tomorrow;
            }
        }

        try {
            $this->em->flush();
            $this->em->clear();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}