<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class NumberExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('number', [$this, 'formatNumber']),
        ];
    }


    public function formatNumber($value)
    {
        return number_format($value, 0, ".", " ");
    }
}
