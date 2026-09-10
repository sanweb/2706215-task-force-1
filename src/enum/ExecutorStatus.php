<?php

declare(strict_types=1);


namespace Sanweb\Taskforce\enum;

enum ExecutorStatus: string
{
    case Available = 'available';
    case Busy = 'busy';
    case Unavailable = 'unavailable';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Открыт для новых заказов',
            self::Busy => 'Занят',
            self::Unavailable => 'Не принимает заказы',
        };
    }
}