<?php

namespace App\Command;

use App\Service\CovidApiService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CollectCountriesCommand extends Command
{
    protected static $defaultName = 'app:collect:countries';
    /**
     * @var CovidApiService
     */
    private $apiService;

    public function __construct(string $name = null, CovidApiService $covidApiService)
    {
        parent::__construct($name);

        $this->apiService = $covidApiService;
    }


    protected function configure()
    {
        $this
            ->setDescription('Get current overall statistic')
            ->addArgument('all', InputArgument::OPTIONAL, '[bool] Get all data for all countries for all time');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $all = (bool) $input->getArgument('all');

        if ($all) {
            $result = $this->apiService->getAllTimeCountriesStat();

        } else {
            $result = $this->apiService->getLatestCountriesStat();
        }

        if (!$result) {
            $io->error('Unable to update countries stat, please check logs');

            return 1;
        }

        return 0;
    }
}
