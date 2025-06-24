<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\DTOs\UserDTO;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
    }

    public function findById(string $id): UserDTO
    {
        $user = User::find($id);
        return UserDTO::fromObject($user->toArray());
    }

    public function getAll(): Collection
    {
        return User::all();
    }

    public function create(UserDTO $userDTO): UserDTO
    {
        $user = User::create([
            'id' => $userDTO->id,
            'name' => $userDTO->name,
            'email' => $userDTO->email,
            'role' => $userDTO->role
        ]);

        return UserDTO::fromObject($user->toArray());
    }

    public function update(string $id, string $name): UserDTO
    {
        $user = User::find($id);
        $user->update(['name' => $name]);

        return UserDTO::fromObject($user->toArray());
    }

    public function deleteWithCategories(string $id): UserDTO
    {
        $user = User::find($id);
        $user->categories()->detach();
        $user->delete();

        return UserDTO::fromObject($user->toArray());
    }

    public function getDB(string $sql): Collection
    {
        return new Collection(DB::select($sql));
    }
}
