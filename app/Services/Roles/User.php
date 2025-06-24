<?php

namespace App\Services\Roles;

use App\Interfaces\UserRoleInterface;

class User implements UserRoleInterface
{

    public function sayHello(string $userName): string
    {
        return "Hello, $userName. You are User.";
    }

    public function sayBay(string $userName): string
    {
        return "Bay, $userName. You are User.";
    }
}
