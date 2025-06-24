<?php

namespace App\Services\Roles;

use App\Interfaces\UserRoleInterface;

class Moderator implements UserRoleInterface
{

    public function sayHello(string $userName): string
    {
        return "Hello, $userName. You are Moderator.";
    }

    public function sayBay(string $userName): string
    {
        return "Bay, $userName. You are Moderator.";
    }
}
