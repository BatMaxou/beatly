<?php

namespace App\Api\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Api\Provider\SearchProvider;
use App\Enum\ApiReusableRoute;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/search',
            provider: SearchProvider::class,
            name: ApiReusableRoute::SEARCH->value,
            normalizationContext: ['groups' => ['search:read']],
        ),
    ],
)]
class Search
{
    public function __construct(
        public array $results = [],
    ) {
    }
}
