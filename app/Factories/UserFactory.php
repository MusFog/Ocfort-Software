<?php

namespace App\Factories;

use App\DTOs\UserDTO;
use App\Interfaces\UserFactoryInterface;
use App\Models\User;

class UserFactory implements UserFactoryInterface
{
    public function makeModel(UserDTO $userDTO): User
    {
        return new User($userDTO->toArray());
    }
}
