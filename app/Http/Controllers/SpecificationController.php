<?php

namespace App\Http\Controllers;

use App\Models\Specification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class SpecificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $specifications = Specification::orderBy('name')->orderBy('value')->paginate(10);
        return view('backend.specification.index')->with('specifications', $specifications);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.specification.create');
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
                    return $query->where('name', $request->name);
                }),
            ],
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->validated(); // Use validated data
        $status = Specification::create($data);

        if ($status) {
            request()->session()->flash('success', __('flash.specification_created_success'));
        } else {
            request()->session()->flash('error', __('flash_messages.error_please_try_again'));
        }
        return redirect()->route('specification.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Specification $specification)
    {
        return redirect()->route('specification.edit', $specification->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Specification $specification)
    {
        return view('backend.specification.edit')->with('specification', $specification);
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
                    return $query->where('name', $request->name);
                })->ignore($specification->id),
            ],
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->validated(); // Use validated data
        $status = $specification->fill($data)->save();

        if ($status) {
            request()->session()->flash('success', __('flash.specification_updated_success'));
        } else {
            request()->session()->flash('error', __('flash_messages.error_please_try_again'));
        }
        return redirect()->route('specification.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specification $specification)
    {
        DB::beginTransaction();
        try {
            // Example check (if product_specification_pivot or product_variants table uses specification_id):
            // if (DB::table('product_specification')->where('specification_id', $specification->id)->exists() ||
            //     DB::table('product_variants')->where('specification_id', $specification->id)->exists()) {
            //     request()->session()->flash('error', __('flash.specification_delete_error_in_use'));
            //     return redirect()->route('specification.index');
            // }

            $status = $specification->delete();
            DB::commit();

            if ($status) {
                request()->session()->flash('success', __('flash.specification_deleted_success'));
            } else {
                request()->session()->flash('error', __('flash_messages.error_please_try_again'));
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            if ($e->getCode() == '23000' || (isset($e->errorInfo[1]) && ($e->errorInfo[1] == 1451 || $e->errorInfo[1] == 547))) {
                request()->session()->flash('error', __('flash.specification_delete_error_in_use'));
            } else {
                request()->session()->flash('error', __('flash_messages.error_please_try_again') . ': ' . $e->getMessage());
            }
        }  catch (\Exception $e) {
            DB::rollBack();
            request()->session()->flash('error', __('flash_messages.error_please_try_again') . ': ' . $e->getMessage());
        }
        return redirect()->route('specification.index');
    }
}
