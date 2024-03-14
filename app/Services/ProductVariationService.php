<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;

class ProductVariationService
{
    public function store(array $variation_values,Category $category,Product $product)
    {
        $counter = count($variation_values['value']);
        for ($i = 0; $i < $counter; $i++) {
            ProductVariation::query()->create([
                'attribute_id' => $category->attributes()->wherePivot('is_variation', 1)->first()->id,
                'product_id' => $product->id,
                'value' => $variation_values['value'][$i],
                'price' => $variation_values['price'][$i],
                'quantity' => $variation_values['quantity'][$i],
                'sku' => $variation_values['sku'][$i],
            ]);
        }
    }

    public function update($variationIds)
    {
        foreach($variationIds as $key => $value){
            $productVariation = ProductVariation::findOrFail($key);

            $productVariation->update([
                'price' => $value['price'],
                'quantity' => $value['quantity'],
                'sku' => $value['sku'],
                'sale_price' => $value['sale_price'],
                'date_on_sale_from' => convertShamsiToGregorianDate($value['date_on_sale_from']),
                'date_on_sale_to' => convertShamsiToGregorianDate($value['date_on_sale_to']),
            ]);
        }
    }
    public function change($variations, $attributeId, $product)
    {
        ProductVariation::where('product_id' , $product->id)->delete();

        $counter = count($variations['value']);
        for ($i = 0; $i < $counter; $i++) {
            ProductVariation::create([
                'attribute_id' => $attributeId,
                'product_id' => $product->id,
                'value' => $variations['value'][$i],
                'price' => $variations['price'][$i],
                'quantity' => $variations['quantity'][$i],
                'sku' => $variations['sku'][$i]
            ]);
        }
    }
}
