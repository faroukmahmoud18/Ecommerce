<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipping;
use App\User;
use PDF;
use Notification;
use Helper;
use Illuminate\Support\Str;
use App\Notifications\StatusNotification;

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
            'coupon' => 'nullable|numeric',
            'phone' => 'numeric|required',
            'post_code' => 'string|nullable',
            'email' => 'string|required'
        ]);
    
        if (empty(Cart::where('user_id', auth()->user()->id)->where('order_id', null)->first())) {
            request()->session()->flash('error', __('flash_messages.cart_is_empty'));
            return back();
        }
    
        $order = new Order();
        $order_data = $request->all();
        $order_data['order_number'] = 'ORD-' . strtoupper(Str::random(10));
        $order_data['user_id'] = $request->user()->id;
        $order_data['shipping_id'] = $request->shipping;
    
        $shipping = Shipping::where('id', $order_data['shipping_id'])->pluck('price');
        $order_data['sub_total'] = Helper::totalCartPrice();
        $order_data['quantity'] = Helper::cartCount();
    
        if (session('coupon')) {
            $order_data['coupon'] = session('coupon')['value'];
        }
    
        $order_data['total_amount'] = Helper::totalCartPrice() + ($shipping[0] ?? 0) - (session('coupon')['value'] ?? 0);
        $order_data['status'] = "new";
    
        if (request('payment_method') == 'paypal') {
            $order_data['payment_method'] = 'paypal';
            $order_data['payment_status'] = 'paid';
        } else {
            $order_data['payment_method'] = 'cod';
            $order_data['payment_status'] = 'unpaid';
        }
    
        $order->fill($order_data);
        $order->save();
    
        // Save order items
        $cart_items = Cart::where('user_id', auth()->user()->id)->where('order_id', null)->get();
    
        foreach ($cart_items as $cart_item) {
            $order_item = new OrderItem();
            $order_item->order_id = $order->id;
            $order_item->product_id = $cart_item->product_id;
            $order_item->name = $cart_item->product->title; // Use product title
            $order_item->image = $cart_item->product->photo; // Use product photo
            $order_item->size = $cart_item->size;
            $order_item->price = $cart_item->price;
            $order_item->quantity = $cart_item->quantity;
            $order_item->save();
        }
        
    
        // Notify admin
        $users = User::where('role', 'admin')->first();
        $details = [
            'title' => __('notification.new_order_created_title'),
            'actionURL' => route('order.show', $order->id),
            'fas' => 'fa-file-alt'
        ];
        Notification::send($users, new StatusNotification($details));
    
        if (request('payment_method') == 'paypal') {
            return redirect()->route('payment')->with(['id' => $order->id]);
        } else {
            session()->forget('cart');
            session()->forget('coupon');
        }
    
        Cart::where('user_id', auth()->user()->id)->where('order_id', null)->update(['order_id' => $order->id]);
    
        request()->session()->flash('success', __('flash_messages.order_placed_success'));
        return redirect()->route('home');
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order=Order::find($id);
        // return $order;
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
        // return $request->status;
        if($request->status=='delivered'){
            foreach($order->cart as $cart){
                $product=$cart->product;
                // return $product;
                $product->stock -=$cart->quantity;
                $product->save();
            }
        }
        $status=$order->fill($data)->save();
        if($status){
            request()->session()->flash('success',__('flash_messages.order_updated_success'));
        }
        else{
            request()->session()->flash('error',__('flash_messages.order_updated_error'));
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
        // return $request->all();
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
            else{
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
        $order=Order::getAllOrder($request->id);
        // return $order;
        $file_name=$order->order_number.'-'.$order->first_name.'.pdf';
        // return $file_name;
        $pdf=PDF::loadview('backend.order.pdf',compact('order'));
        return $pdf->download($file_name);
    }
    // Income chart
    public function incomeChart(Request $request){
        $year=\Carbon\Carbon::now()->year;
        // dd($year);
        $items=Order::with(['cart_info'])->whereYear('created_at',$year)->where('status','delivered')->get()
            ->groupBy(function($d){
                return \Carbon\Carbon::parse($d->created_at)->format('m');
            });
            // dd($items);
        $result=[];
        foreach($items as $month=>$item_collections){
            foreach($item_collections as $item){
                $amount=$item->cart_info->sum('amount');
                // dd($amount);
                $m=intval($month);
                // return $m;
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
