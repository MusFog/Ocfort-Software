<?php

namespace App\Interfaces;
use \Illuminate\Support\Collection;
use App\DTOs\UserDTO;

interface UserRepositoryInterface
{
    public function getAll(): Collection;
    public function create(UserDTO $attrs): void;
    public function update(string $id, string $name): void;
    public function deleteWithCategories(string $id): void;
}
