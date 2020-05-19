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

            /** @var CountryCase $previousCase */
            $previousCase = null;

            $checkFields = ['total', 'deaths', 'recovered'];

            $changes = 0;
            foreach ($countryCases as $countryCase) {

                if (!is_null($previousCase)) {

                    foreach ($checkFields as $field) {
                        $currentValue = $countryCase->{"get{$field}"}();
                        $prevValue = $previousCase->{"get{$field}"}();

                        if ($prevValue > $currentValue) {
                            // Log data
                            $logString = sprintf('Wrong %s data for %s %s / %s: %d vs. %d',
                                $country->getName(),
                                $field,

                                $previousCase->getCaseDate()->format(StatisticsService::DATE_FORMAT),
                                $countryCase->getCaseDate()->format(StatisticsService::DATE_FORMAT),

                                $prevValue,
                                $currentValue
                            );
                            $this->logger->info($logString);

                            // update data, set current same as previous
                            $countryCase->{"set{$field}"}($prevValue);
                            $this->em->persist($countryCase);
                            $changes++;
                        }
                    }
                }

                $previousCase = $countryCase;
            }

            if ($changes > 0) {
                $this->em->flush();
            }
        }

        return true;
    }
}