<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;

class ProductImageService
{
    public function store(array $imagesDirectories,Product $product)
    {
        foreach ($imagesDirectories as $image) {
            ProductImage::query()->create([
                'product_id' => $product->id,
                'image' => $image
            ]);
        }
    }
}
