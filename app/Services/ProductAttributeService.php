<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductAttribute;

class ProductAttributeService
{
    public function store(array $attributeIds, Product $product)
    {
        foreach ($attributeIds as $key => $value) {
            ProductAttribute::query()->create([
                'product_id' => $product->id,
                'attribute_id' => $key,
                'value' => $value
            ]);
        }
    }
    public function update($attributeIds)
    {
        foreach ($attributeIds as $key => $value) {
            $productAttibute = ProductAttribute::findOrFail($key);
            $productAttibute->update([
                'value' => $value
            ]);
        }
    }
    public function change($attributes, $product)
    {
        ProductAttribute::where('product_id' , $product->id)->delete();

        foreach ($attributes as $key => $value) {
            ProductAttribute::create([
                'product_id' => $product->id,
                'attribute_id' => $key,
                'value' => $value
            ]);
        }
    }
}
