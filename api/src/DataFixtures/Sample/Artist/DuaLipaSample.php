<?php

namespace App\DataFixtures\Sample\Artist;

use App\DataFixtures\Sample\Category\CategoryEnum;
use App\DataFixtures\Sample\SampleInterface;

class DuaLipaSample implements SampleInterface
{
    public function getData(): array
    {
        return [
            'name' => 'Dua Lipa',
            'albums' => [
                'Future Nostalgia' => [
                    'Don\'t Start Now' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC, CategoryEnum::HOUSE],
                    'Physical' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'Levitating' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC, CategoryEnum::HOUSE],
                    'Break My Heart' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'Good in Bed' => [CategoryEnum::POP, CategoryEnum::R_B],
                ],
                'Dua Lipa' => [
                    'New Rules' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'IDGAF' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'One Kiss' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC, CategoryEnum::HOUSE],
                    'Be the One' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'Blow Your Mind (Mwah)' => [CategoryEnum::POP, CategoryEnum::R_B],
                ],
                'Radical Optimism' => [
                    'Houdini' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'Training Season' => [CategoryEnum::POP, CategoryEnum::R_B],
                    'Illusion' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'Falling Forever' => [CategoryEnum::POP, CategoryEnum::ALTERNATIVE],
                    'Anything for Love' => [CategoryEnum::POP, CategoryEnum::SOUL],
                ],
            ],
        ];
    }
}
