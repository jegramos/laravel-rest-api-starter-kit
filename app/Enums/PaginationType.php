<?php

namespace App\Enums;

enum PaginationType: string
{
    case LENGTH_AWARE = 'LENGTH_AWARE';
    case SIMPLE = 'SIMPLE';
    case CURSOR = 'CURSOR';
}
