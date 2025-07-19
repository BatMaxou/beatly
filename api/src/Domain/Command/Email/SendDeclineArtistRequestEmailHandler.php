<?php

namespace App\Domain\Command\Email;

use App\Service\Mailer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendDeclineArtistRequestEmailHandler
{
    public function __construct(
        private readonly Mailer $mailer,
    ) {
    }

    public function __invoke(SendDeclineArtistRequestEmailCommand $command): void
    {
        $this->mailer->sendDeclineArtistRequestEmail($command->user);
    }
}
