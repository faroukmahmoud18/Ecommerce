<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $colors = Color::orderBy('name')->paginate(10); // Paginate for better display
        return view('backend.colors.index', compact('colors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.colors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:colors,name',
            'hex_code' => ['nullable', 'string', 'max:7', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'], // Basic hex validation
        ]);

        Color::create($request->all());

        return redirect()->route('colors.index')->with('success', 'Color created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Color  $color
     * @return \Illuminate\Http\Response
     */
    public function show(Color $color)
    {
        // Typically not used for simple CRUD like this, redirect to edit or index.
        return redirect()->route('colors.edit', $color);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Color $color)
    {
        return view('backend.colors.edit', compact('color'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Color $color)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:colors,name,' . $color->id,
            'hex_code' => ['nullable', 'string', 'max:7', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
        ]);

        $color->update($request->all());

        return redirect()->route('colors.index')->with('success', 'Color updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Color $color)
    {
        // Future consideration: Check if color is in use by ProductVariants
        // For now, direct delete.
        // if ($color->productVariants()->count() > 0 || $color->products()->count() > 0) {
        //    return redirect()->route('colors.index')->with('error', 'Color cannot be deleted as it is associated with products or variants.');
        // }

        $color->delete();

        return redirect()->route('colors.index')->with('success', 'Color deleted successfully.');
    }
}
