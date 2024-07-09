<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;


class RequestEventHandler implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onRequest'
        ];
    }

    public static function onRequest(RequestEvent $event): void
    {
        if($event->getRequest()->getContentTypeFormat() !== 'json') {
            return;
        }

        $event->getRequest()->request->replace(json_decode($event->getRequest()->getContent(), true));
    }
}