<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\Category;

final class CategoryRepository
{
    /**
     * Returns all categories sorted by name.
     *
     * @return Category[]
     */
    public function findAll(): array
    {
        return Category::find()->orderBy(['name' => SORT_ASC])->all();
    }
}
