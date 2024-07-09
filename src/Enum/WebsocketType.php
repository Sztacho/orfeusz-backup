<?php

namespace App\Enum;

enum WebsocketType : string
{
    case CONNECTION = 'connection';
    case SYSTEM = 'system';
    case DEFAULT = 'message_default';
}