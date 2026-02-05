<?php

namespace App\Api\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Domain\Command\ListenCommand;
use App\Enum\VoterRoleEnum;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/listen',
            name: 'api_listen',
            messenger: 'input',
            input: ListenCommand::class,
            security: 'is_granted("'.VoterRoleEnum::UNBANED->value.'")',
        ),
    ],
)]
class Listen
{
}
