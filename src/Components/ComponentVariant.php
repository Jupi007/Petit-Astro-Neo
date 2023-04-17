<?php

declare(strict_types=1);

namespace App\Components;

enum ComponentVariant: string
{
    case Info = 'info';
    case Success = 'success';
    case Warning = 'warning';
    case Error = 'error';
}
