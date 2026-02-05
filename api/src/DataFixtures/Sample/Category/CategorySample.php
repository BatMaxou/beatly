<?php

namespace App\DataFixtures\Sample\Category;

use App\DataFixtures\Sample\SampleInterface;

class CategorySample implements SampleInterface
{
    public const COLORS = [
        CategoryEnum::POP->value => '#FF6B6B',
        CategoryEnum::HIP_HOP->value => '#4ECDC4',
        CategoryEnum::ROCK->value => '#45B7D1',
        CategoryEnum::ELECTRONIC->value => '#96CEB4',
        CategoryEnum::R_B->value => '#FFEAA7',
        CategoryEnum::COUNTRY->value => '#DDA0DD',
        CategoryEnum::JAZZ->value => '#98D8C8',
        CategoryEnum::CLASSICAL->value => '#F7DC6F',
        CategoryEnum::REGGAE->value => '#BB8FCE',
        CategoryEnum::ALTERNATIVE->value => '#85C1E9',
        CategoryEnum::INDIE->value => '#F8C471',
        CategoryEnum::FOLK->value => '#AED6F1',
        CategoryEnum::FUNK->value => '#A9DFBF',
        CategoryEnum::SOUL->value => '#F5B7B1',
        CategoryEnum::BLUES->value => '#D2B4DE',
        CategoryEnum::TRAP->value => '#85D8CE',
        CategoryEnum::HOUSE->value => '#F9E79F',
        CategoryEnum::TECHNO->value => '#AEB6BF',
    ];

    public function getData(): array
    {
        $categories = [];
        foreach (CategoryEnum::cases() as $category) {
            $categories[] = [
                'name' => $category->value,
                'color' => self::COLORS[$category->value] ?? '#FFFFFF',
            ];
        }

        return $categories;
    }
}
