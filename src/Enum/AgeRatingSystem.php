<?php

namespace App\Enum;

enum AgeRatingSystem : string
{
    case G = 'General Audiences';
    case PG12 = 'Parental Guidance Requested';
    case R15 = 'Restricted to teenagers 15 and over only';
    case R18 = 'Restricted to adults 18 and over only';
}
