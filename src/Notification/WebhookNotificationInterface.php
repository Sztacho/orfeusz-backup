<?php

namespace App\Notification;

interface WebhookNotificationInterface
{
    public function getNotification($object): array;
}