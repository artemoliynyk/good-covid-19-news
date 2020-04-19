<?php

namespace App\Tests;

use App\Service\StatisticsService;
use PHPUnit\Framework\TestCase;

class StatisticsServiceTest extends TestCase
{

    public function dataProvider()
    {
        return [
            // current, previous, change
            [100, 100, 0],
            [100, 50, 100],
            [50, 100, -100],

            [878712476, 97834, 898067],
            [2345, 512, 359],
            [1, 1, 0],

            [3, 1, 200],
            [3, 0, 0],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testPercentageCount($current, $previous, $change)
    {
        $calculated = StatisticsService::calculatePercentChange($current, $previous);
        $this->assertEquals($change, $calculated);
    }
}
