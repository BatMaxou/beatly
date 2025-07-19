<?php

namespace App\Domain\Command\Email;

use App\Service\Mailer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendAcceptArtistRequestEmailHandler
{
    public function __construct(
        private readonly Mailer $mailer,
        private readonly string $frontLoginUrl,
    ) {
    }

    public function __invoke(SendAcceptArtistRequestEmailCommand $command): void
    {
        $this->mailer->sendAcceptArtistRequestEmail($command->user, $this->frontLoginUrl);
    }
}
