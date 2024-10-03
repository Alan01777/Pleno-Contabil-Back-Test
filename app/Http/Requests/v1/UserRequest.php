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
            'cnpj.string' => 'The CNPJ must be a string.',
            'cnpj.regex' => 'The CNPJ must be in the format 56.223.874/0001-62.',
            'cnpj.unique' => 'The CNPJ has already been taken.',
            'razao_social.string' => 'The Razao Social must be a string.',
            'razao_social.max' => 'The Razao Social may not be greater than 255 characters.',
            'nome_fantasia.string' => 'The Nome Fantasia must be a string.',
            'nome_fantasia.max' => 'The Nome Fantasia may not be greater than 255 characters.',
            'endereco.string' => 'The Endereco must be a string.',
            'endereco.max' => 'The Endereco may not be greater than 255 characters.',
            'telefone.string' => 'The Telefone must be a string.',
            'telefone.max' => 'The Telefone may not be greater than 15 characters.',
            'email.required' => 'The Email field is required.',
            'email.string' => 'The Email must be a string.',
            'email.email' => 'The Email must be a valid email address.',
            'email.max' => 'The Email may not be greater than 255 characters.',
            'email.unique' => 'The Email has already been taken.',
            'password.required' => 'The Password field is required.',
            'password.string' => 'The Password must be a string.',
            'password.min' => 'The Password must be at least 8 characters.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
