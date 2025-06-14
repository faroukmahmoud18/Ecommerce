<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipping;
use App\Models\User; // Corrected namespace for User
use App\Models\Product; // Added for stock updates
use App\Models\ProductVariant; // Added for stock updates
use PDF;
use Notification;
use Helper;
use Illuminate\Support\Str;
use App\Notifications\StatusNotification;
use Illuminate\Support\Facades\DB; // Added for transaction

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders=Order::orderBy('id','DESC')->paginate(10);
        return view('backend.order.index')->with('orders',$orders);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'string|required',
            'last_name' => 'string|required',
            'address1' => 'string|required',
            'address2' => 'string|nullable',
            // 'coupon' => 'nullable|numeric', // Coupon is handled via session
            'phone' => 'numeric|required',
            'post_code' => 'string|nullable',
            'email' => 'string|required|email'
        ]);
    
        if (empty(Cart::where('user_id', auth()->user()->id)->where('order_id', null)->first())) {
            request()->session()->flash('error', __('flash_messages.cart_is_empty'));
            return back();
        }
        
        DB::beginTransaction();
        try {
            $order = new Order();
            $order_data = $request->all();
            $order_data['order_number'] = 'ORD-' . strtoupper(Str::random(10));
            $order_data['user_id'] = $request->user()->id;
            // 'shipping_id' should come from $request if selected, or be handled if default/no shipping
            $order_data['shipping_id'] = $request->shipping_id ?? null;

            $shipping_price = 0;
            if ($order_data['shipping_id']) {
                $shipping = Shipping::find($order_data['shipping_id']);
                if ($shipping) {
                    $shipping_price = $shipping->price;
                }
            }
            $order_data['delivery_charge'] = $shipping_price; // Store shipping charge separately

            $order_data['sub_total'] = Helper::totalCartPrice();
            $order_data['quantity'] = Helper::cartCount();

            $coupon_value = 0;
            if (session('coupon')) {
                $coupon_value = session('coupon')['value'];
                $order_data['coupon'] = $coupon_value;
            }

            $order_data['total_amount'] = Helper::totalCartPrice() + $shipping_price - $coupon_value;
            $order_data['status'] = "new";

            if (request('payment_method') == 'paypal') {
                $order_data['payment_method'] = 'paypal';
                $order_data['payment_status'] = 'paid';
            } else {
                $order_data['payment_method'] = 'cod';
                $order_data['payment_status'] = 'Unpaid'; // Standardized to Unpaid
            }

            $order->fill($order_data);
            $order->save();

            // Save order items
            $cart_items = Cart::with(['product', 'variant', 'variant.color', 'variant.size', 'variant.specification'])
                                ->where('user_id', auth()->user()->id)
                                ->where('order_id', null)
                                ->get();

            foreach ($cart_items as $cart_item) {
                $order_item = new OrderItem();
                $order_item->order_id = $order->id;
                $order_item->product_id = $cart_item->product_id;
                $order_item->variant_id = $cart_item->variant_id; // Store variant_id

                $itemName = $cart_item->product->title;
                if ($cart_item->variant) {
                    $itemName .= ' (';
                    $attributes = [];
                    if ($cart_item->variant->color) $attributes[] = $cart_item->variant->color->name;
                    if ($cart_item->variant->size) $attributes[] = $cart_item->variant->size->name;
                    if ($cart_item->variant->specification) $attributes[] = $cart_item->variant->specification->name .': '. $cart_item->variant->specification->value;
                    $itemName .= implode(', ', $attributes) . ')';
                }
                $order_item->name = $itemName;

                $order_item->image = $cart_item->product->photo;
                $order_item->size = $cart_item->variant ? $cart_item->variant->size->name ?? null : null; // Example, adjust as needed
                $order_item->price = $cart_item->price; // This is unit price from cart
                $order_item->quantity = $cart_item->quantity;
                $order_item->save();
            }

            // Notify admin
            $admin_users = User::where('role', 'admin')->get(); // Get all admins
            $details = [
                'title' => __('notification.new_order_created_title'),
                'actionURL' => route('order.show', $order->id),
                'fas' => 'fa-file-alt'
            ];
            if ($admin_users->count() > 0) {
                Notification::send($admin_users, new StatusNotification($details));
            }

            if (request('payment_method') == 'paypal') {
                 // Clear cart for PayPal flow after successful payment confirmation, not here.
                 // For now, we'll assume PayPal flow handles cart clearing.
                 DB::commit();
                return redirect()->route('payment')->with(['id' => $order->id]);
            } else {
                Cart::where('user_id', auth()->user()->id)->where('order_id', null)->delete(); // Delete cart items
                session()->forget('coupon');
            }

            // Cart::where('user_id', auth()->user()->id)->where('order_id', null)->update(['order_id' => $order->id]); // This was for associating cart items with order, now we create order_items
            DB::commit();
            request()->session()->flash('success', __('flash_messages.order_placed_success'));
            return redirect()->route('home');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order creation failed: ' . $e->getMessage());
            request()->session()->flash('error', __('flash_messages.error_please_try_again') . ' ' . $e->getMessage());
            return back()->withInput();
        }
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order=Order::with([
            'orderItems',
            'orderItems.product',
            'orderItems.variant',
            'orderItems.variant.color',
            'orderItems.variant.size',
            'orderItems.variant.specification',
            'shipping',
            'user'
        ])->find($id);

        if (!$order) {
            request()->session()->flash('error', __('flash_messages.order_not_found'));
            return redirect()->route('order.index');
        }
        return view('backend.order.show')->with('order',$order);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order=Order::find($id);
        return view('backend.order.edit')->with('order',$order);
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
        $order=Order::find($id);
        $this->validate($request,[
            'status'=>'required|in:new,process,delivered,cancel'
        ]);
        $data=$request->all();

        $originalStatus = $order->getOriginal('status');

        DB::beginTransaction();
        try {
            $orderStatusUpdated = $order->fill($data)->save();

            if ($orderStatusUpdated && $order->status == 'delivered' && $originalStatus != 'delivered') {
                foreach ($order->orderItems as $orderItem) { // Iterate through orderItems
                    if ($orderItem->variant_id && $orderItem->variant) {
                        $orderItem->variant->decrement('stock', $orderItem->quantity);
                    } elseif ($orderItem->product_id && $orderItem->product) { // product_id should always exist
                        $orderItem->product->decrement('stock', $orderItem->quantity);
                    }
                }
            }
            DB::commit();
            request()->session()->flash('success',__('flash_messages.order_updated_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order update failed: ' . $e->getMessage());
            request()->session()->flash('error',__('flash_messages.order_updated_error') . ' ' . $e->getMessage());
        }

        return redirect()->route('order.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order=Order::find($id);
        if($order){
            // Consider implications: should related order_items also be deleted?
            // Or set to null if order_id is nullable? Or rely on DB cascade?
            // For now, just deleting the order.
            $status=$order->delete();
            if($status){
                request()->session()->flash('success',__('flash_messages.order_deleted_success'));
            }
            else{
                request()->session()->flash('error',__('flash_messages.order_deleted_error'));
            }
            return redirect()->route('order.index');
        }
        else{
            request()->session()->flash('error',__('flash_messages.order_not_found'));
            return redirect()->back();
        }
    }

    public function orderTrack(){
        return view('frontend.pages.order-track');
    }

    public function productTrackOrder(Request $request){
        $this->validate($request, [
            'order_number' => 'required|string'
        ]);
        $order=Order::where('user_id',auth()->user()->id)->where('order_number',$request->order_number)->first();
        if($order){
            if($order->status=="new"){
            request()->session()->flash('success',__('flash_messages.order_track_status_new'));
            return redirect()->route('home');

            }
            elseif($order->status=="process"){
                request()->session()->flash('success',__('flash_messages.order_track_status_process'));
                return redirect()->route('home');
    
            }
            elseif($order->status=="delivered"){
                request()->session()->flash('success',__('flash_messages.order_track_status_delivered'));
                return redirect()->route('home');
    
            }
            else{ // Assuming 'cancel' or other statuses
                request()->session()->flash('error',__('flash_messages.order_track_status_canceled'));
                return redirect()->route('home');
    
            }
        }
        else{
            request()->session()->flash('error',__('flash_messages.order_track_invalid_number'));
            return back();
        }
    }

    // PDF generate
    public function pdf(Request $request){
        $order=Order::getAllOrder($request->id); // This method might need update to fetch orderItems with variant details
        if(!$order){
            request()->session()->flash('error', __('flash_messages.order_not_found'));
            return back();
        }
        $file_name=$order->order_number.'-'.$order->first_name.'.pdf';
        $pdf=PDF::loadview('backend.order.pdf',compact('order'));
        return $pdf->download($file_name);
    }

    // Income chart
    public function incomeChart(Request $request){
        $year=\Carbon\Carbon::now()->year;

        // This logic might need adjustment if cart_info doesn't accurately reflect final order amounts with variants
        $items=Order::with(['orderItems'])->whereYear('created_at',$year)->where('status','delivered')->get()
            ->groupBy(function($d){
                return \Carbon\Carbon::parse($d->created_at)->format('m');
            });

        $result=[];
        foreach($items as $month=>$item_collections){
            foreach($item_collections as $item){
                // Summing up based on orderItems price and quantity
                $amount = $item->orderItems->sum(function($orderItem) {
                    return $orderItem->price * $orderItem->quantity;
                });
                $m=intval($month);
                isset($result[$m]) ? $result[$m] += $amount :$result[$m]=$amount;
            }
        }
        $data=[];
        for($i=1; $i <=12; $i++){
            $monthName=date('F', mktime(0,0,0,$i,1));
            $data[$monthName] = (!empty($result[$i]))? number_format((float)($result[$i]), 2, '.', '') : 0.0;
        }
        return $data;
    }
}
