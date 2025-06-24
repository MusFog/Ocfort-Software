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

    public function create(CategoryDTO $attrs): Category
    {
        $category = Category::create([
            'id' => $attrs->id,
            'title' => $attrs->title
        ]);

        if (!empty($attrs->userId)) {
            $category->users()->attach($attrs->userId);
        }

        return $category;
    }

    public function update(string $id, string $title, ?string $userId = null): Category
    {
        $category = Category::find($id);

        $category->update(['title' => $title]);

        if ($userId) {
            $category->users()->sync($userId);
        }

        return $category;
    }

    public function delete(string $id): void
    {
        $category = Category::find($id);
        $category->users()->detach();
        $category->delete();
    }
}
