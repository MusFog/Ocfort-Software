<?php

namespace App\Repositories;

use App\Interfaces\CategoryRepositoryInterface;
use App\DTOs\CategoryDTO;
use App\Models\Category;
use Illuminate\Support\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAll(): Collection
    {
        return Category::all();
    }

    public function create(CategoryDTO $attrs): void
    {
        $category = Category::create([
            'id' => $attrs->id,
            'title' => $attrs->title
        ]);

        if (!empty($attrs->userId)) {
            $category->users()->attach($attrs->userId);
        }
    }

    public function update(string $id, string $title, ?string $userId = null): void
    {
        $category = Category::find($id);

        $category->update(['title' => $title]);

        if ($userId) {
            $category->users()->sync($userId);
        }
    }

    public function delete(string $id): void
    {
        $category = Category::find($id);
        $category->users()->detach();
        $category->delete();
    }
}
