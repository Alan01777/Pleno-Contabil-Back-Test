<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Route; // Add this import statement
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
        if ($this->routeIs('register')) { // Use the routeIs method from the Route facade
            return [
                'cnpj' => 'required|string|unique:users',
                'nome_fantasia' => 'required|string',
                'razao_social' => 'required|string|unique:users',
                'porte' => 'required|string|in:MEI,ME,EPP,LTDA,SA',
                'endereco' => 'required|string',
                'telefone' => 'required|string',
                'email' => 'required|string|unique:users',
                'password' => 'required|string|min:8',
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
            'cnpj.required' => 'O campo CNPJ é obrigatório.',
            'cnpj.unique' => 'Este CNPJ já está em uso.',
            'nome_fantasia.required' => 'O campo Nome Fantasia é obrigatório.',
            'razao_social.required' => 'O campo Razão Social é obrigatório.',
            'razao_social.unique' => 'Esta Razão Social já está em uso.',
            'endereco.required' => 'O campo Endereço é obrigatório.',
            'telefone.required' => 'O campo Telefone é obrigatório.',
            'email.required' => 'O campo Email é obrigatório.',
            'email.unique' => 'Este Email já está em uso.',
            'password.required' => 'O campo Senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'c_password.required' => 'O campo Confirmar Senha é obrigatório.',
            'c_password.same' => 'A Confirmar Senha deve ser igual à Senha.',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
