<?php

namespace App\Http\Requests\Service;

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
            'name' => 'required|string|max:30',
            Rule::unique('services', 'name')->ignore($this->route('service')),
            'description' => 'nullable|string|max:150',
            'category_id' => 'required|integer'        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required',
            'name.string' => 'The name field must be a string',
            'name.max' => 'The name field must be at most 30 characters',
            'name.unique' => 'The name field must be unique',
            'description.string' => 'The description field must be a string',
            'description.max' => 'The description field must be at most 150 characters',
            'category_id.required' => 'The category_id field is required',
            'category_id.integer' => 'The category_id field must be an integer',
        ];
    }
}
