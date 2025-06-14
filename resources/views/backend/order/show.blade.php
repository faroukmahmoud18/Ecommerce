@extends('backend.layouts.master')

@section('title',__('order.page_title_show'))

@section('main-content')
<div class="card">
  <h5 class="card-header">{{__('order.section_title_order')}}
    <a href="{{route('order.pdf',$order->id)}}" class="btn btn-sm btn-primary shadow-sm float-right">
      <i class="fas fa-download fa-sm text-white-50"></i> {{__('order.button_generate_pdf')}}
    </a>
  </h5>
  <div class="card-body">
    @if($order)
    <table class="table table-striped table-hover">
      <thead>
        <tr>
            <th>{{__('admin_common.table_header_sn')}}</th>
            <th>{{__('order.table_header_order_no')}}</th>
            <th>{{__('order.table_header_name')}}</th>
            <th>{{__('order.table_header_email')}}</th>
            <th>{{__('order.table_header_quantity')}}</th>
            <th>{{__('order.table_header_charge')}}</th>
            <th>{{__('order.table_header_total_amount')}}</th>
            <th>{{__('admin_common.table_header_status')}}</th>
            <th>{{__('admin_common.table_header_actions')}}</th>
        </tr>
      </thead>
      <tbody>
        <tr>
            <td>{{$order->id}}</td>
            <td>{{$order->order_number}}</td>
            <td>{{$order->first_name}} {{$order->last_name}}</td>
            <td>{{$order->email}}</td>
            <td>{{$order->quantity}}</td>
            @if(empty($order->shipping->price))
            <td>0.00</td>
            @else
            <td> : $ {{$order->shipping->price}}</td>
            @endif
            <td>${{number_format($order->total_amount,2)}}</td>
            <td>
                @if($order->status=='new')
                  <span class="badge badge-primary">{{$order->status}}</span>
                @elseif($order->status=='process')
                  <span class="badge badge-warning">{{$order->status}}</span>
                @elseif($order->status=='delivered')
                  <span class="badge badge-success">{{$order->status}}</span>
                @else
                  <span class="badge badge-danger">{{$order->status}}</span>
                @endif
            </td>
            <td>
                <a href="{{route('order.edit',$order->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="{{__('admin_common.edit_button_tooltip')}}" data-placement="bottom"><i class="fas fa-edit"></i></a>
                <form method="POST" action="{{route('order.destroy',[$order->id])}}">
                  @csrf
                  @method('delete')
                      <button class="btn btn-danger btn-sm dltBtn" data-id={{$order->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="{{__('admin_common.delete_button_tooltip')}}"><i class="fas fa-trash-alt"></i></button>
                </form>
            </td>
        </tr>
      </tbody>
    </table>

    <!-- Order Product Details -->
    <h4 class="text-center pb-4">{{__('order.section_title_order_products')}}</h4>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>{{__('admin_common.table_header_sn')}}</th>
          <th>{{__('order.table_header_image')}}</th>
          <th>{{__('order.table_header_product_name')}}</th>
          <th>{{__('admin_common.table_header_price')}}</th>
          <th>{{__('admin_common.table_header_size')}}</th>
          <th>{{__('order.table_header_quantity')}}</th>
          <th>{{__('order.table_header_subtotal')}}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->orderItems as $item)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td><img src="{{ asset($item->image) }}" alt="Product Image" style="width:50px"></td>
          <td>
            {{ $item->name }}
            @if($item->variant)
                <div style="font-size: 0.9em; color: #555;">
                    <small>
                        @if($item->variant->sku)
                            SKU: {{ $item->variant->sku }}<br>
                        @endif
                        @if($item->variant->color)
                            {{ __('cart.label_color') }}: {{ $item->variant->color->name }}<br>
                        @endif
                        @if($item->variant->size)
                            {{ __('cart.label_size') }}: {{ $item->variant->size->name }}<br>
                        @endif
                        @if($item->variant->specification)
                            {{ $item->variant->specification->name }}: {{ $item->variant->specification->value }}
                        @endif
                    </small>
                </div>
            @endif
          </td>
          <td>${{ number_format($item->price, 2) }}</td>
          <td>{{ $item->size }}</td>
          <td>{{ $item->quantity }}</td>
          <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <section class="confirmation_part section_padding">
      <div class="order_boxes">
        <div class="row">
          <div class="col-lg-6 col-lx-4">
            <div class="order-info">
              <h4 class="text-center pb-4">{{__('order.section_title_order_information')}}</h4>
              <table class="table">
                    <tr class="">
                        <td>{{__('order.label_order_number')}}</td>
                        <td> : {{$order->order_number}}</td>
                    </tr>
                    <tr>
                        <td>{{__('order.label_order_date')}}</td>
                        <td> : {{$order->created_at->format('D d M, Y')}} at {{$order->created_at->format('g : i a')}} </td>
                    </tr>
                    <tr>
                        <td>{{__('order.label_quantity')}}</td>
                        <td> : {{$order->quantity}}</td>
                    </tr>
                    <tr>
                        <td>{{__('order.label_order_status')}}</td>
                        <td> : {{$order->status}}</td>
                    </tr>
                    <tr>
                        <td>{{__('order.label_shipping_charge')}}</td>
                        @if(empty($order->shipping->price))
                        <td>0.00</td>
                        @else
                        <td> : $ {{$order->shipping->price}}</td>
                        @endif
                    </tr>
                    <tr>
                      <td>{{__('order.label_coupon')}}</td>
                      <td> : $ {{number_format($order->coupon,2)}}</td>
                    </tr>
                    <tr>
                        <td>{{__('order.label_total_amount')}}</td>
                        <td> : $ {{number_format($order->total_amount,2)}}</td>
                    </tr>
                    <tr>
                        <td>{{__('order.label_payment_method')}}</td>
                        <td> : @if($order->payment_method=='cod') {{__('order.payment_method_cod')}} @else {{__('order.payment_method_paypal')}} @endif</td>
                    </tr>
                    <tr>
                        <td>{{__('order.label_payment_status')}}</td>
                        <td> : {{$order->payment_status}}</td>
                    </tr>
              </table>
            </div>
          </div>

          <div class="col-lg-6 col-lx-4">
            <div class="shipping-info">
              <h4 class="text-center pb-4">{{__('order.section_title_shipping_information')}}</h4>
              <table class="table">
                    <tr class="">
                        <td>{{__('order.label_full_name')}}</td>
                        <td> : {{$order->first_name}} {{$order->last_name}}</td>
                    </tr>
                    <tr>
                        <td>{{__('order.label_email')}}</td>
                        <td> : {{$order->email}}</td>
                    </tr>
                    <tr>
                        <td>{{__('order.label_phone_no')}}</td>
                        <td> : {{$order->phone}}</td>
                    </tr>
                    <tr>
                        <td>{{__('order.label_address')}}</td>
                        <td> : {{$order->address1}}, {{$order->address2}}</td>
                    </tr>
                    <tr>
                        <td>{{__('order.label_country')}}</td>
                        <td> : {{$order->country}}</td>
                    </tr>
                    <tr>
                        <td>{{__('order.label_post_code')}}</td>
                        <td> : {{$order->post_code}}</td>
                    </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
    @endif

  </div>
</div>
@endsection

@push('styles')
<style>
    .order-info,.shipping-info{
        background:#ECECEC;
        padding:20px;
    }
    .order-info h4,.shipping-info h4{
        text-decoration: underline;
    }
</style>
@endpush
