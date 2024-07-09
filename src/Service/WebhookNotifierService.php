<?php

namespace App\Service;

use App\Client\WebhookClient;
use App\Enum\EventType;
use App\Repository\WebhookRepository;

readonly class WebhookNotifierService
{
    public function __construct(private WebhookClient $client, private WebhookRepository $webhookRepository)
    {
    }

    public function notify(EventType $eventType, array $data): void
    {
        foreach ($this->webhookRepository->findBy(['eventType' => $eventType->value, 'active' => true]) as $event) {
            $this->client->post($data, $event->getUrl());
        }
    }
}