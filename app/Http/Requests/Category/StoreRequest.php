<?php

namespace App\Http\Requests\Category;

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
            'name' => 'required|string|min:3|max:150, unique:categories,name' . $this->route('category.id')
        ];
    }
}
