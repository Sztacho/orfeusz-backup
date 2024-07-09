<?php

namespace App\Enum;

enum EventType : string
{
    case ANIME_ADD_EVENT = 'anime_add_event';
    case ANIME_EDIT_EVENT = 'anime_edit_event';
    case ANIME_DELETE_EVENT = 'anime_delete_event';
    case ANIME_EPISODE_ADD_EVENT = 'anime_episode_add_event';
    case ANIME_EPISODE_EDIT_EVENT = 'anime_episode_edit_event';
    case ANIME_EPISODE_DELETE_EVENT = 'anime_episode_delete_event';
}