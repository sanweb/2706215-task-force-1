<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\Category;
use Override;

final class CategoryRepository implements CategoryRepositoryInterface
{
    #[Override]
    public function findAll(): array
    {
        return Category::find()->orderBy(['name' => SORT_ASC])->all();
    }
}
