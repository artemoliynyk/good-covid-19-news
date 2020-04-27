<?php

namespace App\Controller;

use App\Entity\Country;
use App\Service\ChartDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class JsonController extends AbstractController
{
    /**
     * @var ChartDataService
     */
    private $chartDataService;

    /**
     * JsonController constructor.
     *
     * @param ChartDataService $chartDataService
     */
    public function __construct(ChartDataService $chartDataService)
    {
        $this->chartDataService = $chartDataService;
    }

    /**
     * @Route("/json/daily", name="json_daily")
     */
    public function dailyAction()
    {
        $data = [];

        return new JsonResponse($data);
    }

    /**
     * @Route("/json/active-population", name="json_active_population")
     */
    public function activePopulationAction()
    {
        $data = $this->chartDataService->getTotalToPopulation();

        return new JsonResponse($data);
    }

    // general countr
    /**
     * @Route("/json/active-cases", name="json_active_cases")
     */
    public function activeCasesAction()
    {
        $data = $this->chartDataService->getActive();

        return new JsonResponse($data);
    }

    /**
     * @Route("/json/recovered", name="json_recovered")
     */
    public function recoveredAction()
    {
        $data = $this->chartDataService->getRecovered();

        return new JsonResponse($data);
    }

    /**
     * @Route("/json/recovered-daily", name="json_recovered_daily")
     */
    public function recoveredDailyAction()
    {
        $data = $this->chartDataService->getNewRecoveredDaily();

        return new JsonResponse($data);
    }

    /**
     * @Route("/json/new-daily", name="json_new_daily")
     */
    public function newDailyAction()
    {
        $data = $this->chartDataService->getNewCasesDaily();

        return new JsonResponse($data);
    }

    // country chart
    /**
     * @Route("/json/active-cases/{country}", name="json_country_active_cases")
     */
    public function countryActiveCasesAction(Country $country)
    {
        $data = $this->chartDataService->getCountryActive($country);

        return new JsonResponse($data);
    }

    /**
     * @Route("/json/recovered/{country}", name="json_country_recovered")
     */
    public function countryRecoveredAction(Country $country)
    {
        $data = $this->chartDataService->getCountryRecovered($country);

        return new JsonResponse($data);
    }

    /**
     * @Route("/json/recovered-daily/{country}", name="json_country_recovered_daily")
     */
    public function countryRecoveredDailyAction(Country $country)
    {
        $data = $this->chartDataService->getCountryNewRecoveredDaily($country);

        return new JsonResponse($data);
    }

    /**
     * @Route("/json/new-daily/{country}", name="json_country_new_daily")
     */
    public function countryNewDailyAction(Country $country)
    {
        $data = $this->chartDataService->getCountryNewCasesDaily($country);

        return new JsonResponse($data);
    }
}
