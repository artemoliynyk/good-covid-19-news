<?php

namespace App\Controller;

use App\Repository\CountryCaseRepository;
use App\Repository\CountryRepository;
use App\Repository\DailyStatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="index")
     *
     * @param DailyStatRepository $dailyStatRepository
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(DailyStatRepository $dailyStatRepository, CountryRepository $countryRepository, CountryCaseRepository $countryCaseRepository)
    {
        $worldPopulation = $this->getParameter('world_population');
        $lastRecord = $dailyStatRepository->getLastRecord();
        $prevRecord = $dailyStatRepository->getPrevDayStat($lastRecord);
        $percent = round($lastRecord->getActive() / $worldPopulation * 100, 2);

        $lastUpdateDate = $countryRepository->getLastUpdateAt();

        $topCountries = $countryCaseRepository->getTopCountries();

        return $this->render('main/index.html.twig', [
            'world_population' => $worldPopulation,
            'last_record' => $lastRecord,
            'last_update_date' => $lastUpdateDate,

            'prev_record' => $prevRecord,
            'percent' => $percent,


            'top_countries' => $topCountries,
        ]);
    }

}
