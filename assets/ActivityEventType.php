<?php

declare(strict_types=1);

namespace App\Event;

enum ActivityEventType: string
{
    case Created = 'created';
    case Modified = 'modified';
    case Removed = 'removed';
}
