<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::query()->latest()->paginate(20);
        return view('admin.categories.index',compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        $parentCategories = Category::query()->where('parent_id' , 0)->get();
        $attributes = Attribute::all();

        return view('admin.categories.create' , compact('parentCategories', 'attributes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'parent_id' => 'required',
            'attribute_ids' => 'required',
            'attribute_is_filter_ids' => 'required',
            'variation_id' => 'required'
        ]);
        try {
            DB::beginTransaction();

            $category = Category::query()->create([
                'name' =>  $fields['name'],
                'parent_id' =>  $fields['parent_id'],
                'slug' =>  $fields['slug'],
                'description' =>  $request->description,
                'is_active' =>  $request->is_active,
                'icon' =>  $request->icon,
            ]);
            foreach ($fields['attribute_ids'] as $attribute_id) {
                $attribute = Attribute::query()->findOrFail($attribute_id);
                $category->Attributes()->save($attribute,[
                    'is_filter' => in_array($attribute_id, $fields['attribute_is_filter_ids'], true)? 1 :0,
                    'is_variation' => $attribute_id === $fields['variation_id'] ? 1 : 0
                ]);
            }

            DB::commit();
        }catch (\Exception $ex) {
            DB::rollBack();
            alert()->error('مشکل ایجاد',$ex->getMessage());
            return redirect()->route('admin.categories.index');
        }
        alert()->success('با تشکر','برند مورد نظر ایجاد شد.');
        return redirect()->route('admin.categories.index');
    }


    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return view('admin.categories.show',compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $attributes = Attribute::all();

        $parentCategories = Category::query()->where('parent_id' , 0)->get();
        return view('admin.categories.edit',compact('category','parentCategories','attributes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $fields = $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$category->id,
            'parent_id' => 'required',
            'attribute_ids' => 'required',
            'attribute_is_filter_ids' => 'required',
            'variation_id' => 'required'
        ]);
        try {
            DB::beginTransaction();

            $category->update([
                'name' =>  $fields['name'],
                'parent_id' =>  $fields['parent_id'],
                'slug' =>  $fields['slug'],
                'description' =>  $request->description,
                'is_active' =>  $request->is_active,
                'icon' =>  $request->icon,
            ]);

            $category->attributes()->detach();
            foreach ($fields['attribute_ids'] as $attribute_id) {
                $attribute = Attribute::query()->findOrFail($attribute_id);
                $category->attributes()->attach($attribute,[
                    'is_filter' => in_array($attribute_id, $fields['attribute_is_filter_ids'], true)? 1 :0,
                    'is_variation' => $attribute_id === $fields['variation_id'] ? 1 : 0
                ]);
            }

            DB::commit();
        }catch (\Exception $ex) {
            DB::rollBack();
            alert()->error('مشکل در ویرایش',$ex->getMessage());
            return redirect()->route('admin.categories.index');
        }
        alert()->success('با تشکر','برند مورد نظر ویرایش  شد.');
        return redirect()->route('admin.categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
