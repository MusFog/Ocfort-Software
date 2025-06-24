<?php

namespace App\Interfaces;

use App\DTOs\UserDTO;
use App\Models\User;

interface UserFactoryInterface
{
    public function makeModel(UserDTO $userDTO): User;
}
