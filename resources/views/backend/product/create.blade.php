@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">{{__('product.page_title_create')}}</h5>
    <div class="card-body">
      <form method="post" action="{{route('product.store')}}">
        {{csrf_field()}}
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">{{__('product.form_label_title')}} <span class="text-danger">*</span></label>
          <input id="inputTitle" type="text" name="title" placeholder="{{__('product.form_placeholder_title')}}"  value="{{old('title')}}" class="form-control">
          @error('title')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="summary" class="col-form-label">{{__('product.form_label_summary')}} <span class="text-danger">*</span></label>
          <textarea class="form-control" id="summary" name="summary">{{old('summary')}}</textarea>
          @error('summary')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="description" class="col-form-label">{{__('product.form_label_description')}}</label>
          <textarea class="form-control" id="description" name="description">{{old('description')}}</textarea>
          @error('description')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>


        <div class="form-group">
          <label for="is_featured">{{__('product.form_label_is_featured')}}</label><br>
          <input type="checkbox" name='is_featured' id='is_featured' value='1' checked> {{__('admin_common.yes')}}
        </div>
              {{-- {{$categories}} --}}

        <div class="form-group">
          <label for="cat_id">{{__('product.form_label_category')}} <span class="text-danger">*</span></label>
          <select name="cat_id" id="cat_id" class="form-control">
              <option value="">{{__('product.form_select_placeholder_category')}}</option>
              @foreach($categories as $key=>$cat_data)
                  <option value='{{$cat_data->id}}'>{{$cat_data->title}}</option>
              @endforeach
          </select>
        </div>

        <div class="form-group d-none" id="child_cat_div">
          <label for="child_cat_id">{{__('product.form_label_sub_category')}}</label>
          <select name="child_cat_id" id="child_cat_id" class="form-control">
              <option value="">{{__('product.form_select_placeholder_sub_category')}}</option>
              {{-- @foreach($parent_cats as $key=>$parent_cat)
                  <option value='{{$parent_cat->id}}'>{{$parent_cat->title}}</option>
              @endforeach --}}
          </select>
        </div>

        <div class="form-group">
          <label for="price" class="col-form-label">{{__('product.form_label_price_nrs')}} <span class="text-danger">*</span></label>
          <input id="price" type="number" name="price" placeholder="{{__('product.form_placeholder_price')}}"  value="{{old('price')}}" class="form-control">
          @error('price')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="discount" class="col-form-label">{{__('product.form_label_discount_percentage')}}</label>
          <input id="discount" type="number" name="discount" min="0" max="100" placeholder="{{__('product.form_placeholder_discount')}}"  value="{{old('discount')}}" class="form-control">
          @error('discount')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        {{-- Old Size Field Removed --}}

        <div class="form-group">
          <label for="brand_id">{{__('product.form_label_brand')}}</label>
          {{-- {{$brands}} --}}

          <select name="brand_id" class="form-control">
              <option value="">{{__('product.form_select_placeholder_brand')}}</option>
             @foreach($brands as $brand)
              <option value="{{$brand->id}}">{{$brand->title}}</option>
             @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="condition">{{__('product.form_label_condition')}}</label>
          <select name="condition" class="form-control">
              <option value="">{{__('product.form_select_placeholder_condition')}}</option>
              <option value="default">{{__('product.form_option_condition_default')}}</option>
              <option value="new">{{__('product.form_option_condition_new')}}</option>
              <option value="hot">{{__('product.form_option_condition_hot')}}</option>
          </select>
        </div>

        <div class="form-group">
          <label for="stock">{{__('product.form_label_quantity')}} <span class="text-danger">*</span></label>
          <input id="quantity" type="number" name="stock" min="0" placeholder="{{__('product.form_placeholder_quantity')}}"  value="{{old('stock')}}" class="form-control">
          @error('stock')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group">
          <label for="inputPhoto" class="col-form-label">{{__('product.form_label_photo')}} <span class="text-danger">*</span></label>
          <div class="input-group">
              <span class="input-group-btn">
                  <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
                  <i class="fa fa-picture-o"></i> {{__('admin_common.button_choose')}}
                  </a>
              </span>
          <input id="thumbnail" class="form-control" type="text" name="photo" value="{{old('photo')}}">
        </div>
        <div id="holder" style="margin-top:15px;max-height:100px;"></div>
          @error('photo')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        
        <div class="form-group">
          <label for="status" class="col-form-label">{{__('admin_common.form_label_status')}} <span class="text-danger">*</span></label>
          <select name="status" class="form-control">
              <option value="active">{{__('admin_common.status_active')}}</option>
              <option value="inactive">{{__('admin_common.status_inactive')}}</option>
          </select>
          @error('status')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <hr>
        <h4>{{ __('product.section_title_variations') }}</h4>
        <div id="product-variants-container">
            {{-- Initial variation row --}}
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
                    <input type="number" name="variants[0][price]" class="form-control" placeholder="{{ __('product.form_placeholder_price') }}" step="0.01" value="{{ old('variants.0.price') }}">
                </div>
                <div class="col-md-1">
                    <label>{{ __('product.form_label_variant_stock') }} <span class="text-danger">*</span></label>
                    <input type="number" name="variants[0][stock]" class="form-control" placeholder="{{ __('product.form_placeholder_stock') }}" step="1" value="{{ old('variants.0.stock') }}">
                </div>
                <div class="col-md-2">
                    <label>{{ __('product.form_label_variant_sku') }}</label>
                    <input type="text" name="variants[0][sku]" class="form-control" placeholder="{{ __('product.form_placeholder_sku') }}" value="{{ old('variants.0.sku') }}">
                </div>
                <div class="col-md-12 mt-1">
                    <button type="button" class="btn btn-danger btn-sm remove-variant-btn" style="display:none;">{{ __('admin_common.button_remove') }}</button>
                </div>
            </div>
        </div>
        <button type="button" id="add-variant-btn" class="btn btn-info btn-sm mt-2">{{ __('product.button_add_variation') }}</button>

        <div class="form-group mb-3 mt-3">
          <button type="reset" class="btn btn-warning">{{__('admin_common.button_reset')}}</button>
           <button class="btn btn-success" type="submit">{{__('admin_common.button_submit')}}</button>
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
   $(document).ready(function() {
  // Initialize file manager with error handling
  $('#lfm').filemanager('image').on('error', function(e, data) {
    console.error('FileManager Error:', data);
    alert("{{__('admin_common.alert_error_uploading_file')}}" + data.message);
  });

  // Summernote configuration with explicit image upload settings
  const summernoteConfig = {
    placeholder: "{{__('product.form_placeholder_description')}}",
    tabsize: 2,
    height: 150,
    callbacks: {
      onImageUploadError: function(msg) {
        console.error('Image upload error:', msg);
        alert("{{__('admin_common.alert_image_upload_failed')}}" + msg);
      }
    }
  };

  $('#summary').summernote({ ...summernoteConfig, height: 100 });
  $('#description').summernote(summernoteConfig);
});
</script>

