<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\Cart;
use App\Models\ProductVariant; // Added
use Illuminate\Support\Str;
use Helper;
class CartController extends Controller
{
    protected $product=null;
    public function __construct(Product $product){
        $this->product=$product;
    }

    public function addToCart(Request $request){
        // dd($request->all());
        if (empty($request->slug)) {
            request()->session()->flash('error','Invalid Products');
            return back();
        }        
        $product = Product::where('slug', $request->slug)->first();
        // return $product;
        if (empty($product)) {
            request()->session()->flash('error','Invalid Products');
            return back();
        }

        $already_cart = Cart::where('user_id', auth()->user()->id)->where('order_id',null)->where('product_id', $product->id)->first();
        // return $already_cart;
        if($already_cart) {
            // dd($already_cart);
            $already_cart->quantity = $already_cart->quantity + 1;
            $already_cart->amount = $product->price+ $already_cart->amount;
            // return $already_cart->quantity;
            if ($already_cart->product->stock < $already_cart->quantity || $already_cart->product->stock <= 0) return back()->with('error','Stock not sufficient!.');
            $already_cart->save();
            
        }else{
            
            $cart = new Cart;
            $cart->user_id = auth()->user()->id;
            $cart->product_id = $product->id;
            $cart->price = ($product->price-($product->price*$product->discount)/100);
            $cart->quantity = 1;
            $cart->amount=$cart->price*$cart->quantity;
            if ($cart->product->stock < $cart->quantity || $cart->product->stock <= 0) return back()->with('error','Stock not sufficient!.');
            $cart->save();
            $wishlist=Wishlist::where('user_id',auth()->user()->id)->where('cart_id',null)->update(['cart_id'=>$cart->id]);
        }
        request()->session()->flash('success','Product successfully added to cart');
        return back();       
    }  

    public function singleAddToCart(Request $request){
        $request->validate([
            'variant_id' => 'nullable|exists:product_variants,id',
            'slug' => 'required_without:variant_id|exists:products,slug', // slug is required if no variant_id
            'quant'      =>  'required|array',
            'quant.*'    =>  'numeric|min:1'
        ]);

        $quantity = $request->quant[1]; // Assuming quant[1] is the structure for quantity
        $user_id = auth()->user()->id;

        $item_product_id = null;
        $item_variant_id = null;
        $item_price = 0;
        $item_stock = 0;
        $item_name_for_flash = ''; // For flash messages

        if ($request->filled('variant_id')) {
            $variant = ProductVariant::with('product')->findOrFail($request->input('variant_id'));
            $product = $variant->product;

            if($variant->stock < $quantity){
                return back()->with('error',"Out of stock for the selected variant ({$product->title} - Variant Details). Requested: {$quantity}, Available: {$variant->stock}");
            }
            $item_product_id = $product->id;
            $item_variant_id = $variant->id;
            $item_price = $variant->price;
            $item_stock = $variant->stock;
            $item_name_for_flash = $product->title . " (Variant)"; // Adjust as needed for variant name

        } else {
            $product = Product::where('slug', $request->input('slug'))->firstOrFail();
            if ($product->stock < $quantity) {
                return back()->with('error', "Out of stock for product: {$product->title}. Requested: {$quantity}, Available: {$product->stock}");
            }
            $item_product_id = $product->id;
            $item_variant_id = null;
            // Calculate price after discount for simple product
            $item_price = ($product->price - ($product->price * $product->discount) / 100);
            $item_stock = $product->stock;
            $item_name_for_flash = $product->title;
        }

        $cart_query = Cart::where('user_id', $user_id)
                            ->where('order_id', null)
                            ->where('product_id', $item_product_id);

        if ($item_variant_id) {
            $cart_query->where('variant_id', $item_variant_id);
        } else {
            $cart_query->whereNull('variant_id');
        }
        $already_cart = $cart_query->first();

        if($already_cart) {
            $new_quantity = $already_cart->quantity + $quantity;

            // Re-check stock for the item being updated
            $current_item_stock = $request->filled('variant_id') ?
                                  ProductVariant::find($already_cart->variant_id)->stock :
                                  Product::find($already_cart->product_id)->stock;

            if ($current_item_stock < $new_quantity) {
                return back()->with('error',"Stock not sufficient for {$item_name_for_flash}! Max available: {$current_item_stock}, Requested total: {$new_quantity}");
            }
            $already_cart->quantity = $new_quantity;
            $already_cart->price = $item_price; // Update price in case it changed (though for variants it should be fixed)
            $already_cart->amount = $item_price * $new_quantity;
            $already_cart->save();
        } else {
            // Stock check for new item was done at the beginning of variant/product specific block
            $cart = new Cart;
            $cart->user_id = $user_id;
            $cart->product_id = $item_product_id;
            $cart->variant_id = $item_variant_id;
            $cart->price = $item_price;
            $cart->quantity = $quantity;
            $cart->amount = $item_price * $quantity;
            $cart->save();
        }
        request()->session()->flash('success', $item_name_for_flash . ' successfully added to cart.');
        return back();       
    } 
    
    public function cartDelete(Request $request){
        $cart = Cart::find($request->id);
        if ($cart) {
            $cart->delete();
            request()->session()->flash('success','Cart successfully removed');
            return back();  
        }
        request()->session()->flash('error','Error please try again');
        return back();       
    }     

