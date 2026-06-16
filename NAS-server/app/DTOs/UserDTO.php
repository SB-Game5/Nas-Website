<?php
namespace App\DTOs;

use App\Http\Requests\AddUserRequest;

class UserDTO
{
    public function __construct(
        public readonly string $username,
        public readonly string $password,
        public readonly string $shell
    ) {}

    // Permet de créer le DTO directement depuis une requête validée
    public static function fromRequest(AddUserRequest $request): self
    {
        return new self(
            username: (string) $request->input('username'),
            password: (string) $request->input('password'),
            shell: (string) $request->input('shell', '/bin/bash')
        );
    }

    // Convertit le DTO en tableau pour la requête HTTP
    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
            'shell'    => $this->shell,
        ];
    }
}