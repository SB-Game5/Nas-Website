<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListInfoRequest extends FormRequest
{
    protected $redirectRoute = 'dashboard';
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string|min:4|max:50',
            'selectedPath' => 'required|string|min:4',
        ];
    }
}