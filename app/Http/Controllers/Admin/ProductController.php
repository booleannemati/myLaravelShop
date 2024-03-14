<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\product\ProductRequestStore;
use App\Http\Requests\product\ProductRequestUpdate;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tag;
use App\Services\ImageUploaderService;
use App\Services\ProductAttributeService;
use App\Services\ProductImageService;
use App\Services\ProductVariationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::query()->latest()->paginate(20);
        return view('admin.products.index',compact('products'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\View\View
    {
        $brands = Brand::all();
        $tags = Tag::all();
        $categories = Category::query()->where('parent_id', '!=', 0)->get();
        return view('admin.products.create', compact('brands', 'tags', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequestStore $request, ImageUploaderService $uploader): \Illuminate\Http\RedirectResponse
    {
        // Check if both primary image and additional images are present in the request
        if ($request->hasFile('primary_image') && $request->hasFile('images')) {
            // Upload images and get directories
            $imagesDirectories = $uploader->upload($request->file('primary_image'), $request->file('images'));
        } else {
            // If required files are missing, notify the user
            echo 'Your request does not have the required file(s)';
        }
        try {
            // Begin a database transaction
            DB::beginTransaction();

            // Create a new product
            $product = Product::query()->create([
                'name' => $request->name,
                'brand_id' => $request->brand_id,
                'category_id' => $request->category_id,
                'slug' => $request->slug,
                'primary_image' => $imagesDirectories['fileNamePrimaryImage'],
                'description' => $request->description,
                'is_active' => $request->is_active,
                'delivery_amount' => $request->delivery_amount,
                'delivery_amount_per_product' => $request->delivery_amount_per_product,
            ]);

            // Store related images for the product
            $productImageService = new ProductImageService();
            $productImageService->store($imagesDirectories['fileNameImages'], $product);

            // Store related attributes for the product
            $productAttributeService = new ProductAttributeService();
            $productAttributeService->store($request->attribute_ids, $product);

            // Store related variations for the product
            $category = Category::query()->find($request->category_id);
            $productVariationService = new ProductVariationService();
            $productVariationService->store($request->variation_values,$category,$product);

            // Attach tags to the product
            $product->tags()->attach($request->tag_ids);

            // Commit the transaction
            DB::commit();
        } catch (\Exception $ex) {
            // Rollback the transaction and handle the exception
            DB::rollBack();
            alert()->error('Issue in creation', $ex->getMessage());
            return redirect()->route('admin.products.index');
        }

        // If everything is successful, show success message and redirect
        alert()->success('Thank you', 'The desired product has been created.');
        return redirect()->route('admin.products.index');
    }
    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $productAttributes = $product->attributes()->with('attribute')->get();
        $productVariations = $product->variations;
        $images = $product->images;
        return view('admin.products.show' , compact('product','productAttributes','productVariations','images'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $brands = Brand::all();
        $tags = Tag::all();
        $productAttributes = $product->attributes()->with('attribute')->get();
        $productVariations = $product->variations;

        return view('admin.products.edit', compact('product', 'brands', 'tags','productAttributes','productVariations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequestUpdate $request, Product $product)
    {
        try {
            // Begin a database transaction
            DB::beginTransaction();

            // Create a new product
            $product->update([
                'name' => $request->name,
                'brand_id' => $request->brand_id,
                'description' => $request->description,
                'is_active' => $request->is_active,
                'delivery_amount' => $request->delivery_amount,
                'delivery_amount_per_product' => $request->delivery_amount_per_product,
            ]);

            // Store related attributes for the product
            $productAttributeService = new ProductAttributeService();
            $productAttributeService->update($request->attribute_values);

            // Store related variations for the product
            $category = Category::query()->find($request->category_id);
            $productVariationService = new ProductVariationService();
            $productVariationService->update($request->variation_values);

            // Attach tags to the product
            $product->tags()->sync($request->tag_ids);

            // Commit the transaction
            DB::commit();
        } catch (\Exception $ex) {
            // Rollback the transaction and handle the exception
            DB::rollBack();
            dd($ex->getMessage());
            return redirect()->route('admin.products.index');
        }

        // If everything is successful, show success message and redirect
        alert()->success('Thank you', 'The desired product has been edited.');
        return redirect()->route('admin.products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function EditCategory(Request $request, Product $product)
    {
        $categories = Category::where('parent_id', '!=', 0)->get();
        return view('admin.products.edit_category', compact('product' , 'categories'));
    }

    public function updateCategory(Request $request, Product $product)
    {
        // dd($request->all());
        $request->validate([
            'category_id' => 'required',
            'attribute_ids' => 'required',
            'attribute_ids.*' => 'required',
            'variation_values' => 'required',
            'variation_values.*.*' => 'required',
            'variation_values.price.*' => 'integer',
            'variation_values.quantity.*' => 'integer'
        ]);
        try {
            DB::beginTransaction();

            $product->update([
                'category_id' => $request->category_id
            ]);

            $productAttributeController = new ProductAttributeService();
            $productAttributeController->change($request->attribute_ids, $product);

            $category = Category::query()->find($request->category_id);
            $productVariationController = new ProductVariationService();
            $productVariationController->change($request->variation_values, $category->attributes()->wherePivot('is_variation', 1)->first()->id, $product);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            alert()->error('مشکل در ایجاد محصول', $ex->getMessage())->persistent('حله');
            return redirect()->back();
        }

        alert()->success('محصول مورد نظر ایجاد شد', 'باتشکر');
        return redirect()->route('admin.products.index');
    }
}
