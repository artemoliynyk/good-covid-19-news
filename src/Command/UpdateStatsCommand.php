<?php

namespace App\Command;

use App\Service\StatisticsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateStatsCommand extends Command
{
    protected static $defaultName = 'app:stats:calculate';

    const STAT_RECOVERED = 'country:recovered';
    const STAT_COUNTRY = 'country:change';
    const STAT_TOTAL = 'daily:totals';
    const STAT_DAILY = 'daily:change';

    protected static $stats = [
        self::STAT_RECOVERED => 'calculate new recovered by country (step 1)',
        self::STAT_COUNTRY => 'statistic change per country based on available data (step 2)',
        self::STAT_TOTAL => 'calculate daily totals(step 3)',
        self::STAT_DAILY => 'calculate daily change based on available data (step 4)',
    ];


    /**
     * @var StatisticsService
     */
    private $statisticsService;


    public function __construct(string $name = null, StatisticsService $statisticsService)
    {
        parent::__construct($name);

        $this->statisticsService = $statisticsService;
    }

    private function statOptionText(): string
    {
        $statsInfoArr = [];
        foreach (self::$stats as $stat => $descr) {
            $statsInfoArr[] = "{$stat} - {$descr}";
        }

        return "\n".implode("\n", $statsInfoArr);
    }


    protected function configure()
    {
        $this
            ->setDescription('Get current overall statistic')
            ->addArgument('stat', InputArgument::REQUIRED, sprintf('Update/calculate statistic data: %s', $this->statOptionText()));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $stat = $input->getArgument('stat');

        switch ($stat) {
            case self::STAT_RECOVERED:
                $result = $this->statisticsService->calculateNewRecoveredByCountry();
                break;

            case self::STAT_COUNTRY:
                $result = $this->statisticsService->calculateCountryChange();
                break;

            case self::STAT_TOTAL:
                $result = $this->statisticsService->calculateDailyTotal();
                break;

            case self::STAT_DAILY:
                $result = $this->statisticsService->calculateDailyChange();
                break;

            default:
                $result = 255;
                $io->warning(sprintf('No valid stat provided, possible values: %s', $this->statOptionText()));
        }


        if (!$result) {
            $io->error('Unable to update daily stat, please check logs');

            return 1;
        }

        return 0;
    }
}
