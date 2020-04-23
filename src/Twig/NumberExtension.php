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
            new TwigFilter('number_signed', [$this, 'signedNumber']),
        ];
    }


    public function formatNumber($value)
    {
        return number_format($value, 0, ".", " ");
    }


    public function signedNumber($value)
    {
        if (0 === $value) {
            return 0;
        }

        return ($value > 0 ? '+' : '').$this->formatNumber($value);
    }


}
