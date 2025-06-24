<?php

namespace App\Interfaces;

use App\DTOs\CategoryDTO;
use App\Models\Category;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    public function getAll(): Collection;
    public function create(CategoryDTO $attrs): Category;
    public function update(string $id, string $title, string $userId): Category;
    public function delete(string $id): void;
}
