<?php
namespace App\DTOs;

use App\Http\Requests\ListInfoRequest;

class ListInfoDTO
{
    public function __construct(
        public readonly string $username,
        public readonly string $selectedPath,
    ) {}

    public static function fromRequest(ListInfoRequest $request): self // create DTO
    {
        return new self(
            username: (string) $request->input('username'),
            selectedPath: (string) $request->input('selectedPath')
        );
    }

    
    public function toArray(): array // convert DTO for HTTP request
    {
        return [
            'username' => $this->username,
            'selectedPath' => $this->selectedPath,
        ];
    }
}