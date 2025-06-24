<?php

namespace App\Interfaces;
use Illuminate\Database\Eloquent\Collection;
use App\DTOs\UserDTO;

interface UserRepositoryInterface
{
    public function findById(string $id): UserDTO;
    public function getAll(): Collection;
    public function getDB(string $sql): Collection;
    public function create(UserDTO $userDTO): UserDTO;
    public function update(string $id, string $name): UserDTO;
    public function deleteWithCategories(string $id): UserDTO;
}
