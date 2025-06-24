<?php

namespace App\Services;

use App\Interfaces\UserRoleInterface;

class RoleContextService
{
    public function __construct(
        public UserRoleInterface $userRole,
    ) {}

    public function showSayHello(string $userName): string
    {
        return $this->userRole->sayHello($userName);
    }

    public function showSayBay(string $userName): string
    {
        return $this->userRole->sayBay($userName);
    }
}
