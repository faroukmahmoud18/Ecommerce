<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductVariant;
use App\Models\Color;
use App\Models\Size;
use App\Models\Specification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Added for potential transaction usage

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products=Product::getAllProduct();
        return view('backend.product.index')->with('products',$products);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brands = Brand::get();
        $categories = Category::where('is_parent', 1)->get();
        $colors = Color::orderBy('name')->get();
        $sizes = Size::orderBy('name')->get();
        $specifications = Specification::orderBy('name')->orderBy('value')->get();

        return view('backend.product.create')
            ->with('categories', $categories)
            ->with('brands', $brands)
            ->with('colors', $colors)
            ->with('sizes', $sizes)
            ->with('specifications', $specifications);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'=>'string|required|max:255',
            'summary'=>'string|required',
            'description'=>'string|nullable',
            'photo'=>'string|required', // Assuming this is the main product photo URL
            // 'size'=>'nullable', // Old field, replaced by variants
            'stock'=>"nullable|numeric", // Main product stock, might be derived or deprecated
            'cat_id'=>'required|exists:categories,id',
            'brand_id'=>'nullable|exists:brands,id',
            'child_cat_id'=>'nullable|exists:categories,id',
            'is_featured'=>'sometimes|in:1',
            'status'=>'required|in:active,inactive',
            'condition'=>'required|in:default,new,hot',
            'price'=>'required|numeric', // Main product price, could be a base price
            'discount'=>'nullable|numeric',
            'variants.*.color_id' => 'nullable|exists:colors,id',
            'variants.*.size_id' => 'nullable|exists:sizes,id',
            'variants.*.specification_id' => 'nullable|exists:specifications,id',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.sku' => 'nullable|string|unique:product_variants,sku',
        ]);

        $data=$request->except('variants'); // Exclude variants from main product data

        $slug=Str::slug($request->title);
        $count=Product::where('slug',$slug)->count();
        if($count>0){
            $slug=$slug.'-'.date('ymdis').'-'.rand(0,999);
        }
        $data['slug']=$slug;
        $data['is_featured']=$request->input('is_featured',0);

        // Remove old size handling if it was only for variants
        // unset($data['size']);

        DB::beginTransaction();
        try {
            $product = Product::create($data);

            if ($product && $request->has('variants')) {
                $variantColorIds = [];
                $variantSizeIds = [];
                $variantSpecIds = [];

                foreach ($request->variants as $variantData) {
                    if (isset($variantData['price']) && isset($variantData['stock'])) {
                        $variantData['product_id'] = $product->id;
                        ProductVariant::create($variantData);

                        if(isset($variantData['color_id']) && $variantData['color_id']) $variantColorIds[] = $variantData['color_id'];
                        if(isset($variantData['size_id']) && $variantData['size_id']) $variantSizeIds[] = $variantData['size_id'];
                        if(isset($variantData['specification_id']) && $variantData['specification_id']) $variantSpecIds[] = $variantData['specification_id'];
                    }
                }
                // Sync product attributes (colors, sizes, specifications) based on variants
                if(count($variantColorIds) > 0) $product->colors()->syncWithoutDetaching(array_unique($variantColorIds));
                if(count($variantSizeIds) > 0) $product->sizes()->syncWithoutDetaching(array_unique($variantSizeIds));
                if(count($variantSpecIds) > 0) $product->specifications()->syncWithoutDetaching(array_unique($variantSpecIds));
            }
            DB::commit();
            request()->session()->flash('success', __('flash.product_created_success')); // Ensure this key exists
        } catch (\Exception $e) {
            DB::rollBack();
            request()->session()->flash('error', __('flash_messages.error_please_try_again') . ': ' . $e->getMessage());
        }

        return redirect()->route('product.index');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Not typically used for admin CRUD, redirect to edit or index.
        return redirect()->route('product.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::with(['variants', 'variants.color', 'variants.size', 'variants.specification'])->findOrFail($id);
        $brands = Brand::get();
        $categories = Category::where('is_parent', 1)->get();
        $colors = Color::orderBy('name')->get();
        $sizes = Size::orderBy('name')->get();
        $specifications = Specification::orderBy('name')->orderBy('value')->get();

        return view('backend.product.edit')
            ->with('product', $product)
            ->with('brands', $brands)
            ->with('categories', $categories)
            ->with('colors', $colors)
            ->with('sizes', $sizes)
            ->with('specifications', $specifications);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product=Product::findOrFail($id);
        $request->validate([
            'title'=>'string|required|max:255',
            'summary'=>'string|required',
            'description'=>'string|nullable',
            'photo'=>'string|required',
            // 'size'=>'nullable', // Old field
            'stock'=>"nullable|numeric",
            'cat_id'=>'required|exists:categories,id',
            'child_cat_id'=>'nullable|exists:categories,id',
            'is_featured'=>'sometimes|in:1',
            'brand_id'=>'nullable|exists:brands,id',
            'status'=>'required|in:active,inactive',
            'condition'=>'required|in:default,new,hot',
            'price'=>'required|numeric',
            'discount'=>'nullable|numeric',
            'variants.*.color_id' => 'nullable|exists:colors,id',
            'variants.*.size_id' => 'nullable|exists:sizes,id',
            'variants.*.specification_id' => 'nullable|exists:specifications,id',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.sku' => 'nullable|string|unique:product_variants,sku,' . $request->input('variants.*.id'), // Ignore self on update
        ]);

        $data=$request->except('variants');
        $data['is_featured']=$request->input('is_featured',0);
        // unset($data['size']); // Remove old size handling

        DB::beginTransaction();
        try {
            $product->fill($data)->save();

            if ($request->has('variants')) {
                $incomingVariantIds = [];
                foreach ($request->variants as $variantData) {
                    if (isset($variantData['price']) && isset($variantData['stock'])) {
                        $variantData['product_id'] = $product->id;
                        if (isset($variantData['id']) && !empty($variantData['id'])) {
                            $variant = ProductVariant::find($variantData['id']);
                            if ($variant && $variant->product_id == $product->id) { // Ensure variant belongs to product
                                $variant->update($variantData);
                                $incomingVariantIds[] = $variant->id;
                            }
                        } else {
                            $newVariant = ProductVariant::create($variantData);
                            $incomingVariantIds[] = $newVariant->id;
                        }
                    }
                }
                // Delete variants that were removed from the form
                $product->variants()->whereNotIn('id', $incomingVariantIds)->delete();

                // Re-sync all product attributes based on the current set of variants
                $currentVariants = $product->variants()->get();

                $currentColorIds = $currentVariants->pluck('color_id')->filter()->unique()->toArray();
                $product->colors()->sync($currentColorIds);

                $currentSizeIds = $currentVariants->pluck('size_id')->filter()->unique()->toArray();
                $product->sizes()->sync($currentSizeIds);

                $currentSpecIds = $currentVariants->pluck('specification_id')->filter()->unique()->toArray();
                $product->specifications()->sync($currentSpecIds);

            } else { // No variants submitted, remove all existing variants and attribute links
                $product->variants()->delete();
                $product->colors()->detach();
                $product->sizes()->detach();
                $product->specifications()->detach();
            }
            DB::commit();
            request()->session()->flash('success', __('flash.product_updated_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            request()->session()->flash('error', __('flash_messages.error_please_try_again') . ': ' . $e->getMessage());
        }

        return redirect()->route('product.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product=Product::findOrFail($id);
        // Note: Consider related product_variants and pivot table entries.
        // If foreign keys have cascade delete, they will be handled.
        // Otherwise, manual deletion or soft-delete strategy might be needed.
        $status=$product->delete();
        
        if($status){
            request()->session()->flash('success',__('flash_messages.product_deleted_success'));
        }
        else{
            request()->session()->flash('error',__('flash_messages.product_deleted_error'));
        }
        return redirect()->route('product.index');
    }
}
