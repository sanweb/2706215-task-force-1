<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\enum;

use Sanweb\Taskforce\enum\trait\EnumNames;
use Sanweb\Taskforce\enum\trait\EnumValues;
use Sanweb\Taskforce\exception\MissingEnumLabelException;

enum TaskStatus: string
{
    use EnumNames, EnumValues;

    case New = 'new';
    case Canceled = 'canceled';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Новое',
            self::Canceled => 'Отменено',
            self::InProgress => 'В работе',
            self::Completed => 'Выполнено',
            self::Failed => 'Провалено',
            default => throw new MissingEnumLabelException(
                "Отображаемое название для статуса {$this->value} не задано"
            )
        };
    }
}
