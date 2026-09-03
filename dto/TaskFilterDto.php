<?php

declare(strict_types=1);

namespace app\dto;

final readonly class TaskFilterDto
{
    public function __construct(
        public array $categories = [],
        public bool $isRemote = false,
        public bool $hasNoBid = false,
        public ?int $createdAfter = null,
    ) {}
}
