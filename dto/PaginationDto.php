<?php

declare(strict_types=1);

namespace app\dto;

final readonly class PaginationDto
{
    public const DEFAULT_PAGE_SIZE = 5;

    public function __construct(
        public int $page = 1,
        public int $pageSize = self::DEFAULT_PAGE_SIZE,
    ) {}
}
