<?php

namespace App\Controller;

use App\Repository\DailyStatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(DailyStatRepository $dailyStatRepository)
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

}
