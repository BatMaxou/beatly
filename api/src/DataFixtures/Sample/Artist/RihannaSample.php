<?php

namespace App\DataFixtures\Sample\Artist;

use App\DataFixtures\Sample\Category\CategoryEnum;
use App\DataFixtures\Sample\SampleInterface;

class RihannaSample implements SampleInterface
{
    public function getData(): array
    {
        return [
            'name' => 'Rihanna',
            'albums' => [
                'Anti' => [
                    'Work' => [CategoryEnum::R_B, CategoryEnum::HIP_HOP, CategoryEnum::REGGAE],
                    'Needed Me' => [CategoryEnum::R_B, CategoryEnum::HIP_HOP],
                    'Love on the Brain' => [CategoryEnum::R_B, CategoryEnum::SOUL],
                    'Kiss It Better' => [CategoryEnum::R_B, CategoryEnum::ROCK],
                    'Stay' => [CategoryEnum::R_B, CategoryEnum::POP],
                ],
                'Loud' => [
                    'Only Girl (In The World)' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC, CategoryEnum::HOUSE],
                    'What\'s My Name?' => [CategoryEnum::R_B, CategoryEnum::HIP_HOP],
                    'S&M' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'California King Bed' => [CategoryEnum::R_B, CategoryEnum::POP],
                    'Man Down' => [CategoryEnum::R_B, CategoryEnum::REGGAE],
                ],
                'Good Girl Gone Bad' => [
                    'Umbrella' => [CategoryEnum::POP, CategoryEnum::R_B, CategoryEnum::HIP_HOP],
                    'Don\'t Stop the Music' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC, CategoryEnum::HOUSE],
                    'Shut Up and Drive' => [CategoryEnum::POP, CategoryEnum::ROCK],
                    'Hate That I Love You' => [CategoryEnum::R_B, CategoryEnum::POP],
                    'Rehab' => [CategoryEnum::R_B, CategoryEnum::SOUL],
                ],
            ],
        ];
    }
}
