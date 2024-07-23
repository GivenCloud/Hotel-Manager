<?php

namespace App\Http\Requests\Guest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
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

     public function failedValidation (\Illuminate\Contracts\Validation\Validator $validator) {
        if ($this->expectsJson()) {
            $response = New Response($validator->errors(), 400);
            throw new ValidationException($validator, $response);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:30',
            'lastName' => 'required|string|min:3|max:30',
            'dniPassport' => 'required|string|min:9|max:15|unique:guests,dniPassport,'.$this->route('guest.id'),
            'email' => 'required|email|max:255|unique:guests,email,'.$this->route('guest.id'),
            'phone' => 'required|string|min:9|max:9|unique:guests,phone,'.$this->route('guest.id'),
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
            'dniPassport.min' => 'The dniPassport field must be at least 9 characters',
            'dniPassport.max' => 'The dniPassport field must be at most 15 characters',
            'dniPassport.unique' => 'The dniPassport field must be unique',
            'email.required' => 'The email field is required',
            'email.email' => 'The email field must be a valid email',
            'email.max' => 'The email field must be at most 255 characters',
            'email.unique' => 'The email field must be unique',
            'phone.required' => 'The phone field is required',
            'phone.string' => 'The phone field must be a string',
            'phone.min' => 'The phone field must be at least 9 characters',
            'phone.max' => 'The phone field must be at most 9 characters',
            'phone.unique' => 'The phone field must be unique',
        ];
    }
}
