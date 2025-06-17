<?php

namespace App\DataFixtures\Sample\Artist;

use App\DataFixtures\Sample\Category\CategoryEnum;
use App\DataFixtures\Sample\SampleInterface;

class PNLSample implements SampleInterface
{
    public function getData(): array
    {
        return [
            'name' => 'PNL',
            'albums' => [
                'Dans la légende' => [
                    'Le monde ou rien' => [CategoryEnum::HIP_HOP, CategoryEnum::TRAP],
                    'DA' => [CategoryEnum::HIP_HOP],
                    'Oh lala' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B],
                    'Bené' => [CategoryEnum::HIP_HOP, CategoryEnum::TRAP],
                    'Naha' => [CategoryEnum::HIP_HOP, CategoryEnum::ALTERNATIVE]
                ],
                'Deux frères' => [
                    'Au DD' => [CategoryEnum::HIP_HOP, CategoryEnum::TRAP],
                    'Blanka' => [CategoryEnum::HIP_HOP, CategoryEnum::ELECTRONIC],
                    'Chang' => [CategoryEnum::HIP_HOP, CategoryEnum::ALTERNATIVE],
                    'Celsius' => [CategoryEnum::HIP_HOP, CategoryEnum::TRAP],
                    'Mowgli II' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B]
                ],
                'Le Monde Chico' => [
                    'Le monde chico' => [CategoryEnum::HIP_HOP],
                    'Loin des hommes' => [CategoryEnum::HIP_HOP, CategoryEnum::ALTERNATIVE],
                    'Humain' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B],
                    'Plus tony que sosa' => [CategoryEnum::HIP_HOP, CategoryEnum::TRAP],
                    'Sheita' => [CategoryEnum::HIP_HOP]
                ]
            ]
        ];
    }
}

