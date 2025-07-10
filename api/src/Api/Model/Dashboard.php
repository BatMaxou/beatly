<?php

namespace App\Api\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Api\Provider\DashboardProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/dashboard',
            provider: DashboardProvider::class,
            name: 'api_dashboard',
            normalizationContext: ['groups' => ['dashboard:read']],
        )
    ],
)]
class Dashboard
{
    public function __construct(
        public array $lastListened = [],
        public array $mostPopularCategories = [],
        public array $mostLikedMusics = [],
        public array $mostListenedMusics = [],
    ){
    }
}
