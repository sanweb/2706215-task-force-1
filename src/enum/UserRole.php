<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\enum;

use Sanweb\Taskforce\enum\trait\EnumNames;
use Sanweb\Taskforce\enum\trait\EnumValues;
use Sanweb\Taskforce\exception\MissingEnumLabelException;

enum UserRole: string
{
    use EnumNames, EnumValues;

    case Customer = 'customer';
    case Executor = 'executor';

    public function label(): string
    {
        return match ($this) {
            self::Customer => 'Заказчик',
            self::Executor => 'Исполнитель',
            default => throw new MissingEnumLabelException(
                "Отображаемое название для роли пользователя {$this->value} не задано"
            )
        };
    }
}
