<?php

namespace App\Services;

use App\Interfaces\CategoryServiceInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\DTOs\CategoryDTO;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CategoryService implements CategoryServiceInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function list(): Collection
    {
        return $this->categoryRepository->getAll();
    }

    public function create(CategoryDTO $data): void
    {
        $dataWithId = $data->withId(Str::uuid());
        $this->categoryRepository->create($dataWithId);
    }

    public function update(string $id, CategoryDTO $data): void
    {
        if ($data->userId !== null) {
            $this->categoryRepository->userExists($id, $data->userId)
                ? $this->categoryRepository->detachUser($id, $data->userId)
                : $this->categoryRepository->attachUser($id, $data->userId);
        }
        
        $this->categoryRepository->update($id, $data->title);
    }

    public function delete(string $id): void
    {
        $this->categoryRepository->delete($id);
    }
}
