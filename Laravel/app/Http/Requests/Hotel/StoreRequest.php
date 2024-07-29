<?php

namespace App\Http\Requests\Hotel;

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

    public function failedValidation (\Illuminate\Contracts\Validation\Validator $validator) {
        if ($this->expectsJson()) {
            $response = New Response($validator->errors(), 400);
            throw new ValidationException($validator, $response);
        }
    }

    public function rules(): array
    {
        return [
            // 'name' => 'required|string|max:40',
            // 'address' => 'required|string|max:100',
            // 'phone' => 'required|string|min:9|max:9|unique:hotels,phone,'.$this->route('hotel.id'),
            // 'email' => 'required|string|email|max:255|unique:hotels,email,'.$this->route('hotel.id'),
            // 'website' => 'required|string|max:150|unique:hotels,website,'.$this->route('hotel.id'),
            'name' => 'required|string|max:40',
            'address' => 'required|string|max:100',
            'stars' => 'required|integer|between:1,5',
            'phone' => [
                'required',
                'integer',
                'digits:9',
                Rule::unique('hotels', 'phone')->ignore($this->route('hotel'))
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('hotels', 'email')->ignore($this->route('hotel'))
            ],
            'website' => [
                'required',
                'string',
                'max:150',
                Rule::unique('hotels', 'website')->ignore($this->route('hotel'))
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required',
            'name.string' => 'The name field must be a string',
            'name.max' => 'The name field must be at most 40 characters',
            'address.required' => 'The address field is required',
            'address.string' => 'The address field must be a string',
            'address.max' => 'The address field must be at most 100 characters',
            'phone.required' => 'The phone field is required',
            'phone.integer' => 'The phone field must be a integer',
            'phone.digits' => 'The phone field must be 9 digits',
            'phone.unique' => 'The phone field must be unique',
            'email.required' => 'The email field is required',
            'email.string' => 'The email field must be a string',
            'email.email' => 'The email field must be a valid email',
            'email.max' => 'The email field must be at most 255 characters',
            'email.unique' => 'The email field must be unique',
            'website.required' => 'The website field is required',
            'website.string' => 'The website field must be a string',
            'website.max' => 'The website field must be at most 150 characters',
            'website.unique' => 'The website field must be unique',
        ];
    }
}
