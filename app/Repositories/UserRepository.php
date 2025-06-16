<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\DTOs\UserDTO;
use App\Models\User;
use Illuminate\Support\Collection;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
    }

    public function getAll(): Collection
    {
        return User::all();
    }

    public function create(UserDTO $attrs): void
    {
        User::create([
            'id' => $attrs->id,
            'name' => $attrs->name,
            'email' => $attrs->email
        ]);
    }

    public function update(string $id, string $name): void
    {
        $user = User::find($id);
        $user->update(['name' => $name]);
    }

    public function deleteWithCategories(string $id): void
    {
        $user = User::find($id);
        $user->categories()->detach();  
        $user->delete();
    }
}
