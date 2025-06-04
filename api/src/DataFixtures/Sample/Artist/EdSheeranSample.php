<?php

namespace App\DataFixtures\Sample\Artist;

use App\DataFixtures\Sample\Category\CategoryEnum;
use App\DataFixtures\Sample\SampleInterface;

class EdSheeranSample implements SampleInterface
{
    public function getData(): array
    {
        return [
            'name' => 'Ed Sheeran',
            'albums' => [
                'Divide' => [
                    'Shape of You' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC, CategoryEnum::R_B],
                    'Perfect' => [CategoryEnum::POP, CategoryEnum::FOLK],
                    'Castle on the Hill' => [CategoryEnum::POP, CategoryEnum::ROCK],
                    'Galway Girl' => [CategoryEnum::POP, CategoryEnum::FOLK, CategoryEnum::COUNTRY],
                    'Thinking Out Loud' => [CategoryEnum::POP, CategoryEnum::SOUL]
                ],
                'Multiply' => [
                    'Sing' => [CategoryEnum::POP, CategoryEnum::HIP_HOP],
                    'Don\'t' => [CategoryEnum::POP, CategoryEnum::R_B],
                    'Photograph' => [CategoryEnum::POP, CategoryEnum::FOLK],
                    'Bloodstream' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'Tenerife Sea' => [CategoryEnum::POP, CategoryEnum::FOLK]
                ],
                'Plus' => [
                    'The A Team' => [CategoryEnum::POP, CategoryEnum::FOLK],
                    'Lego House' => [CategoryEnum::POP, CategoryEnum::INDIE],
                    'Drunk' => [CategoryEnum::POP, CategoryEnum::FOLK],
                    'Small Bump' => [CategoryEnum::POP, CategoryEnum::FOLK],
                    'Give Me Love' => [CategoryEnum::POP, CategoryEnum::SOUL]
                ],
                'Equals' => [
                    'Bad Habits' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'Shivers' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'Overpass Graffiti' => [CategoryEnum::POP, CategoryEnum::ROCK],
                    'The Joker And The Queen' => [CategoryEnum::POP, CategoryEnum::FOLK],
                    'First Times' => [CategoryEnum::POP, CategoryEnum::R_B]
                ]
            ]
        ];
    }
}

