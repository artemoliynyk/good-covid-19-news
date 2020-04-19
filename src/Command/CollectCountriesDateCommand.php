<?php

namespace App\Command;

use App\Service\CovidApiService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CollectCountriesDateCommand extends Command
{
    protected static $defaultName = 'app:collect:date';
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
            ->addArgument('date', InputArgument::REQUIRED, 'Date to fetch date: format 2020-01-30');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $date = $input->getArgument('date');
        $result = $this->apiService->getDateCountriesStat($date);

        if (!$result) {
            $io->error('Unable to update countries stat, please check logs');

            return 1;
        }

        return 0;
    }
}
