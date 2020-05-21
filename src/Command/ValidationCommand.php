<?php

namespace App\Command;

use App\Service\StatisticsService;
use App\Service\ValidationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ValidationCommand extends Command
{
    protected static $defaultName = 'app:stats:validate';
    /**
     * @var StatisticsService
     */
    private $validationService;


    public function __construct(string $name = null, ValidationService $validationService)
    {
        parent::__construct($name);

        $this->validationService = $validationService;
    }

    protected function configure()
    {
        $this
            ->setDescription("Verify collected numbers from API. Previous day can't have bigger numbers");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $result = $this->validationService->verifyCountryData();

        if (!$result) {
            $io->error('Unable to verify countries data');

            return 1;
        }

        return 0;
    }
}
