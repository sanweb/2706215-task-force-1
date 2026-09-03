<?php

declare(strict_types=1);

namespace app\components;

use yii\i18n\Formatter;

final class AppFormatter extends Formatter
{
    public function asPhone(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if (!preg_match('/^\+7(\d{3})(\d{3})(\d{2})(\d{2})$/', $value, $matches)) {
            return $value;
        }

        return "+7 ({$matches[1]}) {$matches[2]}-{$matches[3]}-{$matches[4]}";
    }
}
