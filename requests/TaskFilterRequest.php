<?php

declare(strict_types=1);

namespace app\requests;

use yii\base\Model;

class TaskFilterRequest extends Model
{
    public const array PERIODS = [
        '-1 hour' => 'За час',
        '-12 hours' => 'За 12 часов',
        '-24 hours' => 'За сутки',
    ];

    public array|string $categories = [];
    public string|int|bool $isRemote = false;
    public string|int|bool $hasNoBid = false;
    public string $period = '';

    public function rules(): array
    {
        return [
            [
                ['categories'],
                'filter',
                'filter' => static fn($value): array => $value === '' || $value === null ? [] : (array) $value,
            ],
            [['categories'], 'each', 'rule' => ['integer']],
            [['isRemote', 'hasNoBid'], 'boolean'],
            [['period'], 'in', 'range' => array_keys(self::PERIODS)],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'categories' => 'Категории',
            'isRemote' => 'Удалённая работа',
            'hasNoBid' => 'Без откликов',
            'period' => 'Период',
        ];
    }

    public function formName(): string
    {
        return 'filters';
    }
}
