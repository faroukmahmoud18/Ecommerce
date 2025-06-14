<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // For DB transaction in delete

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $colors = Color::orderBy('id', 'DESC')->paginate(10);
        return view('backend.color.index')->with('colors', $colors);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.color.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'string|required|unique:colors,name',
            'hex_code' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/i'], // Added 'i' for case-insensitive hex
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();
        $status = Color::create($data);

        if ($status) {
            request()->session()->flash('success', __('flash.color_created_success'));
        } else {
            request()->session()->flash('error', __('flash_messages.error_please_try_again'));
        }
        return redirect()->route('color.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Color $color)
    {
        // Not typically used for this kind of resource, redirect to edit
        return redirect()->route('color.edit', $color->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Color $color)
    {
        return view('backend.color.edit')->with('color', $color);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Color $color)
    {
        $request->validate([
            'name' => 'string|required|unique:colors,name,' . $color->id,
            'hex_code' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/i'], // Added 'i' for case-insensitive hex
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();
        $status = $color->fill($data)->save();

        if ($status) {
            request()->session()->flash('success', __('flash.color_updated_success'));
        } else {
            request()->session()->flash('error', __('flash_messages.error_please_try_again'));
        }
        return redirect()->route('color.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Color $color)
    {
        DB::beginTransaction();
        try {
            // A more robust check would be to see if this color is linked in product_variants
            // This depends on the exact relationship and whether cascading deletes are set up at DB level
            // For now, we rely on the try-catch for a generic foreign key constraint violation

            // Example check (if product_variants table exists and has color_id):
            // if (DB::table('product_variants')->where('color_id', $color->id)->exists()) {
            //     request()->session()->flash('error', __('flash.color_delete_error_in_use'));
            //     return redirect()->route('color.index');
            // }

            $status = $color->delete();
            DB::commit();

            if ($status) {
                request()->session()->flash('success', __('flash.color_deleted_success'));
            } else {
                // This case might not be reached if delete() throws an exception on failure
                request()->session()->flash('error', __('flash_messages.error_please_try_again'));
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            // Check for common foreign key violation error codes
            // SQLSTATE[23000]: Integrity constraint violation
            if ($e->getCode() == '23000' || (isset($e->errorInfo[1]) && ($e->errorInfo[1] == 1451 || $e->errorInfo[1] == 547))) {
                request()->session()->flash('error', __('flash.color_delete_error_in_use'));
            } else {
                request()->session()->flash('error', __('flash_messages.error_please_try_again') . ': ' . $e->getMessage());
            }
        }  catch (\Exception $e) {
            DB::rollBack();
            request()->session()->flash('error', __('flash_messages.error_please_try_again') . ': ' . $e->getMessage());
        }
        return redirect()->route('color.index');
    }
}
