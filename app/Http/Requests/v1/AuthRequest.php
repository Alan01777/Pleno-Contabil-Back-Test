<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
        if ($this->routeIs('register')) {
            return [
                'cnpj' => 'required|string|unique:users',
                'nome_fantasia' => 'required|string',
                'razao_social'=> 'required|string|unique:users',
                'endereco' => 'required|string',
                'telefone' => 'required|string',
                'email' => 'required|string|unique:users',
                'password' => 'required|string',
                'c_password' => 'required|same:password',
            ];
        }

        if ($this->routeIs('login')) {
            return [
                'email' => 'required|string',
                'password' => 'required|string',
            ];
        }

        return [];
    }
}