<?php
/**
 * Artem Oliynyk, <artem@readmire.com>
 * Date: 03/04/20
 *
 * Class return formatted data for charts
 */

namespace App\Service;


use App\Entity\Country;
use App\Entity\CountryCase;
use App\Entity\DailyStat;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChartDataService
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

    private $worldPopulation;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * StatisticsService constructor.
     *
     * @param EntityManagerInterface $em
     * @param LoggerInterface        $statsLogger
     */
    public function __construct(EntityManagerInterface $em, $worldPopulation, TranslatorInterface $translator)
    {
        $this->em = $em;

        // repos
        $this->repoDailyStat = $this->em->getRepository(DailyStat::class);
        $this->repoCountry = $this->em->getRepository(Country::class);
        $this->repoCountryCase = $this->em->getRepository(CountryCase::class);
        $this->worldPopulation = $worldPopulation;
        $this->translator = $translator;
    }

    public function getTotalToPopulation(): array
    {
        $lastRecord = $this->repoDailyStat->getLastRecord();

        $data = [
            $this->translator->trans('Active cases') => $lastRecord->getActive(),
            $this->translator->trans('World population') => $this->worldPopulation,
        ];

        return $data;
    }

    public function getActive()
    {
        $cases = [];
        $lastRecord = $this->repoDailyStat->getOrdered();
        foreach ($lastRecord as $dayData) {
            $day = $this->translator->trans('chart_date', ['date' => $dayData->getDailyDate()]);
            $cases[$day] = $dayData->getActive();
        }

        $data = [
            'title' => $this->translator->trans('Active cases'),
            'data' => $cases,
        ];

        return $data;
    }

    public function getRecovered()
    {
        $cases = [];
        $lastRecord = $this->repoDailyStat->getOrdered();
        foreach ($lastRecord as $dayData) {
            $day = $this->translator->trans('chart_date', ['date' => $dayData->getDailyDate()]);
            $cases[$day] = $dayData->getRecovered();
        }

        $data = [
            'title' => $this->translator->trans('Recovered'),
            'data' => $cases,
        ];

        return $data;
    }

    public function getNewCasesDaily()
    {
        $cases = [];
        $lastRecord = $this->repoDailyStat->getOrdered();
        foreach ($lastRecord as $dayData) {
            $day = $this->translator->trans('chart_date', ['date' => $dayData->getDailyDate()]);
            $cases[$day] = $dayData->getNewCases();
        }

        $data = [
            'title' => $this->translator->trans('New infected'),
            'data' => $cases,
        ];

        return $data;
    }

    public function getNewRecoveredDaily()
    {
        $cases = [];
        $lastRecord = $this->repoDailyStat->getOrdered();
        foreach ($lastRecord as $dayData) {
            $day = $this->translator->trans('chart_date', ['date' => $dayData->getDailyDate()]);
            $cases[$day] = $dayData->getNewRecovered();
        }

        $data = [
            'title' => $this->translator->trans('New recovered'),
            'data' => $cases,
        ];

        return $data;
    }


    public function getCountryActive(Country $country)
    {
        $cases = [];
        $lastRecord = $this->repoCountryCase->getCountryOrdered($country);
        foreach ($lastRecord as $dayData) {
            $day = $this->translator->trans('chart_date', ['date' => $dayData->getCaseDate()]);
            $cases[$day] = $dayData->getActive();
        }

        $data = [
            'title' => $this->translator->trans('Active cases'),
            'data' => $cases,
        ];

        return $data;
    }

    public function getCountryRecovered(Country $country)
    {
        $cases = [];
        $lastRecord = $this->repoCountryCase->getCountryOrdered($country);
        foreach ($lastRecord as $dayData) {
            $day = $this->translator->trans('chart_date', ['date' => $dayData->getCaseDate()]);
            $cases[$day] = $dayData->getRecovered();
        }

        $data = [
            'title' => $this->translator->trans('Recovered'),
            'data' => $cases,
        ];

        return $data;
    }

    public function getCountryNewCasesDaily(Country $country)
    {
        $cases = [];
        $lastRecord = $this->repoCountryCase->getCountryOrdered($country);
        foreach ($lastRecord as $dayData) {
            $day = $this->translator->trans('chart_date', ['date' => $dayData->getCaseDate()]);
            $cases[$day] = $dayData->getNewCases();
        }

        $data = [
            'title' => $this->translator->trans('New infected'),
            'data' => $cases,
        ];

        return $data;
    }

    public function getCountryNewRecoveredDaily(Country $country)
    {
        $cases = [];
        $lastRecord = $this->repoCountryCase->getCountryOrdered($country);
        foreach ($lastRecord as $dayData) {
            $day = $this->translator->trans('chart_date', ['date' => $dayData->getCaseDate()]);
            $cases[$day] = $dayData->getNewRecovered();
        }

        $data = [
            'title' => $this->translator->trans('New recovered'),
            'data' => $cases,
        ];

        return $data;
    }

}