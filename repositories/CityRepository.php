<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\City;
use Override;

final class CityRepository implements CityRepositoryInterface
{
    #[Override]
    public function findAllForSelect(): array
    {
        return City::find()
            ->select(['name', 'id'])
            ->orderBy(['name' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }
}
