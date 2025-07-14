<?php

namespace App\Event\Subscriber;

use App\Event\Event\AddMultipleNextToQueueEvent;
use App\Event\Event\AddMultipleToQueueEvent;
use App\Event\Event\AddNextToQueueEvent;
use App\Event\Event\AddToQueueEvent;
use App\Event\Event\ClearRandomQueueEvent;
use App\Event\Event\GenerateRandomQueueEvent;
use App\Event\Event\Interface\UserEvent;
use App\Event\Event\ResetQueueEvent;
use App\Service\QueueManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RandomQueueSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly QueueManager $queueManager,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            GenerateRandomQueueEvent::class => 'onGenerate',
            AddToQueueEvent::class => 'onAdd',
            AddNextToQueueEvent::class => 'onAddNext',
            AddMultipleToQueueEvent::class => 'onAddMultiple',
            AddMultipleNextToQueueEvent::class => 'onAddMultipleNext',
            ResetQueueEvent::class => 'onReset',
            ClearRandomQueueEvent::class => 'onClear',
        ];
    }

    public function onGenerate(GenerateRandomQueueEvent $event): void
    {
        $user = $event->getUser();
        $currentPosition = $event->currentPosition;

        $randomQueue = $user->getRandomQueue();
        if ($randomQueue) {
            return;
        }

        $queue = $this->queueManager->getQueue($user);
        $this->queueManager->randomizeQueueAfterPosition($queue, $currentPosition);
    }

    public function onAdd(AddToQueueEvent $event): void
    {
        $user = $event->getUser();
        $currentPosition = $event->currentPosition;
        if (null === $currentPosition || !$queue = $user->getRandomQueue()) {
            return;
        }

        $this->queueManager->addRandomToQueue($queue, $event->added, $currentPosition);

        $this->em->flush();
    }

    public function onAddNext(AddNextToQueueEvent $event): void
    {
        $user = $event->getUser();
        $currentPosition = $event->currentPosition;
        if (!$currentPosition || !$queue = $user->getRandomQueue()) {
            return;
        }

        $this->queueManager->addNextToQueue($queue, $event->added, $currentPosition);

        $this->em->flush();
    }

    public function onAddMultiple(AddMultipleToQueueEvent $event): void
    {
        $user = $event->getUser();
        $currentPosition = $event->currentPosition;
        if (null === $currentPosition || !$queue = $user->getRandomQueue()) {
            return;
        }

        $this->queueManager->addRandomMusicsToQueue($queue, $event->added, $currentPosition);

        $this->em->flush();
    }

    public function onAddMultipleNext(AddMultipleNextToQueueEvent $event): void
    {
        $user = $event->getUser();
        $currentPosition = $event->currentPosition;
        if (null === $currentPosition || !$queue = $user->getRandomQueue()) {
            return;
        }

        $this->queueManager->addNextMusicsToQueue($queue, $event->added, $currentPosition);

        $this->em->flush();
    }

    public function onReset(ResetQueueEvent $event): void
    {
        $this->clearRandomQueue($event);
    }

    public function onClear(ClearRandomQueueEvent $event): void
    {
        $this->clearRandomQueue($event);
    }

    private function clearRandomQueue(UserEvent $event): void
    {
        $user = $event->getUser();
        $queue = $user->getRandomQueue();
        if (!$queue) {
            return;
        }

        $this->em->remove($queue);
        $this->em->flush();
    }
}
