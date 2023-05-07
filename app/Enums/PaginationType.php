<?php

namespace App\Enums;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;

enum PaginationType: string
{
    case LENGTH_AWARE = LengthAwarePaginator::class;
    case SIMPLE = Paginator::class;
    case CURSOR = CursorPaginator::class;
    
}
