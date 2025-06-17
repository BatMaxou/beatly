<?php

namespace App\DataFixtures\Sample\Artist;

use App\DataFixtures\Sample\Category\CategoryEnum;
use App\DataFixtures\Sample\SampleInterface;

class AyaNakamuraSample implements SampleInterface
{
    public function getData(): array
    {
        return [
            'name' => 'Aya Nakamura',
            'albums' => [
                'Nakamura' => [
                    'Djadja' => [CategoryEnum::POP, CategoryEnum::HIP_HOP, CategoryEnum::R_B],
                    'Copines' => [CategoryEnum::POP, CategoryEnum::R_B],
                    'La dot' => [CategoryEnum::POP, CategoryEnum::HIP_HOP],
                    'Sucette' => [CategoryEnum::POP, CategoryEnum::R_B],
                    'Pookie' => [CategoryEnum::POP, CategoryEnum::HIP_HOP]
                ],
                'Aya' => [
                    'Pookie' => [CategoryEnum::POP, CategoryEnum::HIP_HOP],
                    'Jolie nana' => [CategoryEnum::POP, CategoryEnum::R_B],
                    'Plus jamais' => [CategoryEnum::POP, CategoryEnum::R_B],
                    '40%' => [CategoryEnum::POP, CategoryEnum::HIP_HOP],
                    'Whine Up' => [CategoryEnum::POP, CategoryEnum::REGGAE, CategoryEnum::HIP_HOP]
                ],
                'DNK' => [
                    'SMS' => [CategoryEnum::POP, CategoryEnum::HIP_HOP],
                    'Dégaine' => [CategoryEnum::POP, CategoryEnum::HIP_HOP, CategoryEnum::TRAP],
                    'Baby' => [CategoryEnum::POP, CategoryEnum::R_B],
                    'Méchante' => [CategoryEnum::POP, CategoryEnum::HIP_HOP],
                    'Nirvana' => [CategoryEnum::POP, CategoryEnum::R_B, CategoryEnum::ALTERNATIVE]
                ]
            ]
        ];
    }
}

