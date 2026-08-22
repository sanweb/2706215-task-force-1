<?php

declare(strict_types=1);

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

final class UserFixture extends ActiveFixture
{
    public $modelClass = \app\models\User::class;
}
