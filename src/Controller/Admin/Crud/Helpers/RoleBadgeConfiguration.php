<?php

namespace App\Controller\Admin\Crud\Helpers;

readonly class RoleBadgeConfiguration
{
    public static function getRoleBadges(): array
    {
        return [
            //User roles
            'ROLE_USER' => 'success',
            //Administrator roles
            'ROLE_ADMIN' => 'danger',
            'ROLE_SUPER_ADMIN' => 'danger',
            'ROLE_ALLOWED_TO_SWITCH' => 'danger',

            //Crew roles
            'ROLE_CREW_TRANSLATOR' => 'warning',
            'ROLE_CREW_UPLOADER' => 'warning',
            'ROLE_CREW_COPY_READER' => 'warning',
            'ROLE_CREW_MODERATOR' => 'warning',
            'ROLE_CREW_ADMINISTRATOR' => 'warning',
            'ROLE_CREW_DEVELOPER' => 'warning',
        ];
    }
}