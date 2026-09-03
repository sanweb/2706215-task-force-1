<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\Category;

interface CategoryRepositoryInterface
{
    /** @return Category[] */
    public function findAll(): array;
}
