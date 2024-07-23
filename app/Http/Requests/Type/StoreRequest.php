<?php

namespace App\Http\Requests\Type;

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
            'name' => 'required|string|min:3|max:30, unique:types,name' . $this->route('type.id'),
            'price' => 'required|numeric|min:0|max: 9999.99',
            'capacity' => 'required|integer|min:1|max:14',
        ];
    }

    public function messages(): array 
    {
        return [
            'name.required' => 'The name field is required',
            'name.string' => 'The name field must be a string',
            'name.min' => 'The name field must be at least 3 characters',
            'name.max' => 'The name field must be at most 30 characters',
            'name.unique' => 'The name field must be unique',
            'price.required' => 'The price field is required',
            'price.numeric' => 'The price field must be a number',
            'price.min' => 'The price field must be at least 0',
            'price.max' => 'The price field must be at most 9999.99',
            'capacity.required' => 'The capacity field is required',
            'capacity.integer' => 'The capacity field must be an integer',
            'capacity.min' => 'The capacity field must be at least 1',
            'capacity.max' => 'The capacity field must be at most 14',
        ];
    }
}
