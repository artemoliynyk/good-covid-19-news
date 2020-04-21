<?php

namespace App\Controller;

use App\Repository\CountryCaseRepository;
use App\Repository\DailyStatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(DailyStatRepository $dailyStatRepository)
    {
        $worldPopulation = $this->getParameter('world_population');
        $lastRecord = $dailyStatRepository->getLastRecord();
        $prevRecord = $dailyStatRepository->getPrevDayStat($lastRecord);

        $percent = round($lastRecord->getActive() / $worldPopulation * 100, 2);

        return $this->render('main/index.html.twig', [
            'world_population' => $worldPopulation,
            'last_record' => $lastRecord,
            'prev_record' => $prevRecord,
            'prev_record_date' => $prevRecord->getDay()->format('d F, Y'),
            'percent' => $percent,
        ]);
    }

    /**
     * @Route("/countries", name="main_countries")
     */
    public function countriesAction(CountryCaseRepository $countryCaseRepository, DailyStatRepository $dailyStatRepository)
    {
        $lastRecord = $dailyStatRepository->getLastRecord();

        $countryCases = $countryCaseRepository->getAllCounries($lastRecord->getDay());

        return $this->render('main/countries.html.twig', [
            'last_record' => $lastRecord,
            'country_cases' => $countryCases,
        ]);
    }

}
