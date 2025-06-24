<?php

namespace App\Services\Roles;

use App\Interfaces\UserRoleInterface;

class Admin implements UserRoleInterface
{

    public function sayHello(string $userName): string
    {
        return "Hello, $userName. You are Admin.";
    }

    public function sayBay(string $userName): string
    {
        return "Bay, $userName. You are Admin.";
    }
}
