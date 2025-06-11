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
        // return $products;
        return view('backend.product.index')->with('products',$products);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brand=Brand::get();
        $category=Category::where('is_parent',1)->get();
        // return $category;
        return view('backend.product.create')->with('categories',$category)->with('brands',$brand);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        // return $request->all();
        $this->validate($request,[
            'title'=>'string|required',
            'summary'=>'string|required',
            'description'=>'string|nullable',
            'photo'=>'string|required',
            // 'size'=>'nullable', // Size validation removed, handled by variants
            'stock'=>"required|numeric", // Stock for main product (if applicable, or could be derived from variants)
            'cat_id'=>'required|exists:categories,id',
            'brand_id'=>'nullable|exists:brands,id',
            'child_cat_id'=>'nullable|exists:categories,id',
            'is_featured'=>'sometimes|in:1',
            'status'=>'required|in:active,inactive',
            'condition'=>'required|in:default,new,hot',
            'price'=>'required|numeric', // Base price for main product
            'discount'=>'nullable|numeric',
            'variants.*.color_id' => 'nullable|exists:colors,id',
            'variants.*.size_id' => 'nullable|exists:sizes,id',
            'variants.*.specification_id' => 'nullable|exists:specifications,id',
            'variants.*.price' => 'required|numeric',
            'variants.*.stock' => 'required|integer',
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
        // $data['size']=''; // Deprecate direct size column for variants

        $product=Product::create($data);

        if($product){
            if ($request->has('variants')) {
                $allColorIds = [];
                $allSizeIds = [];
                $allSpecificationIds = [];

                foreach ($request->variants as $variantData) {
                    $variantData['product_id'] = $product->id;
                    ProductVariant::create($variantData);

                    if (!empty($variantData['color_id'])) $allColorIds[] = $variantData['color_id'];
                    if (!empty($variantData['size_id'])) $allSizeIds[] = $variantData['size_id'];
                    if (!empty($variantData['specification_id'])) $allSpecificationIds[] = $variantData['specification_id'];
                }

                // Sync product attributes to pivot tables
                if (!empty($allColorIds)) $product->colors()->syncWithoutDetaching(array_unique($allColorIds));
                if (!empty($allSizeIds)) $product->sizes()->syncWithoutDetaching(array_unique($allSizeIds));
                if (!empty($allSpecificationIds)) $product->specifications()->syncWithoutDetaching(array_unique($allSpecificationIds));
            }
            request()->session()->flash('success','Product Successfully added');
        }
        else{
            request()->session()->flash('error','Please try again!!');
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $brand=Brand::get();
        $product=Product::with('variants')->findOrFail($id); // Eager load variants
        $category=Category::where('is_parent',1)->get();
        // $items=Product::where('id',$id)->get(); // This seems redundant, $product is the item

        $colors = Color::all();
        $sizes = Size::all();
        $specifications = Specification::all();

        return view('backend.product.edit')->with('product',$product)
                    ->with('brands',$brand)
                    ->with('categories',$category)
                    // ->with('items',$items) // Redundant
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
        $this->validate($request,[
            'title'=>'string|required',
            'summary'=>'string|required',
            'description'=>'string|nullable',
            'photo'=>'string|required',
            // 'size'=>'nullable', // Size validation removed
            'stock'=>"required|numeric",
            'cat_id'=>'required|exists:categories,id',
            'child_cat_id'=>'nullable|exists:categories,id',
            'is_featured'=>'sometimes|in:1',
            'brand_id'=>'nullable|exists:brands,id',
            'status'=>'required|in:active,inactive',
            'condition'=>'required|in:default,new,hot',
            'price'=>'required|numeric',
            'discount'=>'nullable|numeric',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.color_id' => 'nullable|exists:colors,id',
            'variants.*.size_id' => 'nullable|exists:sizes,id',
            'variants.*.specification_id' => 'nullable|exists:specifications,id',
            'variants.*.price' => 'required|numeric',
            'variants.*.stock' => 'required|integer',
            'variants.*.sku' => 'nullable|string|unique:product_variants,sku,' . $request->input('variants.*.id'), // Ensure SKU is unique, ignoring current variant
        ]);

        $data=$request->except('variants');
        $data['is_featured']=$request->input('is_featured',0);
        // $data['size']=''; // Deprecate direct size column for variants

        $status=$product->fill($data)->save();

        if($status){
            if ($request->has('variants')) {
                $incomingVariantIds = [];
                $allColorIds = [];
                $allSizeIds = [];
                $allSpecificationIds = [];

                foreach ($request->variants as $variantData) {
                    $variantData['product_id'] = $product->id;
                    if (isset($variantData['id']) && !empty($variantData['id'])) {
                        // Update existing variant
                        $variant = ProductVariant::find($variantData['id']);
                        if ($variant) {
                            $variant->update($variantData);
                        }
                        $incomingVariantIds[] = $variantData['id'];
                    } else {
                        // Create new variant
                        $newVariant = ProductVariant::create($variantData);
                        $incomingVariantIds[] = $newVariant->id;
                    }

                    if (!empty($variantData['color_id'])) $allColorIds[] = $variantData['color_id'];
                    if (!empty($variantData['size_id'])) $allSizeIds[] = $variantData['size_id'];
                    if (!empty($variantData['specification_id'])) $allSpecificationIds[] = $variantData['specification_id'];
                }

                // Delete variants not present in the incoming request
                $product->variants()->whereNotIn('id', $incomingVariantIds)->delete();

                // Sync product attributes to pivot tables
                $product->colors()->sync(array_unique($allColorIds));
                $product->sizes()->sync(array_unique($allSizeIds));
                $product->specifications()->sync(array_unique($allSpecificationIds));

            } else {
                // No variants submitted, remove all existing variants and pivot entries
                $product->variants()->delete();
                $product->colors()->detach();
                $product->sizes()->detach();
                $product->specifications()->detach();
            }
            request()->session()->flash('success','Product Successfully updated');
        }
        else{
            request()->session()->flash('error','Please try again!!');
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
        // Cascading deletes for variants and pivot table entries are handled by the database (checked migrations)
        $status=$product->delete();
        
        if($status){
            request()->session()->flash('success','Product successfully deleted');
        }
        else{
            request()->session()->flash('error','Error while deleting product');
        }
        return redirect()->route('product.index');
    }
}