<script>
  $('#cat_id').change(function(){
    var cat_id=$(this).val();
    // alert(cat_id);
    if(cat_id !=null){
      // Ajax call
      $.ajax({
        url:"/admin/category/"+cat_id+"/child",
        data:{
          _token:"{{csrf_token()}}",
          id:cat_id
        },
        type:"POST",
        success:function(response){
          if(typeof(response) !='object'){
            response=$.parseJSON(response)
          }
          // console.log(response);
          var html_option="<option value=''>{{__('product.form_select_placeholder_sub_category_js')}}</option>"
          if(response.status){
            var data=response.data;
            // alert(data);
            if(response.data){
              $('#child_cat_div').removeClass('d-none');
              $.each(data,function(id,title){
                html_option +="<option value='"+id+"'>"+title+"</option>"
              });
            }
            else{
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
  })
</script>

<script>
    $(document).ready(function() {
        let variantIndex = 0;
        if ($('.product-variant-item').length > 0) {
             variantIndex = $('.product-variant-item').length -1;
        }

        function updateRemoveButtons() {
            if ($('.product-variant-item').length <= 1) {
                $('.remove-variant-btn').hide();
            } else {
                $('.remove-variant-btn').show();
            }
        }
        updateRemoveButtons();

        $('#add-variant-btn').on('click', function() {
            variantIndex++;
            const variantHtml = `
                <div class="product-variant-item row mb-2">
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
            $('#product-variants-container').append(variantHtml);
            updateRemoveButtons();
        });

        $('#product-variants-container').on('click', '.remove-variant-btn', function() {
            $(this).closest('.product-variant-item').remove();
            updateRemoveButtons();
        });

        updateRemoveButtons();
    });
    </script>
@endpush