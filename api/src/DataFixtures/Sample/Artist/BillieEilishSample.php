<?php

namespace App\DataFixtures\Sample\Artist;

use App\DataFixtures\Sample\Category\CategoryEnum;
use App\DataFixtures\Sample\SampleInterface;

class BillieEilishSample implements SampleInterface
{
    public function getData(): array
    {
        return [
            'name' => 'Billie Eilish',
            'albums' => [
                'When We All Fall Asleep, Where Do We Go?' => [
                    'bad guy' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC, CategoryEnum::ALTERNATIVE],
                    'when the party\'s over' => [CategoryEnum::POP, CategoryEnum::ALTERNATIVE],
                    'bury a friend' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC, CategoryEnum::ALTERNATIVE],
                    'all the good girls go to hell' => [CategoryEnum::POP, CategoryEnum::ALTERNATIVE],
                    'wish you were gay' => [CategoryEnum::POP, CategoryEnum::INDIE]
                ],
                'Happier Than Ever' => [
                    'Happier Than Ever' => [CategoryEnum::POP, CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                    'Therefore I Am' => [CategoryEnum::POP, CategoryEnum::ALTERNATIVE],
                    'my boy' => [CategoryEnum::POP, CategoryEnum::INDIE],
                    'Lost Cause' => [CategoryEnum::POP, CategoryEnum::INDIE],
                    'Your Power' => [CategoryEnum::POP, CategoryEnum::FOLK, CategoryEnum::ALTERNATIVE]
                ],
                'Hit Me Hard and Soft' => [
                    'LUNCH' => [CategoryEnum::POP, CategoryEnum::ALTERNATIVE],
                    'CHIHIRO' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'BIRDS OF A FEATHER' => [CategoryEnum::POP, CategoryEnum::INDIE],
                    'WILDFLOWER' => [CategoryEnum::POP, CategoryEnum::FOLK],
                    'THE GREATEST' => [CategoryEnum::POP, CategoryEnum::ALTERNATIVE]
                ]
            ]
        ];
    }
}

