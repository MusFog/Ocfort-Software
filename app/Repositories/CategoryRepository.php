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

    public function update(string $id, string $title): void
    {
        $category = Category::find($id);

        $category->update(['title' => $title]);
    }

    public function userExists(string $catId, string $userId): bool
    {
        return Category::find($catId)
            ->users()
            ->whereKey($userId)
            ->exists();
    }

    public function attachUser(string $catId, string $userId): void
    {
        Category::find($catId)
            ->users()
            ->attach($userId);
    }

    public function detachUser(string $catId, string $userId): void
    {
        Category::find($catId)
            ->users()
            ->detach($userId);
    }

    public function delete(string $id): void
    {
        $category = Category::find($id);
        $category->users()->detach();
        $category->delete();
    }
}
