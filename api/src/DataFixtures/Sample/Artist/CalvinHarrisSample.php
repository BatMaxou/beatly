<?php

namespace App\DataFixtures\Sample\Artist;

use App\DataFixtures\Sample\Category\CategoryEnum;
use App\DataFixtures\Sample\SampleInterface;

class CalvinHarrisSample implements SampleInterface
{
    public function getData(): array
    {
        return [
            'name' => 'Calvin Harris',
            'albums' => [
                'Funk Wav Bounces Vol. 1' => [
                    'Slide' => [CategoryEnum::ELECTRONIC, CategoryEnum::FUNK, CategoryEnum::HIP_HOP],
                    'Heatstroke' => [CategoryEnum::ELECTRONIC, CategoryEnum::HIP_HOP],
                    'Rollin' => [CategoryEnum::ELECTRONIC, CategoryEnum::HIP_HOP],
                    'Cash Out' => [CategoryEnum::ELECTRONIC, CategoryEnum::HIP_HOP],
                    'Faking It' => [CategoryEnum::ELECTRONIC, CategoryEnum::POP],
                ],
                '18 Months' => [
                    'We Found Love' => [CategoryEnum::ELECTRONIC, CategoryEnum::HOUSE, CategoryEnum::POP],
                    'Feel So Close' => [CategoryEnum::ELECTRONIC, CategoryEnum::HOUSE],
                    'Let\'s Go' => [CategoryEnum::ELECTRONIC, CategoryEnum::HOUSE],
                    'Sweet Nothing' => [CategoryEnum::ELECTRONIC, CategoryEnum::HOUSE],
                    'I Need Your Love' => [CategoryEnum::ELECTRONIC, CategoryEnum::POP],
                ],
                'Motion' => [
                    'Summer' => [CategoryEnum::ELECTRONIC, CategoryEnum::HOUSE],
                    'Blame' => [CategoryEnum::ELECTRONIC, CategoryEnum::HOUSE],
                    'Outside' => [CategoryEnum::ELECTRONIC, CategoryEnum::POP],
                    'Open Wide' => [CategoryEnum::ELECTRONIC, CategoryEnum::TECHNO],
                    'Slow Acid' => [CategoryEnum::ELECTRONIC, CategoryEnum::TECHNO],
                ],
            ],
        ];
    }
}
