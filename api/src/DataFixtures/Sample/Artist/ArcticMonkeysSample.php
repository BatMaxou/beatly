<?php

namespace App\DataFixtures\Sample\Artist;

use App\DataFixtures\Sample\Category\CategoryEnum;
use App\DataFixtures\Sample\SampleInterface;

class ArcticMonkeysSample implements SampleInterface
{
    public function getData(): array
    {
        return [
            'name' => 'Arctic Monkeys',
            'albums' => [
                'AM' => [
                    'Do I Wanna Know?' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE, CategoryEnum::INDIE],
                    'R U Mine?' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                    'One for the Road' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                    'Arabella' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                    'Snap Out of It' => [CategoryEnum::ROCK, CategoryEnum::INDIE]
                ],
                'Whatever People Say I Am, That\'s What I\'m Not' => [
                    'I Bet You Look Good on the Dancefloor' => [CategoryEnum::ROCK, CategoryEnum::INDIE],
                    'When the Sun Goes Down' => [CategoryEnum::ROCK, CategoryEnum::INDIE],
                    'Fake Tales of San Francisco' => [CategoryEnum::ROCK, CategoryEnum::INDIE],
                    'Dancing Shoes' => [CategoryEnum::ROCK, CategoryEnum::INDIE]
                ],
                'Favourite Worst Nightmare' => [
                    'Brianstorm' => [CategoryEnum::ROCK, CategoryEnum::INDIE],
                    'Fluorescent Adolescent' => [CategoryEnum::ROCK, CategoryEnum::INDIE],
                    'Teddy Picker' => [CategoryEnum::ROCK, CategoryEnum::INDIE],
                    '505' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE, CategoryEnum::INDIE],
                    'Do Me a Favour' => [CategoryEnum::ROCK, CategoryEnum::INDIE]
                ]
            ]
        ];
    }
}

