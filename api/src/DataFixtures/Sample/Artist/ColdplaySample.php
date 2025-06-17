<?php

namespace App\DataFixtures\Sample\Artist;

use App\DataFixtures\Sample\Category\CategoryEnum;
use App\DataFixtures\Sample\SampleInterface;

class ColdplaySample implements SampleInterface
{
    public function getData(): array
    {
        return [
            'name' => 'Coldplay',
            'albums' => [
                'A Rush of Blood to the Head' => [
                    'In My Place' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                    'The Scientist' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                    'Clocks' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE, CategoryEnum::ELECTRONIC],
                    'Daylight' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                    'A Rush of Blood to the Head' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE]
                ],
                'Viva la Vida or Death and All His Friends' => [
                    'Viva la Vida' => [CategoryEnum::ROCK, CategoryEnum::POP, CategoryEnum::ALTERNATIVE],
                    'Violet Hill' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                    'Strawberry Swing' => [CategoryEnum::ROCK, CategoryEnum::INDIE],
                    'Lost!' => [CategoryEnum::ROCK, CategoryEnum::ELECTRONIC],
                    '42' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE]
                ],
                'Parachutes' => [
                    'Yellow' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                    'Trouble' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                    'Shiver' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                    'Don\'t Panic' => [CategoryEnum::ROCK, CategoryEnum::INDIE],
                    'Spies' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE]
                ],
                'Music of the Spheres' => [
                    'Higher Power' => [CategoryEnum::ROCK, CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'My Universe' => [CategoryEnum::ROCK, CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'Coloratura' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                    'Humankind' => [CategoryEnum::ROCK, CategoryEnum::ELECTRONIC],
                    'People of the Pride' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE]
                ]
            ]
        ];
    }
}

