<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\enum;

use Sanweb\Taskforce\enum\trait\EnumNames;
use Sanweb\Taskforce\enum\trait\EnumValues;
use Sanweb\Taskforce\exception\MissingEnumLabelException;

enum TaskAction: string
{
    use EnumNames, EnumValues;

    case Create = 'create';
    case Cancel = 'cancel';
    case Bid = 'bid';
    case Assign = 'assign';
    case Complete = 'complete';
    case Refuse = 'refuse';

    public function label(): string
    {
        return match ($this) {
            self::Create => 'Создать',
            self::Cancel => 'Отменить',
            self::Bid => 'Откликнуться',
            self::Assign => 'Назначить',
            self::Complete => 'Завершить',
            self::Refuse => 'Отказаться',
            default => throw new MissingEnumLabelException(
                "Отображаемое название для действия {$this->value} не задано"
            )
        };
    }
}
