<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
                'razao_social' => 'required|string|unique:users',
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

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cnpj.required' => 'The CNPJ field is required.',
            'cnpj.unique' => 'The CNPJ has already been taken.',
            'nome_fantasia.required' => 'The Nome Fantasia field is required.',
            'razao_social.required' => 'The Razao Social field is required.',
            'razao_social.unique' => 'The Razao Social has already been taken.',
            'endereco.required' => 'The Endereco field is required.',
            'telefone.required' => 'The Telefone field is required.',
            'email.required' => 'The Email field is required.',
            'email.unique' => 'The Email has already been taken.',
            'password.required' => 'The Password field is required.',
            'c_password.required' => 'The Confirm Password field is required.',
            'c_password.same' => 'The Confirm Password must match the Password.',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
