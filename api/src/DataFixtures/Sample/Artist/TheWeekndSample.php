<?php

namespace App\DataFixtures\Sample\Artist;

use App\DataFixtures\Sample\Category\CategoryEnum;
use App\DataFixtures\Sample\SampleInterface;

class TheWeekndSample implements SampleInterface
{
    public function getData(): array
    {
        return [
            'name' => 'The Weeknd',
            'albums' => [
                'After Hours' => [
                    'Blinding Lights' => [CategoryEnum::R_B, CategoryEnum::ELECTRONIC, CategoryEnum::POP],
                    'Save Your Tears' => [CategoryEnum::R_B, CategoryEnum::POP],
                    'In Your Eyes' => [CategoryEnum::R_B, CategoryEnum::SOUL],
                    'Heartless' => [CategoryEnum::R_B, CategoryEnum::HIP_HOP],
                    'After Hours' => [CategoryEnum::R_B, CategoryEnum::ALTERNATIVE],
                ],
                'Dawn FM' => [
                    'Take My Breath' => [CategoryEnum::R_B, CategoryEnum::ELECTRONIC],
                    'Sacrifice' => [CategoryEnum::R_B, CategoryEnum::ELECTRONIC],
                    'Out of Time' => [CategoryEnum::R_B, CategoryEnum::SOUL],
                    'Is There Someone Else?' => [CategoryEnum::R_B],
                    'Gasoline' => [CategoryEnum::R_B, CategoryEnum::ALTERNATIVE],
                ],
                'Beauty Behind the Madness' => [
                    'Can\'t Feel My Face' => [CategoryEnum::R_B, CategoryEnum::POP, CategoryEnum::FUNK],
                    'The Hills' => [CategoryEnum::R_B, CategoryEnum::ALTERNATIVE],
                    'Earned It' => [CategoryEnum::R_B, CategoryEnum::SOUL],
                    'Often' => [CategoryEnum::R_B],
                    'Acquainted' => [CategoryEnum::R_B, CategoryEnum::SOUL],
                ],
                'Starboy' => [
                    'Starboy' => [CategoryEnum::R_B, CategoryEnum::ELECTRONIC],
                    'I Feel It Coming' => [CategoryEnum::R_B, CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'Party Monster' => [CategoryEnum::R_B, CategoryEnum::ELECTRONIC],
                    'False Alarm' => [CategoryEnum::R_B, CategoryEnum::ROCK],
                    'Reminder' => [CategoryEnum::R_B, CategoryEnum::HIP_HOP],
                ],
            ],
        ];
    }
}
