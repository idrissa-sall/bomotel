<?php

declare(strict_types=1);

namespace App\Enum;

enum RoomCategoryEnum: string
{
    case SINGLE = 'single';
    case DOUBLE = 'double';
    case TRIPLE = 'triple';
    case QUAD = 'quad';
    case FAMILIALE = 'familiale';
}
