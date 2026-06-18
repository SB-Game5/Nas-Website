<?php
namespace App\DTOs;

use App\Http\Requests\DeleteUserRequest;

class DeleteUserDTO
{
    public function __construct(
        public readonly string $username,
    ) {}

    public static function fromRequest(DeleteUserRequest $request): self // create DTO
    {
        return new self(
            username: (string) $request->input('username'),
        );
    }

    
    public function toArray(): array // convert DTO for HTTP request
    {
        return [
            'username' => $this->username,
        ];
    }
}