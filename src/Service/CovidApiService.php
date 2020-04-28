<?php
/**
 * Artem Oliynyk, <artem@readmire.com>
 * Date: 03/04/20
 */

namespace App\Service;

use App\Entity\Country;
use App\Entity\CountryCase;
use App\Entity\DailyStat;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class CovidApiService
{

    const URL_WORLDSTAT = 'https://coronavirus-monitor.p.rapidapi.com/coronavirus/worldstat.php';
    const URL_BY_COUNTRY = 'https://coronavirus-monitor.p.rapidapi.com/coronavirus/cases_by_country.php';
    const URL_ARCHIVE_BY_COUNTRY = 'https://coronavirus-monitor.p.rapidapi.com/coronavirus/cases_by_particular_country.php';
    const URL_ARCHIVE_BY_COUNTRY_DATE = 'https://coronavirus-monitor.p.rapidapi.com/coronavirus/history_by_particular_country_by_date.php';

    /**
     * @var Client
     */
    private $guzzleClient;

    /**
     * @var EntityManagerInterface
     */
    private $em;
    private $rapidapiKey;

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
     * CovidApiService constructor.
     *
     * @param ClientInterface $guzzleClient
     * @param                 $rapidapiKey
     */
    public function __construct($rapidapiKey, EntityManagerInterface $em, LoggerInterface $covidLogger)
    {
        $this->em = $em;
        $this->guzzleClient = new Client();
        $this->rapidapiKey = $rapidapiKey;

        // repos
        $this->repoDailyStat = $this->em->getRepository(DailyStat::class);
        $this->repoCountry = $this->em->getRepository(Country::class);
        $this->repoCountryCase = $this->em->getRepository(CountryCase::class);
        $this->logger = $covidLogger;
    }


    public static function prepareNumber(string $string)
    {
        return str_replace(",", "", $string);
    }

    private function rapidApiRequest(string $url, array $options = [], array $headers = []): ResponseInterface
    {
        $reqHeaders = ["x-rapidapi-key" => $this->rapidapiKey];
        $reqHeaders = array_merge($reqHeaders, $headers);

        $reqOptions = ['headers' => $reqHeaders];
        $reqOptions = array_merge($reqOptions, $options);

        return $this->guzzleClient->request('GET', $url, $reqOptions);
    }

    private function saveCountryStat(\DateTime $statDate, Country $country, $countryData)
    {
        $cases = $this->repoCountryCase->getForCountryByDate($statDate, $country);

        if (!$cases instanceof CountryCase) {
            $cases = new CountryCase();
        } else {
            $this->logger->info(
                sprintf('Stat for country %s by day "%s" already present, updating...',
                    $country->getName(),
                    $statDate->format(StatisticsService::DATE_FORMAT)
                )
            );
        }

        $cases->loadJSON($country, $statDate, $countryData, [self::class, 'prepareNumber']);

        try {
            $this->em->persist($cases);
            $this->em->flush();

            $this->logger->info(
                sprintf('Country %s daily stat by %s saved', $country->getName(), $statDate->format('r'))
            );
        } catch (\Exception $exception) {
            $this->logger->error(
                sprintf('Unable to country %s daily stat by %s due to error: %s',
                    $country->getName(),
                    $statDate->format(StatisticsService::DATE_FORMAT),
                    $exception->getMessage()
                )
            );

            return false;
        }

        return true;
    }

    public function getLatestCountriesStat()
    {
        $response = $this->rapidApiRequest(self::URL_BY_COUNTRY);

        if (200 == $response->getStatusCode()) {
            $bodyRaw = (string) $response->getBody();
            $jsonResponse = json_decode($bodyRaw);

            $statDate = new \DateTime($jsonResponse->statistic_taken_at);

            if (empty($jsonResponse->countries_stat) || !is_array($jsonResponse->countries_stat)) {
                return false;
            }

            foreach ($jsonResponse->countries_stat as $countryData) {
                if (!empty($countryData->country_name)) {
                    $country = $this->repoCountry->findOrCreate($countryData->country_name);
                    $country->setUpdatedAt($statDate);

                    // process country data
                    $processed = $this->saveCountryStat($statDate, $country, $countryData);

                    // terminate of fatal error
                    if (!$processed) {
                        return false;
                    }
                }
            }

        }

        return true;
    }

    public function getAllTimeCountriesStat()
    {

        $countries = $this->repoCountry->getAllOrdered();

        $processedDates = [];

        foreach ($countries as $country) {

            $countryName = $country->getName();

            if (empty($countryName)) {
                continue;
            }

            $requestURL = sprintf(self::URL_ARCHIVE_BY_COUNTRY."?country=%s", urlencode($countryName));

            $response = $this->rapidApiRequest($requestURL);
            if (200 == $response->getStatusCode()) {
                $bodyRaw = (string) $response->getBody();
                $jsonResponse = json_decode($bodyRaw);

                // terminate if no data
                if (empty($jsonResponse->stat_by_country) || !is_array($jsonResponse->stat_by_country)) {
                    return false;
                }

                // reverse data to get oldest info first
                $dataReversed = array_reverse($jsonResponse->stat_by_country);
                foreach ($dataReversed as $countryData) {
                    // make sure country date are for correct country ( for e.g.: 'Netherlands' not 'Caribbean Netherlands'
                    if ($countryData->country_name == $countryName) {

                        $recordDate = new \DateTime($countryData->record_date);
                        $dateDay = $recordDate->format('Y-m-d');

                        // skip processed date
                        if (isset($processedDates[$countryName][$dateDay])) {
                            continue;
                        }

                        $processedDates[$countryName][$dateDay] = $dateDay;

                        // process country data
                        $processed = $this->saveCountryStat($recordDate, $country, $countryData);

                        // terminate of fatal error or break on success
                        if (!$processed) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    public function getDateCountriesStat(string $date)
    {

        $countries = $this->repoCountry->getAllOrdered();

        foreach ($countries as $country) {

            $countryName = $country->getName();

            if (empty($countryName)) {
                continue;
            }

            $requestURL = sprintf(self::URL_ARCHIVE_BY_COUNTRY_DATE."?country=%s&date=%s", urlencode($countryName), $date);

            $response = $this->rapidApiRequest($requestURL);
            if (200 == $response->getStatusCode()) {
                $bodyRaw = (string) $response->getBody();
                $jsonResponse = json_decode($bodyRaw);

                // terminate if no data
                if (empty($jsonResponse->stat_by_country) || !is_array($jsonResponse->stat_by_country)) {
                    $this->logger->error(sprintf('Daily was not received: "%s"', $bodyRaw));

                    return false;
                }

                // reverse data to get oldest info first
                $countryData = end($jsonResponse->stat_by_country);

                // make sure country date are for correct country ( for e.g.: 'Netherlands' not 'Caribbean Netherlands'
                if ($countryData->country_name == $countryName) {

                    $recordDate = new \DateTime($countryData->record_date);
                    $dateDay = $recordDate->format('Y-m-d');

                    // process country data
                    $processed = $this->saveCountryStat($recordDate, $country, $countryData);

                    // terminate of fatal error or break on success
                    if (!$processed) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}