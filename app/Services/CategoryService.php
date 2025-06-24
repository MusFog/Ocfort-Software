<?php

namespace App\Services;

use App\DTOs\ReportData;
use App\Enums\EmailType;
use App\Interfaces\CategoryServiceInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\DTOs\CategoryDTO;
use App\Interfaces\NotificationsServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CategoryService implements CategoryServiceInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private NotificationsServiceInterface $notificationsService
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

    public function update(CategoryDTO $data): void
    {
        $this->categoryRepository->update($data->id, $data->title, $data->userId);

        $this->notificationsService->sendEmail(
            $data->userId,
            EmailType::CATEGORY_SET,
            new ReportData($data->title),
            config('send_mail.category_action')
        );
    }

    public function delete(string $id): void
    {
        $this->categoryRepository->delete($id);
    }
}
