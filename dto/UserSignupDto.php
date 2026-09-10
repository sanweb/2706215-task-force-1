<?php

declare(strict_types=1);

namespace app\dto;

final readonly class UserSignupDto
{
    public function __construct(
        public string $name = '',
        public string $email = '',
        public string $password = '',
        public string $passwordConfirm = '',
        public ?int $cityId = null,
        public bool $isExecutor = false,
    ) {}
}
