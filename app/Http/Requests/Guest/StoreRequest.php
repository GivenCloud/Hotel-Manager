<?php

namespace App\Http\Requests\Guest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreRequest extends FormRequest
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
        return [
            'name' => 'required|string|min:3|max:30',
            'lastName' => 'required|string|min:3|max:30',
            'dniPassport' => [
                'required',
                'string',
                'min:8',
                'max:15',
                function ($attribute, $value, $fail) {
                    // Expresión regular para DNI y pasaporte
                    $pattern = '/^(?:\d{8}[A-Z]|[A-Z]{3}\d{6}[A-Z])$/';
                    if (!preg_match($pattern, $value)) {
                        $fail('The '.$attribute.' format is invalid.');
                    }
                },
                Rule::unique('guests', 'dniPassport')->ignore($this->route('guest')),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('guests', 'email')->ignore($this->route('guest')),
            ],
            'phone' => [
                'required',
                'integer',
                'digits:9',
                Rule::unique('guests', 'phone')->ignore($this->route('guest')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required',
            'name.string' => 'The name field must be a string',
            'name.min' => 'The name field must be at least 3 characters',
            'name.max' => 'The name field must be at most 30 characters',
            'lastName.required' => 'The lastName field is required',
            'lastName.string' => 'The lastName field must be a string',
            'lastName.min' => 'The lastName field must be at least 3 characters',
            'lastName.max' => 'The lastName field must be at most 30 characters',
            'dniPassport.required' => 'The dniPassport field is required',
            'dniPassport.string' => 'The dniPassport field must be a string',
            'dniPassport.min' => 'The dniPassport field must be at least 8 characters',
            'dniPassport.max' => 'The dniPassport field must be at most 15 characters',
            'dniPassport.regex' => 'The dniPassport field format is invalid',
            'dniPassport.unique' => 'The dniPassport field must be unique',
            'email.required' => 'The email field is required',
            'email.email' => 'The email field must be a valid email',
            'email.max' => 'The email field must be at most 255 characters',
            'email.unique' => 'The email field must be unique',
            'phone.required' => 'The phone field is required',
            'phone.integer' => 'The phone field must be an integer',
            'phone.digits' => 'The phone field must be 9 digits',
            'phone.unique' => 'The phone field must be unique',
        ];
    }
}
