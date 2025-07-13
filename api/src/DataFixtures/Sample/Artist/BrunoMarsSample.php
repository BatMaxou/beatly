<?php

namespace App\DataFixtures\Sample\Artist;

use App\DataFixtures\Sample\Category\CategoryEnum;
use App\DataFixtures\Sample\SampleInterface;

class BrunoMarsSample implements SampleInterface
{
    public function getData(): array
    {
        return [
            'name' => 'Bruno Mars',
            'albums' => [
                '24K Magic' => [
                    '24K Magic' => [CategoryEnum::R_B, CategoryEnum::FUNK, CategoryEnum::HIP_HOP],
                    'That\'s What I Like' => [CategoryEnum::R_B, CategoryEnum::HIP_HOP],
                    'Versace on the Floor' => [CategoryEnum::R_B, CategoryEnum::POP],
                    'Chunky' => [CategoryEnum::R_B, CategoryEnum::FUNK],
                    'Perm' => [CategoryEnum::R_B, CategoryEnum::FUNK],
                ],
                'Unorthodox Jukebox' => [
                    'Locked Out of Heaven' => [CategoryEnum::POP, CategoryEnum::ROCK, CategoryEnum::REGGAE],
                    'When I Was Your Man' => [CategoryEnum::POP, CategoryEnum::SOUL],
                    'Treasure' => [CategoryEnum::POP, CategoryEnum::FUNK],
                    'Gorilla' => [CategoryEnum::R_B, CategoryEnum::ROCK],
                    'Young Girls' => [CategoryEnum::POP, CategoryEnum::R_B],
                ],
                'Doo-Wops & Hooligans' => [
                    'Just the Way You Are' => [CategoryEnum::POP, CategoryEnum::R_B],
                    'Grenade' => [CategoryEnum::POP, CategoryEnum::ROCK],
                    'The Lazy Song' => [CategoryEnum::POP, CategoryEnum::REGGAE],
                    'Count on Me' => [CategoryEnum::POP, CategoryEnum::FOLK],
                    'Marry You' => [CategoryEnum::POP, CategoryEnum::SOUL],
                ],
            ],
        ];
    }
}
