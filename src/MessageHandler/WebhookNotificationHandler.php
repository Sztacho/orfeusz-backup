<?php

namespace App\MessageHandler;

use App\Entity\Anime;
use App\Entity\Episode;
use App\Message\WebhookNotification;
use App\Notification\DiscordEpisodeNotification;
use App\Service\WebhookNotifierService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class WebhookNotificationHandler
{
    public function __construct(private DiscordEpisodeNotification $discordEpisodeNotification, private WebhookNotifierService $notifierService)
    {

    }

    public function __invoke(WebhookNotification $message): void
    {
        switch ($message->getContext()::class) {
            case Episode::class:
                $discord = $this->discordEpisodeNotification->getNotification($message->getContext());
                break;
            case Anime::class:
                break;
        }

        $this->notifierService->notify($message->getEventType(), $discord ?? []);
    }
}