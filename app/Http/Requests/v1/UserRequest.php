<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->routeis('user.update')) {
            return [
                'cnpj' => 'sometimes|string|max:14|unique:users,cnpj,' . $this->user,
                'razao_social' => 'sometimes|string|max:255',
                'nome_fantasia' => 'sometimes|string|max:255',
                'endereco' => 'sometimes|string|max:255',
                'telefone' => 'sometimes|string|max:15',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $this->user,
                'password' => 'sometimes|string|min:8',
            ];
        }
        return [
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ];
    }
}
