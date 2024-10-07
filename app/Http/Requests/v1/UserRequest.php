<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

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
            $userId = Auth::id();
            return [
                'cnpj' => ['sometimes', 'string', 'regex:/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/', 'unique:users,cnpj,' . $userId],
                'razao_social' => 'sometimes|string|max:255',
                'nome_fantasia' => 'sometimes|string|max:255',
                'endereco' => 'sometimes|string|max:255',
                'telefone' => 'sometimes|string|max:15',
                'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $userId],
                'password' => 'sometimes|string|min:8',
            ];
        }

        return [
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cnpj.string' => 'O CNPJ deve ser uma string.',
            'cnpj.regex' => 'O CNPJ deve estar no formato 56.223.874/0001-62.',
            'cnpj.unique' => 'O CNPJ já foi utilizado.',
            'razao_social.string' => 'A Razão Social deve ser uma string.',
            'razao_social.max' => 'A Razão Social não deve ter mais de 255 caracteres.',
            'nome_fantasia.string' => 'O Nome Fantasia deve ser uma string.',
            'nome_fantasia.max' => 'O Nome Fantasia não deve ter mais de 255 caracteres.',
            'endereco.string' => 'O Endereço deve ser uma string.',
            'endereco.max' => 'O Endereço não deve ter mais de 255 caracteres.',
            'telefone.string' => 'O Telefone deve ser uma string.',
            'telefone.max' => 'O Telefone não deve ter mais de 15 caracteres.',
            'email.required' => 'O campo Email é obrigatório.',
            'email.string' => 'O Email deve ser uma string.',
            'email.email' => 'O Email deve ser um endereço de email válido.',
            'email.max' => 'O Email não deve ter mais de 255 caracteres.',
            'email.unique' => 'O Email já foi utilizado.',
            'password.required' => 'O campo Senha é obrigatório.',
            'password.string' => 'A Senha deve ser uma string.',
            'password.min' => 'A Senha deve ter pelo menos 8 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
