<?php

namespace App\DataFixtures\Sample\Artist;

use App\DataFixtures\Sample\Category\CategoryEnum;
use App\DataFixtures\Sample\SampleInterface;

class DrakeSample implements SampleInterface
{
    public function getData(): array
    {
        return [
            'name' => 'Drake',
            'albums' => [
                'Scorpion' => [
                    'God\'s Plan' => [CategoryEnum::HIP_HOP, CategoryEnum::TRAP],
                    'In My Feelings' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B],
                    'Nice For What' => [CategoryEnum::HIP_HOP],
                    'Nonstop' => [CategoryEnum::HIP_HOP, CategoryEnum::TRAP],
                    'Mob Ties' => [CategoryEnum::HIP_HOP, CategoryEnum::TRAP]
                ],
                'Views' => [
                    'Hotline Bling' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B],
                    'One Dance' => [CategoryEnum::HIP_HOP, CategoryEnum::ELECTRONIC, CategoryEnum::REGGAE],
                    'Too Good' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B],
                    'Controlla' => [CategoryEnum::HIP_HOP, CategoryEnum::REGGAE],
                    'Child\'s Play' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B]
                ],
                'Take Care' => [
                    'Take Care' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B],
                    'Marvins Room' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B, CategoryEnum::SOUL],
                    'Headlines' => [CategoryEnum::HIP_HOP],
                    'Make Me Proud' => [CategoryEnum::HIP_HOP],
                    'The Motto' => [CategoryEnum::HIP_HOP, CategoryEnum::TRAP]
                ],
                'Certified Lover Boy' => [
                    'Way 2 Sexy' => [CategoryEnum::HIP_HOP, CategoryEnum::TRAP],
                    'Girls Want Girls' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B],
                    'Champagne Poetry' => [CategoryEnum::HIP_HOP, CategoryEnum::R_B],
                    'Fair Trade' => [CategoryEnum::HIP_HOP]
                ]
            ]
        ];
    }
}

