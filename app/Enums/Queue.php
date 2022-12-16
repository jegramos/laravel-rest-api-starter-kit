<?php

namespace App\Enums;

enum Queue: string
{
    case EMAILS = 'emails';
    case DEFAULT = 'default';

    case NOTIFICATIONS = 'notifications';
}
