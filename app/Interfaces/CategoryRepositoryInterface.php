<?php

namespace App\Interfaces;

use App\DTOs\CategoryDTO;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    public function getAll(): Collection;
    public function create(CategoryDTO $attrs): void;
    public function update(string $id, string $title, ?string $userId = null): void;
    public function toggleUser(string $id, string $userId): void;
    public function delete(string $id): void;
}
