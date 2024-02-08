<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attrribute;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attributes = Attrribute::latest()->paginate(20);
        return view('admin.attributes.index',compact('attributes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.attributes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=> 'required'
        ]);
        Attrribute::create([
            'name'=> $request->name,
        ]);
        alert()->success('با تشکر','ویژگی مورد نظر ایجاد شد.');
        return redirect()->route('admin.attributes.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Attrribute $attrribute)
    {
        return view('admin.attributes.show', compact('attrribute'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attrribute $attrribute)
    {
        return view('admin.attributes.edit',compact('attrribute'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attrribute $attrribute)
    {
        $request->validate([
            'name'=> 'required'
        ]);
        $attrribute->update([
            'name'=> $request->name,
        ]);
        alert()->success('با تشکر','ویژگی مورد نظر بروزرسانی شد.');
        return redirect()->route('admin.attributes.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
