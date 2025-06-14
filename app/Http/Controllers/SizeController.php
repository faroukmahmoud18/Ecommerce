<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sizes = Size::orderBy('id', 'DESC')->paginate(10);
        return view('backend.size.index')->with('sizes', $sizes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.size.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'string|required|unique:sizes,name',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();
        $status = Size::create($data);

        if ($status) {
            request()->session()->flash('success', __('flash.size_created_success'));
        } else {
            request()->session()->flash('error', __('flash_messages.error_please_try_again'));
        }
        return redirect()->route('size.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Size $size)
    {
        return redirect()->route('size.edit', $size->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Size $size)
    {
        return view('backend.size.edit')->with('size', $size);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Size $size)
    {
        $request->validate([
            'name' => 'string|required|unique:sizes,name,' . $size->id,
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();
        $status = $size->fill($data)->save();

        if ($status) {
            request()->session()->flash('success', __('flash.size_updated_success'));
        } else {
            request()->session()->flash('error', __('flash_messages.error_please_try_again'));
        }
        return redirect()->route('size.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Size $size)
    {
        DB::beginTransaction();
        try {
            // Example check (if product_variants table exists and has size_id):
            // if (DB::table('product_variants')->where('size_id', $size->id)->exists()) {
            //     request()->session()->flash('error', __('flash.size_delete_error_in_use'));
            //     return redirect()->route('size.index');
            // }

            $status = $size->delete();
            DB::commit();

            if ($status) {
                request()->session()->flash('success', __('flash.size_deleted_success'));
            } else {
                request()->session()->flash('error', __('flash_messages.error_please_try_again'));
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            if ($e->getCode() == '23000' || (isset($e->errorInfo[1]) && ($e->errorInfo[1] == 1451 || $e->errorInfo[1] == 547))) {
                request()->session()->flash('error', __('flash.size_delete_error_in_use'));
            } else {
                request()->session()->flash('error', __('flash_messages.error_please_try_again') . ': ' . $e->getMessage());
            }
        }  catch (\Exception $e) {
            DB::rollBack();
            request()->session()->flash('error', __('flash_messages.error_please_try_again') . ': ' . $e->getMessage());
        }
        return redirect()->route('size.index');
    }
}
