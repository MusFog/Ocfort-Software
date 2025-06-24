<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Facades\Query;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserRoleInterface;
use App\Interfaces\UserServiceInterface;
use App\DTOs\UserDTO;
use App\Services\Roles\Admin;
use App\Services\Roles\Moderator;
use App\Services\Roles\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class UserService implements UserServiceInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function getRoleAnswerSayHello(int $userRole, string $userName): string
    {
        $strategy = $this->getRole($userRole);
        $roleContext = new RoleContextService($strategy);
        return $roleContext->showSayHello($userName);
    }

    public function getRoleAnswerSayBay(int $userRole, string $userName): string
    {
        $strategy = $this->getRole($userRole);
        $roleContext = new RoleContextService($strategy);
        return $roleContext->showSayBay($userName);
    }

    public static function getRole(int $userRole): UserRoleInterface
    {
        switch ($userRole) {
            case UserRole::ADMIN:
                $strategy = new Admin();
                break;

            case UserRole::MODERATOR:
                $strategy = new Moderator();
                break;

            case UserRole::USER:
                $strategy = new User();
                break;

            default:
                $strategy = new User();
                break;
        }
        return $strategy;
    }

    public function list(): Collection
    {
        return $this->userRepository->getAll();
    }

    public function register(UserDTO $data): string
    {
        $dataWithId = $data->withId(Str::uuid());
        $user = $this->userRepository->create($dataWithId);

        return $this->getRoleAnswerSayHello($user->role, $user->name);
    }

    public function update(string $id, string $name, int $role): string
    {
        $user = $this->userRepository->update($id, $name);

        return $this->getRoleAnswerSayHello($user->role, $user->name);
    }

    public function remove(string $id): string
    {
        $user = $this->userRepository->deleteWithCategories($id);

        return $this->getRoleAnswerSayBay($user->role, $user->name);
    }

    public function findTopRoleUserList(int $limit): Collection
    {
        $sql = Query::driver('sqlite')
            ->select('users', ['id', 'name', 'role'])
            ->where('role','=','0')
            ->limit(0, $limit)
            ->getSQL();

        return $this->userRepository->getDB($sql);
    }
}
