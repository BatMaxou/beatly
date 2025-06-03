<?php

namespace App\DataFixtures\Sample\Artist;

use App\DataFixtures\Sample\Category\CategoryEnum;
use App\DataFixtures\Sample\SampleInterface;

class AngeleSample implements SampleInterface
{
    public function getData(): array
    {
        return [
            'name' => 'Angèle',
            'albums' => [
                'Brol' => [
                    'Tout oublier' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'Balance ton quoi' => [CategoryEnum::POP, CategoryEnum::HIP_HOP],
                    'Jalousie' => [CategoryEnum::POP, CategoryEnum::R_B],
                    'La thune' => [CategoryEnum::POP, CategoryEnum::HIP_HOP],
                    'Flou' => [CategoryEnum::POP, CategoryEnum::INDIE]
                ],
                'Nonante-cinq' => [
                    'Bruxelles je t\'aime' => [CategoryEnum::POP, CategoryEnum::FOLK],
                    'Plus de sens' => [CategoryEnum::POP, CategoryEnum::ALTERNATIVE],
                    'Libre' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'Tempête' => [CategoryEnum::POP, CategoryEnum::ROCK],
                    'Fever' => [CategoryEnum::POP, CategoryEnum::R_B]
                ]
            ]
        ];
    }
}

