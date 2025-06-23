<?php

namespace App\Service;

use App\Entity\User;
use App\Enum\EmailTypeEnum;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Exception\InvalidArgumentException;

class Mailer
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    private function send(User $user, string $subject, EmailTypeEnum $type, array $context = []): void
    {
        $to = $user->getEmail();
        if (!$to) {
            throw new InvalidArgumentException('User email is required');
        }

        $template = (new TemplatedEmail())
            ->to($to)
            ->from('team@beatly.fr')
            ->subject($subject)
            ->htmlTemplate(sprintf('email/%s.html.twig', $type->value))
            ->context($context);

        $this->mailer->send($template);
    }
}

