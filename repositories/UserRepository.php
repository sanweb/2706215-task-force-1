<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\User;
use Override;

final class UserRepository implements UserRepositoryInterface
{
    #[Override]
    public function findById(int $id): ?User
    {
        return User::findOne($id);
    }

    #[Override]
    public function findExecutorById(int $id): ?User
    {
        return User::find()
            ->where([
                'id' => $id,
                'is_executor' => 1,
            ])
            ->with([
                'city',
                'categories',
                'executorProfile',
                'executorStats',
                'receivedReviews',
            ])
            ->one();
    }
}
