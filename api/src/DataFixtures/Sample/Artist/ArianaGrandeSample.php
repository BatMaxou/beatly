<?php

namespace App\DataFixtures\Sample\Artist;

use App\DataFixtures\Sample\Category\CategoryEnum;
use App\DataFixtures\Sample\SampleInterface;

class ArianaGrandeSample implements SampleInterface
{
    public function getData(): array
    {
        return [
            'name' => 'Ariana Grande',
            'albums' => [
                'Thank U, Next' => [
                    'thank u, next' => [CategoryEnum::POP, CategoryEnum::R_B],
                    '7 rings' => [CategoryEnum::POP, CategoryEnum::HIP_HOP, CategoryEnum::TRAP],
                    'break up with your girlfriend, i\'m bored' => [CategoryEnum::POP, CategoryEnum::R_B],
                    'NASA' => [CategoryEnum::POP],
                    'needy' => [CategoryEnum::POP, CategoryEnum::R_B],
                ],
                'Positions' => [
                    'positions' => [CategoryEnum::POP, CategoryEnum::R_B],
                    '34+35' => [CategoryEnum::POP, CategoryEnum::R_B],
                    'motive' => [CategoryEnum::POP, CategoryEnum::HIP_HOP],
                    'just like magic' => [CategoryEnum::POP],
                    'off the table' => [CategoryEnum::POP, CategoryEnum::R_B],
                ],
                'Sweetener' => [
                    'no tears left to cry' => [CategoryEnum::POP],
                    'God is a woman' => [CategoryEnum::POP, CategoryEnum::R_B],
                    'breathin' => [CategoryEnum::POP],
                    'sweetener' => [CategoryEnum::POP, CategoryEnum::R_B],
                    'successful' => [CategoryEnum::POP, CategoryEnum::HIP_HOP],
                ],
                'Dangerous Woman' => [
                    'Dangerous Woman' => [CategoryEnum::POP, CategoryEnum::R_B],
                    'Into You' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'Side To Side' => [CategoryEnum::POP, CategoryEnum::HIP_HOP],
                    'Be Alright' => [CategoryEnum::POP],
                    'Greedy' => [CategoryEnum::POP, CategoryEnum::R_B],
                ],
            ],
        ];
    }
}
