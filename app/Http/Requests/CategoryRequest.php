<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Set authorization logic here, e.g., return true if all users are authorized to make this request.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'slug' => 'required|string|unique:categories,slug,' . $this->category->id,
            'parent_id' => 'required|exists:categories,id',
            'attribute_ids' => 'required|array',
            'attribute_ids.*' => 'exists:attributes,id', // Validate each element in the array
            'attribute_is_filter_ids' => 'required|array',
            'attribute_is_filter_ids.*' => 'boolean', // Validate each element in the array as boolean
            'variation_id' => 'required|exists:attributes,id',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'icon' => 'nullable|string',
        ];
    }
}
