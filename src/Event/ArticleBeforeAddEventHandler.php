<?php

namespace App\Event;

use App\Entity\Article;
use DateTimeImmutable;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ArticleBeforeAddEventHandler implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['setArticleDate']
        ];
    }

    public function setArticleDate(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Article)) {
            return;
        }

        if (!$entity->getCreatedAt()) {
            $entity->setCreatedAt(new DateTimeImmutable());
        }

        if ($entity->getCreatedAt()) {
            $entity->setUpdatedAt(new DateTimeImmutable());
        }
    }
}