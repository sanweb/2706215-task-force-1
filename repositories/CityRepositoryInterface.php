<?php

declare(strict_types=1);

namespace app\repositories;

interface CityRepositoryInterface
{
    /** @return array<int, string> */
    public function findAllForSelect(): array;
}
