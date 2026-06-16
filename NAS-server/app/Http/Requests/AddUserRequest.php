<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddUserRequest extends FormRequest
{
    protected $redirectRoute = 'users.create';
    public function authorize(): bool
    {
        return true; // Active la validation pour tout le monde
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string|min:4|max:50',
            'password' => 'required|string|min:4',
            'shell'    => 'nullable|string',
        ];
    }
}