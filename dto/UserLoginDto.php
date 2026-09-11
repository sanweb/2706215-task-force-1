<?php

declare(strict_types=1);

namespace app\dto;

final readonly class UserLoginDto
{
    public function __construct(
        public string $email = '',
        public string $password = '',
    ) {}
}
