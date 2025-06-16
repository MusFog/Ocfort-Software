<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserServiceInterface;
use App\DTOs\UserDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use App\Jobs\SendEmail;

class UserService implements UserServiceInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private UserRepositoryInterface $userRepository
        )
    {
        
    }

    public function list(): Collection {
        return $this->userRepository->getAll();
    }

    public function register(UserDTO $data): void
    {
        $dataWithId = $data->withId(Str::uuid());
        $this->userRepository->create($dataWithId);
        
        SendEmail::dispatch($dataWithId->id, now()->addMinutes(5));
    }

    public function rename(string $id, string $name): void
    {
        $this->userRepository->update($id, $name);
    }

    public function remove(string $id): void
    {
        $this->userRepository->deleteWithCategories($id);
    }
}
