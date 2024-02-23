<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use App\Models\ProductVariation;
use App\Models\Tag;
use App\Services\ImageUploaderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = Brand::all();
        $tags = Tag::all();
        $categories = Category::query()->where('parent_id', '!=', 0)->get();
        return view('admin.products.create', compact('brands', 'tags', 'categories'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request, ImageUploaderService $uploader)
    {
        if ($request->hasFile('primary_image') && $request->hasFile('images')) {
            $imagesDirectories = $uploader->upload($request->file('primary_image'), $request->file('images'), env('product_images_uploader_path'));
        } else {
            echo 'Your request does not have the required file(s)';
        }

        try {
            DB::beginTransaction();
            $product = Product::query()->create([
                'name' => $request->name,
                'brand_id' => $request->brand_id,
                'category_id' => $request->category_id,
                'slug' => $request->slug,
                'primary_image' => $imagesDirectories['primary_image'],
                'description' => $request->description,
                'is_active' => $request->is_active,
                'delivery_amount' => $request->delivery_amount,
                'delivery_amount_per_product' => $request->delivery_amount_per_product,
            ]);


            foreach ($imagesDirectories['images'] as $image) {
                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'image' => $image
                ]);
            }
            foreach ($request->attribute_ids as $key => $value) {
                ProductAttribute::query()->create([
                    'product_id' => $product->id,
                    'attribute_id' => $key,
                    'value' => $value
                ]);
            }

            $counter = count($request->variation_values['value']);
            $category = Category::find($request->category_id);
            for ($i = 0; $i < $counter; $i++) {
                ProductVariation::query()->create([
                    'attribute_id' => $category->attributes()->wherePivot('is_variation', 1)->first()->id,
                    'product_id' => $product->id,
                    'value' => $request->variation_values['value'][$i],
                    'price' => $request->variation_values['price'][$i],
                    'quantity' => $request->variation_values['quantity'][$i],
                    'sku' => $request->variation_values['sku'][$i],
                ]);
            }
            $product->tags()->attach($request->tag_ids);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            alert()->error('مشکل در ایجاد', $ex->getMessage());
            return redirect()->route('admin.products.index');
        }
        alert()->success('با تشکر', 'محصول مورد نظر ایجاد  شد.');
        return redirect()->route('admin.products.index');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

}
