<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('euro', [$this, 'formatEuro']),
        ];
    }

    public function formatEuro(float $amount): string
    {
        return number_format($amount, 2, ',', ' ') . ' €';
    }
}
