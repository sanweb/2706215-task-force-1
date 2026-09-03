<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    public function findExecutorById(int $id): ?User;
}
