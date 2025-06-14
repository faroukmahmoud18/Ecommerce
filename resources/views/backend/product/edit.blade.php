@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">{{__('product.page_title_edit')}}</h5>
    <div class="card-body">
      <form method="post" action="{{route('product.update',$product->id)}}">
        @csrf 
        @method('PATCH')
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">{{__('product.form_label_title')}} <span class="text-danger">*</span></label>
          <input id="inputTitle" type="text" name="title" placeholder="{{__('product.form_placeholder_title')}}"  value="{{$product->title}}" class="form-control">
          @error('title')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="summary" class="col-form-label">{{__('product.form_label_summary')}} <span class="text-danger">*</span></label>
          <textarea class="form-control" id="summary" name="summary">{{$product->summary}}</textarea>
          @error('summary')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="description" class="col-form-label">{{__('product.form_label_description')}}</label>
          <textarea class="form-control" id="description" name="description">{{$product->description}}</textarea>
          @error('description')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>


        <div class="form-group">
          <label for="is_featured">{{__('product.form_label_is_featured')}}</label><br>
          <input type="checkbox" name='is_featured' id='is_featured' value='{{$product->is_featured}}' {{(($product->is_featured) ? 'checked' : '')}}> {{__('admin_common.yes')}}
        </div>
              {{-- {{$categories}} --}}

        <div class="form-group">
          <label for="cat_id">{{__('product.form_label_category')}} <span class="text-danger">*</span></label>
          <select name="cat_id" id="cat_id" class="form-control">
              <option value="">{{__('product.form_select_placeholder_category')}}</option>
              @foreach($categories as $key=>$cat_data)
                  <option value='{{$cat_data->id}}' {{(($product->cat_id==$cat_data->id)? 'selected' : '')}}>{{$cat_data->title}}</option>
              @endforeach
          </select>
        </div>
        @php 
          $sub_cat_info=DB::table('categories')->select('title')->where('id',$product->child_cat_id)->get();
        // dd($sub_cat_info);

        @endphp
        {{-- {{$product->child_cat_id}} --}}
        <div class="form-group {{(($product->child_cat_id)? '' : 'd-none')}}" id="child_cat_div">
          <label for="child_cat_id">{{__('product.form_label_sub_category')}}</label>
          <select name="child_cat_id" id="child_cat_id" class="form-control">
              <option value="">{{__('product.form_select_placeholder_sub_category')}}</option>
              
          </select>
        </div>

        <div class="form-group">
          <label for="price" class="col-form-label">{{__('product.form_label_price_nrs')}} <span class="text-danger">*</span></label>
          <input id="price" type="number" name="price" placeholder="{{__('product.form_placeholder_price')}}"  value="{{$product->price}}" class="form-control">
          @error('price')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="discount" class="col-form-label">{{__('product.form_label_discount_percentage')}}</label>
          <input id="discount" type="number" name="discount" min="0" max="100" placeholder="{{__('product.form_placeholder_discount')}}"  value="{{$product->discount}}" class="form-control">
          @error('discount')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        {{-- Old Size Field Removed --}}
        <div class="form-group">
          <label for="brand_id">{{__('product.form_label_brand')}}</label>
          <select name="brand_id" class="form-control">
              <option value="">{{__('product.form_select_placeholder_brand')}}</option>
             @foreach($brands as $brand)
              <option value="{{$brand->id}}" {{(($product->brand_id==$brand->id)? 'selected':'')}}>{{$brand->title}}</option>
             @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="condition">{{__('product.form_label_condition')}}</label>
          <select name="condition" class="form-control">
              <option value="">{{__('product.form_select_placeholder_condition')}}</option>
              <option value="default" {{(($product->condition=='default')? 'selected':'')}}>{{__('product.form_option_condition_default')}}</option>
              <option value="new" {{(($product->condition=='new')? 'selected':'')}}>{{__('product.form_option_condition_new')}}</option>
              <option value="hot" {{(($product->condition=='hot')? 'selected':'')}}>{{__('product.form_option_condition_hot')}}</option>
          </select>
        </div>

        <div class="form-group">
          <label for="stock">{{__('product.form_label_quantity')}} <span class="text-danger">*</span></label>
          <input id="quantity" type="number" name="stock" min="0" placeholder="{{__('product.form_placeholder_quantity')}}"  value="{{$product->stock}}" class="form-control">
          @error('stock')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group">
          <label for="inputPhoto" class="col-form-label">{{__('product.form_label_photo')}} <span class="text-danger">*</span></label>
          <div class="input-group">
              <span class="input-group-btn">
                  <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
                  <i class="fas fa-image"></i> {{__('admin_common.button_choose')}}
                  </a>
              </span>
          <input id="thumbnail" class="form-control" type="text" name="photo" value="{{$product->photo}}">
        </div>
        <div id="holder" style="margin-top:15px;max-height:100px;"></div>
          @error('photo')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        
        <div class="form-group">
          <label for="status" class="col-form-label">{{__('admin_common.form_label_status')}} <span class="text-danger">*</span></label>
          <select name="status" class="form-control">
            <option value="active" {{(($product->status=='active')? 'selected' : '')}}>{{__('admin_common.status_active')}}</option>
            <option value="inactive" {{(($product->status=='inactive')? 'selected' : '')}}>{{__('admin_common.status_inactive')}}</option>
        </select>
          @error('status')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <hr>
        <h4>{{ __('product.section_title_variations') }}</h4>
        <div id="product-variants-container">
            @php $variant_idx = 0; @endphp
            @if($product->variants && $product->variants->count() > 0)
                @foreach($product->variants as $variant_item)
                    <div class="product-variant-item row mb-2">
                        <input type="hidden" name="variants[{{ $variant_idx }}][id]" value="{{ $variant_item->id }}">
                        <div class="col-md-2">
                            <label>{{ __('product.form_label_variant_color') }}</label>
                            <select name="variants[{{ $variant_idx }}][color_id]" class="form-control">
                                <option value="">{{ __('admin_common.form_select_placeholder_none') }}</option>
                                @foreach($colors as $color)
                                    <option value="{{ $color->id }}" {{ old('variants.'.$variant_idx.'.color_id', $variant_item->color_id) == $color->id ? 'selected' : '' }}>{{ $color->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>{{ __('product.form_label_variant_size') }}</label>
                            <select name="variants[{{ $variant_idx }}][size_id]" class="form-control">
                                <option value="">{{ __('admin_common.form_select_placeholder_none') }}</option>
                                @foreach($sizes as $size)
                                    <option value="{{ $size->id }}" {{ old('variants.'.$variant_idx.'.size_id', $variant_item->size_id) == $size->id ? 'selected' : '' }}>{{ $size->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>{{ __('product.form_label_variant_specification') }}</label>
                            <select name="variants[{{ $variant_idx }}][specification_id]" class="form-control">
                                <option value="">{{ __('admin_common.form_select_placeholder_none') }}</option>
                                @foreach($specifications as $spec)
                                    <option value="{{ $spec->id }}" {{ old('variants.'.$variant_idx.'.specification_id', $variant_item->specification_id) == $spec->id ? 'selected' : '' }}>{{ $spec->name }} - {{ $spec->value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>{{ __('product.form_label_variant_price') }} <span class="text-danger">*</span></label>
                            <input type="number" name="variants[{{ $variant_idx }}][price]" class="form-control" value="{{ old('variants.'.$variant_idx.'.price', $variant_item->price) }}" placeholder="{{ __('product.form_placeholder_price') }}" step="0.01">
                        </div>
                        <div class="col-md-1">
                            <label>{{ __('product.form_label_variant_stock') }} <span class="text-danger">*</span></label>
                            <input type="number" name="variants[{{ $variant_idx }}][stock]" class="form-control" value="{{ old('variants.'.$variant_idx.'.stock', $variant_item->stock) }}" placeholder="{{ __('product.form_placeholder_stock') }}" step="1">
                        </div>
                        <div class="col-md-2">
                            <label>{{ __('product.form_label_variant_sku') }}</label>
                            <input type="text" name="variants[{{ $variant_idx }}][sku]" class="form-control" value="{{ old('variants.'.$variant_idx.'.sku', $variant_item->sku) }}" placeholder="{{ __('product.form_placeholder_sku') }}">
                        </div>
                        <div class="col-md-12 mt-1">
                            <button type="button" class="btn btn-danger btn-sm remove-variant-btn">{{ __('admin_common.button_remove') }}</button>
                        </div>
                    </div>
                    @php $variant_idx++; @endphp
                @endforeach
            @else
                {{-- Display one empty row if no variants exist --}}
                <div class="product-variant-item row mb-2">
                    <div class="col-md-2">
                        <label>{{ __('product.form_label_variant_color') }}</label>
                        <select name="variants[0][color_id]" class="form-control">
                            <option value="">{{ __('admin_common.form_select_placeholder_none') }}</option>
                            @foreach($colors as $color)
                                <option value="{{ $color->id }}">{{ $color->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>{{ __('product.form_label_variant_size') }}</label>
                        <select name="variants[0][size_id]" class="form-control">
                            <option value="">{{ __('admin_common.form_select_placeholder_none') }}</option>
                            @foreach($sizes as $size)
                                <option value="{{ $size->id }}">{{ $size->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('product.form_label_variant_specification') }}</label>
                        <select name="variants[0][specification_id]" class="form-control">
                            <option value="">{{ __('admin_common.form_select_placeholder_none') }}</option>
                            @foreach($specifications as $spec)
                                <option value="{{ $spec->id }}">{{ $spec->name }} - {{ $spec->value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>{{ __('product.form_label_variant_price') }} <span class="text-danger">*</span></label>
                        <input type="number" name="variants[0][price]" class="form-control" placeholder="{{ __('product.form_placeholder_price') }}" step="0.01">
                    </div>
                    <div class="col-md-1">
                        <label>{{ __('product.form_label_variant_stock') }} <span class="text-danger">*</span></label>
                        <input type="number" name="variants[0][stock]" class="form-control" placeholder="{{ __('product.form_placeholder_stock') }}" step="1">
                    </div>
                    <div class="col-md-2">
                        <label>{{ __('product.form_label_variant_sku') }}</label>
                        <input type="text" name="variants[0][sku]" class="form-control" placeholder="{{ __('product.form_placeholder_sku') }}">
                    </div>
                    <div class="col-md-12 mt-1">
                        <button type="button" class="btn btn-danger btn-sm remove-variant-btn" style="display:none;">{{ __('admin_common.button_remove') }}</button>
                    </div>
                </div>
            @endif
        </div>
        <button type="button" id="add-variant-btn" class="btn btn-info btn-sm mt-2">{{ __('product.button_add_new_variation') }}</button>

        <div class="form-group mb-3 mt-3">
           <button class="btn btn-success" type="submit">{{__('admin_common.button_update')}}</button>
        </div>
      </form>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />

@endpush
@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>

<script>
    $('#lfm').filemanager('image');

    $(document).ready(function() {
    $('#summary').summernote({
      placeholder: "{{__('product.form_placeholder_summary_short')}}",
        tabsize: 2,
        height: 150
    });
    });
    $(document).ready(function() {
      $('#description').summernote({
        placeholder: "{{__('product.form_placeholder_description')}}",
          tabsize: 2,
          height: 150
      });
    });
</script>

<script>
    $(document).ready(function() {
        let variantIndex = {{ $product->variants->count() > 0 ? $product->variants->count() -1 : -1 }};
        // If no variants rendered by PHP and container is empty, first new is index 0.
        // If variants ARE rendered, variantIndex is set to the last index of those.
        // So, first click on "add" should increment to the next correct new index.
        if ($('.product-variant-item').length === 0 && {{ $product->variants->count() === 0 }}) {
             variantIndex = -1;
        }


        function updateRemoveButtons() {
            if ($('.product-variant-item').length <= 1) {
                 if ($('.product-variant-item').find('input[name$="[id]"]').length > 0 && $('.product-variant-item').length === 1) {
                    $('.remove-variant-btn').show();
                 } else {
                    $('.remove-variant-btn').hide();
                 }
            } else {
                $('.remove-variant-btn').show();
            }
        }
        // Initial call to set button visibility based on pre-rendered variants
        updateRemoveButtons();


        $('#add-variant-btn').on('click', function() {
            variantIndex++;
            const newVariantHtml = `
            <div class="product-variant-item row mb-2">
                {{-- NO hidden ID input for new rows --}}
                <div class="col-md-2">
                    <label>{{ __('product.form_label_variant_color') }}</label>
                    <select name="variants[${variantIndex}][color_id]" class="form-control">
                        <option value="">{{ __('admin_common.form_select_placeholder_none') }}</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>{{ __('product.form_label_variant_size') }}</label>
                    <select name="variants[${variantIndex}][size_id]" class="form-control">
                        <option value="">{{ __('admin_common.form_select_placeholder_none') }}</option>
                        @foreach($sizes as $size)
                            <option value="{{ $size->id }}">{{ $size->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>{{ __('product.form_label_variant_specification') }}</label>
                    <select name="variants[${variantIndex}][specification_id]" class="form-control">
                        <option value="">{{ __('admin_common.form_select_placeholder_none') }}</option>
                        @foreach($specifications as $spec)
                            <option value="{{ $spec->id }}">{{ $spec->name }} - {{ $spec->value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>{{ __('product.form_label_variant_price') }} <span class="text-danger">*</span></label>
                    <input type="number" name="variants[${variantIndex}][price]" class="form-control" placeholder="{{ __('product.form_placeholder_price') }}" step="0.01">
                </div>
                <div class="col-md-1">
                    <label>{{ __('product.form_label_variant_stock') }} <span class="text-danger">*</span></label>
                    <input type="number" name="variants[${variantIndex}][stock]" class="form-control" placeholder="{{ __('product.form_placeholder_stock') }}" step="1">
                </div>
                <div class="col-md-2">
                    <label>{{ __('product.form_label_variant_sku') }}</label>
                    <input type="text" name="variants[${variantIndex}][sku]" class="form-control" placeholder="{{ __('product.form_placeholder_sku') }}">
                </div>
                <div class="col-md-12 mt-1">
                    <button type="button" class="btn btn-danger btn-sm remove-variant-btn">{{ __('admin_common.button_remove') }}</button>
                </div>
            </div>`;
            $('#product-variants-container').append(newVariantHtml);
            updateRemoveButtons();
        });

        $('#product-variants-container').on('click', '.remove-variant-btn', function() {
            $(this).closest('.product-variant-item').remove();
            updateRemoveButtons();
        });
        // Call again for initial state after page load, especially if there's only one existing variant
        updateRemoveButtons();
    });
    </script>

<script>
  var  child_cat_id='{{$product->child_cat_id}}';
        // alert(child_cat_id);
        $('#cat_id').change(function(){
            var cat_id=$(this).val();

            if(cat_id !=null){
                // ajax call
                $.ajax({
                    url:"/admin/category/"+cat_id+"/child",
                    type:"POST",
                    data:{
                        _token:"{{csrf_token()}}"
                    },
                    success:function(response){
                        if(typeof(response)!='object'){
                            response=$.parseJSON(response);
                        }
                        var html_option="<option value=''>{{__('product.form_select_placeholder_any_one')}}</option>";
                        if(response.status){
                            var data=response.data;
                            if(response.data){
                                $('#child_cat_div').removeClass('d-none');
                                $.each(data,function(id,title){
                                    html_option += "<option value='"+id+"' "+(child_cat_id==id ? 'selected ' : '')+">"+title+"</option>";
                                });
                            }
                            else{
                                console.log('no response data');
                            }
                        }
                        else{
                            $('#child_cat_div').addClass('d-none');
                        }
                        $('#child_cat_id').html(html_option);

                    }
                });
            }
            else{

            }

        });
        if(child_cat_id!=null){
            $('#cat_id').change();
        }
</script>
@endpush