    public function cartUpdate(Request $request){
        // dd($request->all());
        if($request->quant){
            $error = array();
            $success = '';
            // return $request->quant;
            foreach ($request->quant as $k=>$quant) {
                // return $k;
                $id = $request->qty_id[$k];
                $cart = Cart::with('variant.product')->find($id); // Eager load variant and its product

                if($quant > 0 && $cart && $cart->variant) {
                    if($cart->variant->stock < $quant){
                        // Set quantity to max available stock if requested quantity is too high
                        $quant = $cart->variant->stock;
                        request()->session()->flash('error',"Quantity for '{$cart->variant->product->title}' (Variant: {$cart->variant->id}) adjusted to max available stock: {$quant}.");
                        // Do not immediately return; allow other items to be updated. Error will be flashed.
                    }
                    
                    if ($quant <= 0) { // If adjusted quant is 0 or less (e.g. stock was 0)
                        // Optionally delete the cart item if quantity becomes 0
                        // $cart->delete();
                        // request()->session()->flash('info', "Item '{$cart->variant->product->title}' removed as stock is 0 or requested quantity invalid.");
                        // continue;
                        // For now, let's just prevent negative/zero quantity update if not deleting
                        $error[] = "Invalid quantity for item '{$cart->variant->product->title}'.";
                        continue;
                    }

                    $cart->quantity = $quant;
                    $cart->amount = $cart->variant->price * $quant; // Use variant's price
                    $cart->save();
                    $success = 'Cart successfully updated!';
                } else if (!$cart->variant) {
                    $error[] = 'Cart item references an invalid product variant.';
                }
                else {
                    $error[] = 'Cart Invalid or item quantity is zero!';
                }
            }
            return back()->with($error)->with('success', $success);
        }else{
            return back()->with('Cart Invalid!');
        }    
    }

    // public function addToCart(Request $request){
    //     // return $request->all();
    //     if(Auth::check()){
    //         $qty=$request->quantity;
    //         $this->product=$this->product->find($request->pro_id);
    //         if($this->product->stock < $qty){
    //             return response(['status'=>false,'msg'=>'Out of stock','data'=>null]);
    //         }
    //         if(!$this->product){
    //             return response(['status'=>false,'msg'=>'Product not found','data'=>null]);
    //         }
    //         // $session_id=session('cart')['session_id'];
    //         // if(empty($session_id)){
    //         //     $session_id=Str::random(30);
    //         //     // dd($session_id);
    //         //     session()->put('session_id',$session_id);
    //         // }
    //         $current_item=array(
    //             'user_id'=>auth()->user()->id,
    //             'id'=>$this->product->id,
    //             // 'session_id'=>$session_id,
    //             'title'=>$this->product->title,
    //             'summary'=>$this->product->summary,
    //             'link'=>route('product-detail',$this->product->slug),
    //             'price'=>$this->product->price,
    //             'photo'=>$this->product->photo,
    //         );
            
    //         $price=$this->product->price;
    //         if($this->product->discount){
    //             $price=($price-($price*$this->product->discount)/100);
    //         }
    //         $current_item['price']=$price;

    //         $cart=session('cart') ? session('cart') : null;

    //         if($cart){
    //             // if anyone alreay order products
    //             $index=null;
    //             foreach($cart as $key=>$value){
    //                 if($value['id']==$this->product->id){
    //                     $index=$key;
    //                 break;
    //                 }
    //             }
    //             if($index!==null){
    //                 $cart[$index]['quantity']=$qty;
    //                 $cart[$index]['amount']=ceil($qty*$price);
    //                 if($cart[$index]['quantity']<=0){
    //                     unset($cart[$index]);
    //                 }
    //             }
    //             else{
    //                 $current_item['quantity']=$qty;
    //                 $current_item['amount']=ceil($qty*$price);
    //                 $cart[]=$current_item;
    //             }
    //         }
    //         else{
    //             $current_item['quantity']=$qty;
    //             $current_item['amount']=ceil($qty*$price);
    //             $cart[]=$current_item;
    //         }

    //         session()->put('cart',$cart);
    //         return response(['status'=>true,'msg'=>'Cart successfully updated','data'=>$cart]);
    //     }
    //     else{
    //         return response(['status'=>false,'msg'=>'You need to login first','data'=>null]);
    //     }
    // }

    // public function removeCart(Request $request){
    //     $index=$request->index;
    //     // return $index;
    //     $cart=session('cart');
    //     unset($cart[$index]);
    //     session()->put('cart',$cart);
    //     return redirect()->back()->with('success','Successfully remove item');
    // }

    public function checkout(Request $request){
        // $cart=session('cart');
        // $cart_index=\Str::random(10);
        // $sub_total=0;
        // foreach($cart as $cart_item){
        //     $sub_total+=$cart_item['amount'];
        //     $data=array(
        //         'cart_id'=>$cart_index,
        //         'user_id'=>$request->user()->id,
        //         'product_id'=>$cart_item['id'],
        //         'quantity'=>$cart_item['quantity'],
        //         'amount'=>$cart_item['amount'],
        //         'status'=>'new',
        //         'price'=>$cart_item['price'],
        //     );

        //     $cart=new Cart();
        //     $cart->fill($data);
        //     $cart->save();
        // }
        return view('frontend.pages.checkout');
    }
}
