<?php

namespace App\DTOs;

class UserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $id = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email']
        );
    }

    public function withId(string $id): self
    {
        return new self(
            name: $this->name,
            email: $this->email,
            id: $id
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'id' => $this->id
        ];
    }
} 