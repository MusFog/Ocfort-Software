<?php

namespace App\Interfaces;

use App\DTOs\CategoryDTO;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    public function getAll(): Collection;
    public function create(CategoryDTO $attrs): void;
    public function update(string $id, string $title): void;
    public function userExists(string $catId, string $userId): bool;
    public function attachUser(string $catId, string $userId): void;
    public function detachUser(string $catId, string $userId): void;
    public function delete(string $id): void;
}
