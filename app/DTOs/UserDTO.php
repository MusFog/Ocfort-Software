<?php
namespace App\DTOs;

use App\Enums\UserRole;

class UserDTO
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?int $role = null,
    ) {}

    public static function fromObject(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            role: $data['role'] ?? null,
        );
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            role: $data['role'] ?? null,
        );
    }

    public function withId(string $id): self
    {
        return new self(
            id: $id,
            name: $this->name,
            email: $this->email,
            role: $this->role,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'role' => $this->role,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
