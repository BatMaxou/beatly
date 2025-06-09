<?php

namespace App\DataFixtures\Sample\Artist;

use App\DataFixtures\Sample\Category\CategoryEnum;
use App\DataFixtures\Sample\SampleInterface;

class StromaeSample implements SampleInterface
{
    public function getData(): array
    {
        return [
            'name' => 'Stromae',
            'albums' => [
                'Racine carrée' => [
                    'Papaoutai' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'Formidable' => [CategoryEnum::POP, CategoryEnum::ALTERNATIVE],
                    'Tous les mêmes' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'Ta fête' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC, CategoryEnum::HIP_HOP],
                    'AVF' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC]
                ],
                'Cheese' => [
                    'Alors on danse' => [CategoryEnum::ELECTRONIC, CategoryEnum::POP],
                    'Te quiero' => [CategoryEnum::ELECTRONIC, CategoryEnum::POP],
                    'House\'llelujah' => [CategoryEnum::ELECTRONIC, CategoryEnum::HOUSE],
                    'Peace or Violence' => [CategoryEnum::ELECTRONIC, CategoryEnum::ALTERNATIVE],
                    'Bienvenue chez moi' => [CategoryEnum::ELECTRONIC, CategoryEnum::POP]
                ],
                'Multitude' => [
                    'Santé' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'L\'enfer' => [CategoryEnum::POP, CategoryEnum::ALTERNATIVE],
                    'C\'est que du bonheur' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC],
                    'Fils de joie' => [CategoryEnum::POP, CategoryEnum::ALTERNATIVE],
                    'Macarena' => [CategoryEnum::POP, CategoryEnum::ELECTRONIC, CategoryEnum::HIP_HOP]
                ]
            ]
        ];
    }
}

