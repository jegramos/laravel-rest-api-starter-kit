<?php

namespace App\Enums;

enum Role: string
{
    case STANDARD_USER = 'standard_user';
    case ADMIN = 'admin';
    case SYSTEM_SUPPORT = 'system_support';
    case SUPER_USER = 'super_user';
}
