<?php

namespace App\Http\Requests\product;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequestStore extends FormRequest
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
            'name'=>'required',
            'brand_id' =>'required',
            'is_active' =>'required',
            'tag_ids' =>'required',
            'description' =>'required',
            'primary_image'=>'required|mimes:jpg,jpeg,png,svg',
            'images'=>'required',
            'images.*'=>'required|mimes:jpg,jpeg,png,svg',
            'category_id'=>'required',
            'attribute_ids' =>'required',
            'attribute_ids.*' =>'required',
            'variation_values.*.*' =>'required',
            'variation_values.price.*' =>'required',
            'variation_values.quantity.*' =>'required',
            'delivery_amount' =>'required|integer',
            'delivery_amount_per_product' =>'nullable|integer',
        ];
    }
}
