<?php

namespace App\DataFixtures\Sample\Artist;

use App\DataFixtures\Sample\Category\CategoryEnum;
use App\DataFixtures\Sample\SampleInterface;

class KendrickLamarSample implements SampleInterface
{
    public function getData(): array
    {
        return [
            'name' => 'Kendrick Lamar',
            'albums' => [
                'DAMN.' => [
                    'HUMBLE.' => [CategoryEnum::HIP_HOP, CategoryEnum::TRAP],
                    'DNA.' => [CategoryEnum::HIP_HOP],
                    'LOYALTY.' => [CategoryEnum::HIP_HOP, CategoryEnum::POP],
                    'LOVE.' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B],
                    'XXX.' => [CategoryEnum::HIP_HOP, CategoryEnum::ROCK],
                ],
                'good kid, m.A.A.d city' => [
                    'Swimming Pools (Drank)' => [CategoryEnum::HIP_HOP],
                    'Bitch, Don\'t Kill My Vibe' => [CategoryEnum::HIP_HOP, CategoryEnum::JAZZ],
                    'Poetic Justice' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B],
                    'm.A.A.d city' => [CategoryEnum::HIP_HOP],
                    'Money Trees' => [CategoryEnum::HIP_HOP, CategoryEnum::JAZZ],
                ],
                'To Pimp a Butterfly' => [
                    'King Kunta' => [CategoryEnum::HIP_HOP, CategoryEnum::FUNK],
                    'Alright' => [CategoryEnum::HIP_HOP, CategoryEnum::JAZZ],
                    'These Walls' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B],
                    'i' => [CategoryEnum::HIP_HOP, CategoryEnum::JAZZ, CategoryEnum::FUNK],
                    'The Blacker the Berry' => [CategoryEnum::HIP_HOP],
                ],
                'Mr. Morale & The Big Steppers' => [
                    'N95' => [CategoryEnum::HIP_HOP],
                    'Silent Hill' => [CategoryEnum::HIP_HOP, CategoryEnum::ALTERNATIVE],
                    'Die Hard' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B],
                    'Father Time' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B],
                    'Rich Spirit' => [CategoryEnum::HIP_HOP, CategoryEnum::SOUL],
                ],
            ],
        ];
    }
}
