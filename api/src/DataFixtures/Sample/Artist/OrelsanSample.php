<?php

namespace App\DataFixtures\Sample\Artist;

use App\DataFixtures\Sample\Category\CategoryEnum;
use App\DataFixtures\Sample\SampleInterface;

class OrelsanSample implements SampleInterface
{
    public function getData(): array
    {
        return [
            'name' => 'Orelsan',
            'albums' => [
                'Civilisation' => [
                    'L\'odeur de l\'essence' => [CategoryEnum::HIP_HOP, CategoryEnum::ALTERNATIVE],
                    'Manifeste' => [CategoryEnum::HIP_HOP],
                    'Shonen' => [CategoryEnum::HIP_HOP, CategoryEnum::ELECTRONIC],
                    'Du propre' => [CategoryEnum::HIP_HOP],
                    'Casseurs Flowters' => [CategoryEnum::HIP_HOP, CategoryEnum::ALTERNATIVE]
                ],
                'La fête est finie' => [
                    'Basique' => [CategoryEnum::HIP_HOP],
                    'La pluie' => [CategoryEnum::HIP_HOP, CategoryEnum::ALTERNATIVE],
                    'Défaite de famille' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B],
                    'Notes pour trop tard' => [CategoryEnum::HIP_HOP, CategoryEnum::SOUL],
                    'Tout va bien' => [CategoryEnum::HIP_HOP, CategoryEnum::POP]
                ],
                'Perdu d\'avance' => [
                    'Relève-toi' => [CategoryEnum::HIP_HOP],
                    'Suicide social' => [CategoryEnum::HIP_HOP, CategoryEnum::ALTERNATIVE],
                    'RaelSan' => [CategoryEnum::HIP_HOP],
                    'Ils sont cools' => [CategoryEnum::HIP_HOP, CategoryEnum::ELECTRONIC],
                    'Changement' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B]
                ]
            ]
        ];
    }
}

