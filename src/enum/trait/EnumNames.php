<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\enum\trait;

trait EnumNames
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }
}
