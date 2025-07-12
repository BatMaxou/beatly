<?php

namespace App\DataFixtures\Sample\Artist;

use App\DataFixtures\Sample\Category\CategoryEnum;
use App\DataFixtures\Sample\SampleInterface;

class TaylorSwiftSample implements SampleInterface
{
    public function getData(): array
    {
        return [
            'name' => 'Taylor Swift',
            'albums' => [
                '1989' => [
                    'Shake It Off' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'Blank Space' => [CategoryEnum::POP],
                    'Bad Blood' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'Style' => [CategoryEnum::POP, CategoryEnum::ROCK],
                    'Wildest Dreams' => [CategoryEnum::POP, CategoryEnum::ALTERNATIVE],
                ],
                'Folklore' => [
                    'cardigan' => [CategoryEnum::INDIE, CategoryEnum::FOLK, CategoryEnum::ALTERNATIVE],
                    'the 1' => [CategoryEnum::INDIE, CategoryEnum::FOLK],
                    'august' => [CategoryEnum::FOLK, CategoryEnum::ALTERNATIVE],
                    'invisible string' => [CategoryEnum::FOLK, CategoryEnum::INDIE],
                    'betty' => [CategoryEnum::FOLK, CategoryEnum::COUNTRY],
                ],
                'Midnights' => [
                    'Anti-Hero' => [CategoryEnum::POP, CategoryEnum::ALTERNATIVE],
                    'Lavender Haze' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'Midnight Rain' => [CategoryEnum::POP, CategoryEnum::R_B],
                    'Bejeweled' => [CategoryEnum::POP],
                    'Karma' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                ],
                'Red (Taylor\'s Version)' => [
                    'All Too Well (10 Minute Version)' => [CategoryEnum::POP, CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                    'We Are Never Ever Getting Back Together' => [CategoryEnum::POP],
                    'I Knew You Were Trouble' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    '22' => [CategoryEnum::POP],
                ],
            ],
        ];
    }
}
