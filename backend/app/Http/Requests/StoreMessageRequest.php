<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // endpoint público
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'subject' => ['required', 'string', 'max:150'],
            'body'    => ['required', 'string', 'max:3000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'El nombre es obligatorio.',
            'email.required'   => 'El correo es obligatorio.',
            'email.email'      => 'Introduce un correo válido.',
            'subject.required' => 'El asunto es obligatorio.',
            'body.required'    => 'El mensaje no puede estar vacío.',
            'body.max'         => 'El mensaje no puede superar los 3000 caracteres.',
        ];
    }
}
