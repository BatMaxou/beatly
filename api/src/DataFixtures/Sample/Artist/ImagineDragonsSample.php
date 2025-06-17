<?php

namespace App\DataFixtures\Sample\Artist;

use App\DataFixtures\Sample\Category\CategoryEnum;
use App\DataFixtures\Sample\SampleInterface;

class ImagineDragonsSample implements SampleInterface
{
    public function getData(): array
    {
        return [
            'name' => 'Imagine Dragons',
            'albums' => [
                'Night Visions' => [
                    'Radioactive' => [CategoryEnum::ROCK, CategoryEnum::ELECTRONIC, CategoryEnum::ALTERNATIVE],
                    'Demons' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                    'It\'s Time' => [CategoryEnum::ROCK, CategoryEnum::INDIE],
                    'On Top of the World' => [CategoryEnum::ROCK, CategoryEnum::POP],
                    'Amsterdam' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE]
                ],
                'Evolve' => [
                    'Believer' => [CategoryEnum::ROCK, CategoryEnum::ELECTRONIC],
                    'Thunder' => [CategoryEnum::ROCK, CategoryEnum::ELECTRONIC, CategoryEnum::POP],
                    'Whatever It Takes' => [CategoryEnum::ROCK, CategoryEnum::ELECTRONIC],
                    'Walking the Wire' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                    'Rise Up' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE]
                ],
                'Origins' => [
                    'Natural' => [CategoryEnum::ROCK, CategoryEnum::ELECTRONIC],
                    'Zero' => [CategoryEnum::ROCK, CategoryEnum::ELECTRONIC],
                    'Machine' => [CategoryEnum::ROCK, CategoryEnum::ELECTRONIC],
                    'Bad Liar' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                    'West Coast' => [CategoryEnum::ROCK, CategoryEnum::INDIE]
                ],
                'Mercury - Act 1' => [
                    'Enemy' => [CategoryEnum::ROCK, CategoryEnum::ELECTRONIC, CategoryEnum::HIP_HOP],
                    'My Life' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                    'Lonely' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                    'Wrecked' => [CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                    'Monday' => [CategoryEnum::ROCK, CategoryEnum::ELECTRONIC]
                ]
            ]
        ];
    }
}

