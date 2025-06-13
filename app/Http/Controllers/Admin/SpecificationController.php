<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Added for unique rule

class SpecificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $specifications = Specification::orderBy('name')->orderBy('value')->paginate(10);
        return view('backend.specifications.index', compact('specifications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.specifications.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'value' => [
                'required',
                'string',
                'max:255',
                Rule::unique('specifications')->where(function ($query) use ($request) {
                    return $query->where('name', $request->name)
                                 ->where('value', $request->value);
                }),
            ],
        ]);

        Specification::create($request->all());

        return redirect()->route('specifications.index')->with('success', 'Specification created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Specification $specification)
    {
        return redirect()->route('specifications.edit', $specification);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Specification $specification)
    {
        return view('backend.specifications.edit', compact('specification'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Specification $specification)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'value' => [
                'required',
                'string',
                'max:255',
                Rule::unique('specifications')->where(function ($query) use ($request) {
                    return $query->where('name', $request->name)
                                 ->where('value', $request->value);
                })->ignore($specification->id),
            ],
        ]);

        $specification->update($request->all());

        return redirect()->route('specifications.index')->with('success', 'Specification updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specification $specification)
    {
        // Future consideration: Check if specification is in use by ProductVariants
        // if ($specification->productVariants()->count() > 0 || $specification->products()->count() > 0) {
        //    return redirect()->route('specifications.index')->with('error', 'Specification cannot be deleted as it is associated with products or variants.');
        // }
        $specification->delete();

        return redirect()->route('specifications.index')->with('success', 'Specification deleted successfully.');
    }
}
