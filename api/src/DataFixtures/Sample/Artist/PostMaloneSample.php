<?php

namespace App\DataFixtures\Sample\Artist;

use App\DataFixtures\Sample\Category\CategoryEnum;
use App\DataFixtures\Sample\SampleInterface;

class PostMaloneSample implements SampleInterface
{
    public function getData(): array
    {
        return [
            'name' => 'Post Malone',
            'albums' => [
                'Hollywood\'s Bleeding' => [
                    'Circles' => [CategoryEnum::HIP_HOP, CategoryEnum::POP, CategoryEnum::ALTERNATIVE],
                    'Sunflower' => [CategoryEnum::HIP_HOP, CategoryEnum::POP],
                    'Wow.' => [CategoryEnum::HIP_HOP, CategoryEnum::TRAP],
                    'Goodbyes' => [CategoryEnum::HIP_HOP, CategoryEnum::POP, CategoryEnum::R_B],
                    'Take What You Want' => [CategoryEnum::HIP_HOP, CategoryEnum::ROCK],
                ],
                'beerbongs & bentleys' => [
                    'rockstar' => [CategoryEnum::HIP_HOP, CategoryEnum::TRAP],
                    'Psycho' => [CategoryEnum::HIP_HOP, CategoryEnum::TRAP],
                    'Better Now' => [CategoryEnum::HIP_HOP, CategoryEnum::POP],
                    'Stay' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B],
                    '92 Explorer' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B],
                ],
                'Stoney' => [
                    'Congratulations' => [CategoryEnum::HIP_HOP, CategoryEnum::TRAP],
                    'White Iverson' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B],
                    'Go Flex' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B],
                    'Too Young' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B],
                    'I Fall Apart' => [CategoryEnum::HIP_HOP, CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                ],
                'Twelve Carat Toothache' => [
                    'I Like You (A Happier Song)' => [CategoryEnum::HIP_HOP, CategoryEnum::POP],
                    'Cooped Up' => [CategoryEnum::HIP_HOP, CategoryEnum::TRAP],
                    'One Right Now' => [CategoryEnum::HIP_HOP, CategoryEnum::POP],
                    'Insane' => [CategoryEnum::HIP_HOP, CategoryEnum::TRAP],
                    'Love/Hate Letter to Alcohol' => [CategoryEnum::HIP_HOP, CategoryEnum::ROCK, CategoryEnum::ALTERNATIVE],
                ],
            ],
        ];
    }
}
