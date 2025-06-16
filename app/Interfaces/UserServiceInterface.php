<?php

namespace App\Interfaces;

use App\DTOs\UserDTO;
use Illuminate\Support\Collection;

interface UserServiceInterface
{
    public function list(): Collection;
    public function register(UserDTO $data): void;
    public function rename(string $id, string $name): void;
    public function remove(string $id): void;
}
