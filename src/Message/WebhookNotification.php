<?php

namespace App\Message;

use App\Enum\EventType;

class WebhookNotification
{
    public function __construct(
        private readonly int       $id,
        private readonly object    $context,
        private readonly EventType $eventType
    )
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getContext(): object
    {
        return $this->context;
    }

    public function getEventType(): EventType
    {
        return $this->eventType;
    }
}