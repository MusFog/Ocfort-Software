<?php

namespace App\Interfaces;

use App\DTOs\CategoryDTO;
use Illuminate\Support\Collection;

interface CategoryServiceInterface
{
    public function list(): Collection;
    public function create(CategoryDTO $data): void;
    public function update(CategoryDTO $data): void;
    public function delete(string $id): void;
}
