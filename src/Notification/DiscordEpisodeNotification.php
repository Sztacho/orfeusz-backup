<?php

namespace App\Notification;

use App\Entity\Episode;
use Exception;

//Builder do obiektów epizodów 2 metody WEB + DC
class DiscordEpisodeNotification implements WebhookNotificationInterface
{

    /**
     * @throws Exception
     */
    public function getNotification($object): array
    {
        if (!$object instanceof Episode) {
            throw new Exception();
        }

        return [
            'content' => '|| <@&1057721401802489989> ||',
            'embeds' => [
                [
                    'title' => $object->getAnime()->getName(),
                    'description' => 'Nowy odcinek właśnie się pojawił\n [<a:a_cuteNekoHello:1076479934211555459> OGLĄDAJ!](' . $_ENV['WEB_APP_ANIME_URL'] . $object->getAnime()->getId() . ')',
                    'color' => 8454358,
                    'footer' => [
                        'text' => 'Odcinek ' . $object->getNumber()
                    ],
                    'timestamp' => date('Y-m-d\TH:i:sP'),
                    'thumbnail' => [
                        'url' => $_ENV['WEB_APP_ANIME_URL'] . '/assets/images/' . $object->getAnime()->getImage()
                    ]
                ]
            ],
            'username' => 'Orfeusz-Subs',
            'avatar_url' => 'https://images-ext-2.discordapp.net/external/7gSvOqZwmICZ9cDMBmilmKUaL45ynYwiUupF8LR9ng8/https/cdn.discordapp.com/icons/1057721401802489986/60bdc534228fdef1e66db4c25b2d25c8.png'
        ];
    }
}