<?php

namespace App\DTOs;

class CategoryDTO
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $userId = null,
        public readonly ?string $id = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            title: $data['title'],
            userId: $data['user_id']
        );
    }

    public function withId(string $id): self
    {
        return new self(
            title: $this->title,
            userId: $this->userId,
            id: $id
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'user_id' => $this->userId,
            'id' => $this->id
        ];
    }
}
