<?php

namespace App\Api\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Api\Provider\RecommendationProvider;
use App\Entity\Music;
use App\Enum\ApiReusableRoute;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/dashboard/recommendations',
            provider: RecommendationProvider::class,
            name: ApiReusableRoute::GET_RECOMMENDATIONS->value,
            normalizationContext: ['groups' => ['recommendation:read']],
        ),
    ],
)]
class Recommendation
{
    /**
     * @param Music[] $recommendations
     */
    public function __construct(
        public array $recommendations = [],
    ) {
    }
}
