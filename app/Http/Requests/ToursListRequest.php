<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ToursListRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'priceFrom' => ['sometimes', 'numeric', 'min:0'],
            'priceTo' => ['sometimes', 'numeric', 'min:0'],
            'dateFrom' => ['sometimes', 'date'],
            'dateTo' => ['sometimes', 'date'],
            'sortBy' => ['sometimes', Rule::in(['price'])],
            'sortOrder' => ['sometimes', Rule::in(['asc', 'desc'])],
        ];
    }

    public function messages(): array
    {
        return [
            'sortBy' => "The 'Sort-By' parameter accepts only 'price' value",
            'sortOrder' => "The 'Sort-Order' parameter accepts only 'asc' or 'desc' value",
        ];
    }
}
