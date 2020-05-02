<?php

namespace App\Controller;

use App\Entity\Country;
use App\Repository\CountryCaseRepository;
use App\Repository\CountryRepository;
use App\Repository\DailyStatRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CountriesController extends AbstractController
{
    /**
     * @Route("/countries", name="countries_index")
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

        return $this->render('countries/countries.html.twig', [
            'last_update_date' => $lastUpdateDate,
            'country_cases' => $countryCases,
        ]);
    }

    /**
     * @Route("/countries/top-5", name="countries_top5")
     *
     * @param CountryCaseRepository $countryCaseRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function countriesTop5(CountryCaseRepository $countryCaseRepository)
    {
        $topCountries = $countryCaseRepository->getTopCountries();

        return $this->render('inc/top5.html.twig', [
            'top_countries' => $topCountries,
        ]);
    }

    /**
     * @Route("/countries/{country_name}", name="countries_view", requirements={"country_name"="[\w\s.-]+"})
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
        $countryCase = $countryCaseRepository->getLastByCountryWithChange($country);
        $prevRecord = $countryCaseRepository->getCasesPrevDay($countryCase);


        return $this->render('countries/country.html.twig', [
            'country_case' => $countryCase,
            'country_name' => $countryCase->getCountry()->getName(),
            'prev_record' => $prevRecord,
        ]);
    }

}
