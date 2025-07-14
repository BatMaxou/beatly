<?php

namespace App\Event\Subscriber;

use App\Event\Event\AddMultipleNextToQueueEvent;
use App\Event\Event\AddMultipleToQueueEvent;
use App\Event\Event\AddNextToQueueEvent;
use App\Event\Event\AddToQueueEvent;
use App\Event\Event\ResetQueueEvent;
use App\Service\QueueManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class QueueSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly QueueManager $queueManager,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AddToQueueEvent::class => 'onAdd',
            AddNextToQueueEvent::class => 'onAddNext',
            AddMultipleToQueueEvent::class => 'onAddMultiple',
            AddMultipleNextToQueueEvent::class => 'onAddMultipleNext',
            ResetQueueEvent::class => 'onReset',
        ];
    }

    public function onAdd(AddToQueueEvent $event): void
    {
        $added = $event->added;
        $user = $event->getUser();
        $queue = $this->queueManager->getQueue($user);

        $this->queueManager->addToQueue($queue, $added);

        $this->em->flush();
    }

    public function onAddNext(AddNextToQueueEvent $event): void
    {
        $user = $event->getUser();
        $queue = $this->queueManager->getQueue($user);

        $this->queueManager->addNextToQueue(
            $queue,
            $event->added,
            $event->currentPosition
        );

        $this->em->flush();
    }

    public function onAddMultiple(AddMultipleToQueueEvent $event): void
    {
        $user = $event->getUser();
        $queue = $this->queueManager->getQueue($user);

        $this->queueManager->addMusicsToQueue($queue, $event->added);

        $this->em->flush();
    }

    public function onAddMultipleNext(AddMultipleNextToQueueEvent $event): void
    {
        $user = $event->getUser();
        $queue = $this->queueManager->getQueue($user);

        $this->queueManager->addNextMusicsToQueue(
            $queue,
            $event->added,
            $event->currentPosition
        );

        $this->em->flush();
    }

    public function onReset(ResetQueueEvent $event): void
    {
        $user = $event->getUser();
        $queue = $this->queueManager->getQueue($user);
        $queue->clearQueueItems();

        $this->em->flush();
    }
}
