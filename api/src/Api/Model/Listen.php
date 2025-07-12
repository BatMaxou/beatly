<?php

namespace App\Api\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Domain\Command\ListenCommand;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/listen',
            name: 'api_listen',
            messenger: 'input',
            input: ListenCommand::class,
        ),
    ],
)]
class Listen
{
}
