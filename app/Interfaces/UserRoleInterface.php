<?php

namespace App\Interfaces;

interface UserRoleInterface
{
    public function sayHello(string $userName): string;
    public function sayBay(string $userName): string;
}
