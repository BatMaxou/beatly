<?php

namespace App\Api\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Api\Provider\DashboardProvider;
use App\Enum\ApiReusableRoute;
use App\Enum\VoterRoleEnum;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/dashboard',
            provider: DashboardProvider::class,
            name: ApiReusableRoute::GET_DASHBOARD->value,
            normalizationContext: ['groups' => ['dashboard:read']],
            security: 'is_granted("'.VoterRoleEnum::UNBANED->value.'")',
        ),
    ],
)]
class Dashboard
{
    public function __construct(
        public array $lastListened = [],
        public array $mostPopularCategories = [],
        public array $mostLikedMusics = [],
        public array $mostListenedMusics = [],
        public array $mostLikedPlaylists = [],
    ) {
    }
}
