<?php

namespace App\Interfaces;

use App\DTOs\UserDTO;
use App\Enums\UserRole;
use Illuminate\Support\Collection;

interface UserServiceInterface
{
    public static function getRole(int $userRole): UserRoleInterface;
    public function getRoleAnswerSayHello(int $userRole, string $userName): string;
    public function getRoleAnswerSayBay(int $userRole, string $userName): string;
    public function list(): Collection;
    public function register(UserDTO $data): string;
    public function update(string $id, string $name, int $role): string;
    public function remove(string $id): string;
    public function findTopRoleUserList(int $limit): Collection;
}
