<?php

declare(strict_types=1);

namespace app\widgets;

use yii\base\Widget;

final class RatingWidget extends Widget
{
    public const string SIZE_SMALL = 'small';
    public const string SIZE_BIG = 'big';

    private const int MAX_STARS = 5;

    public float $value;

    public string $size = self::SIZE_SMALL;

    public function run(): string
    {
        return $this->render('rating', [
            'value' => $this->value,
            'size' => $this->size,
            'maxStars' => self::MAX_STARS,
        ]);
    }
}
