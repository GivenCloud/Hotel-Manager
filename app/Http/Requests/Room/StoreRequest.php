<?php

namespace App\Http\Requests\Room;

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
            'number' => 'required|numeric|max:9999',
            Rule::unique('rooms', 'number')->ignore($this->route('room')),
            'type_id' => 'required|integer',
            'hotel_id' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'number.required' => 'The number field is required',
            'number.numeric' => 'The number field must be a number',
            'number.max' => 'The number field must be at most 9999',
            'number.unique' => 'The number field must be unique',
            'type_id.required' => 'The type_id field is required',
            'type_id.integer' => 'The type_id field must be an integer',
            'hotel_id.required' => 'The hotel_id field is required',
            'hotel_id.integer' => 'The hotel_id field must be an integer',
        ];
    }
}
