<?php

namespace App\Controller;

use App\Entity\Country;
use App\Repository\CountryCaseRepository;
use App\Repository\CountryRepository;
use App\Repository\DailyStatRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
    public function indexAction(DailyStatRepository $dailyStatRepository, CountryRepository $countryRepository)
    {
        $worldPopulation = $this->getParameter('world_population');
        $lastRecord = $dailyStatRepository->getLastRecord();
        $prevRecord = $dailyStatRepository->getPrevDayStat($lastRecord);
        $percent = round($lastRecord->getActive() / $worldPopulation * 100, 2);

        $lastUpdateDate = $countryRepository->getLastUpdateAt();

        return $this->render('main/index.html.twig', [
            'world_population' => $worldPopulation,
            'last_record' => $lastRecord,
            'last_update_date' => $lastUpdateDate,

            'prev_record' => $prevRecord,
            'percent' => $percent,
        ]);
    }

    /**
     * @Route("/countries", name="main_countries")
     *
     * @param CountryCaseRepository $countryCaseRepository
     * @param DailyStatRepository   $dailyStatRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function countriesAction(CountryRepository $countryRepository, CountryCaseRepository $countryCaseRepository, DailyStatRepository $dailyStatRepository)
    {
        $lastRecord = $dailyStatRepository->getLastRecord();

        $countryCases = $countryCaseRepository->getAllCounries($lastRecord->getDailyDate());
        $lastUpdateDate = $countryRepository->getLastUpdateAt();

        return $this->render('main/countries.html.twig', [
            'last_update_date' => $lastUpdateDate,
            'country_cases' => $countryCases,
        ]);
    }

    /**
     * @Route("/countries/{country_name}", name="main_country_view", requirements={"country_name"="[\w\s.-]+"})
     *
     * @ParamConverter("country", class="App\Entity\Country", options={"mapping": {"country_name" = "name"} })
     *
     * @param Country               $country
     * @param CountryCaseRepository $countryCaseRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function countryViewAction(Country $country, CountryCaseRepository $countryCaseRepository)
    {
        $countryCase = $countryCaseRepository->getLastByCountry($country);
        $prevRecord = $countryCaseRepository->getCasesPrevDay($countryCase);


        return $this->render('main/country.html.twig', [
            'country_case' => $countryCase,
            'country_name' => $countryCase->getCountry()->getName(),
            'prev_record' => $prevRecord,
        ]);
    }

}
