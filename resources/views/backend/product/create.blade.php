@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Add Product</h5>
    <div class="card-body">
      <form method="post" action="{{route('product.store')}}">
        {{csrf_field()}}
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
          <input id="inputTitle" type="text" name="title" placeholder="Enter title"  value="{{old('title')}}" class="form-control">
          @error('title')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="summary" class="col-form-label">Summary <span class="text-danger">*</span></label>
          <textarea class="form-control" id="summary" name="summary">{{old('summary')}}</textarea>
          @error('summary')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="description" class="col-form-label">Description</label>
          <textarea class="form-control" id="description" name="description">{{old('description')}}</textarea>
          @error('description')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>


        <div class="form-group">
          <label for="is_featured">Is Featured</label><br>
          <input type="checkbox" name='is_featured' id='is_featured' value='1' checked> Yes                        
        </div>
              {{-- {{$categories}} --}}

        <div class="form-group">
          <label for="cat_id">Category <span class="text-danger">*</span></label>
          <select name="cat_id" id="cat_id" class="form-control">
              <option value="">--Select any category--</option>
              @foreach($categories as $key=>$cat_data)
                  <option value='{{$cat_data->id}}'>{{$cat_data->title}}</option>
              @endforeach
          </select>
        </div>

        <div class="form-group d-none" id="child_cat_div">
          <label for="child_cat_id">Sub Category</label>
          <select name="child_cat_id" id="child_cat_id" class="form-control">
              <option value="">--Select any category--</option>
              {{-- @foreach($parent_cats as $key=>$parent_cat)
                  <option value='{{$parent_cat->id}}'>{{$parent_cat->title}}</option>
              @endforeach --}}
          </select>
        </div>

        <div class="form-group">
          <label for="price" class="col-form-label">Price(NRS) <span class="text-danger">*</span></label>
          <input id="price" type="number" name="price" placeholder="Enter price"  value="{{old('price')}}" class="form-control">
          @error('price')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="discount" class="col-form-label">Discount(%)</label>
          <input id="discount" type="number" name="discount" min="0" max="100" placeholder="Enter discount"  value="{{old('discount')}}" class="form-control">
          @error('discount')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        {{-- Old Size field removed --}}

        {{-- Product Variations Section --}}
        <div class="form-group">
          <label class="col-form-label">Product Variations</label>
          <div id="product-variations-container">
            {{-- Initial variation row (index 0) --}}
            <div class="variation-row row mb-2">
              <div class="col-md-2">
                <label>Color</label>
                <select name="variants[0][color_id]" class="form-control">
                  <option value="">-- Select Color --</option>
                  @if(isset($colors))
                    @foreach($colors as $color)
                      <option value="{{ $color->id }}">{{ $color->name }}</option>
                    @endforeach
                  @endif
                </select>
              </div>
              <div class="col-md-2">
                <label>Size</label>
                <select name="variants[0][size_id]" class="form-control">
                  <option value="">-- Select Size --</option>
                  @if(isset($sizes))
                    @foreach($sizes as $size)
                      <option value="{{ $size->id }}">{{ $size->name }}</option>
                    @endforeach
                  @endif
                </select>
              </div>
              <div class="col-md-2">
                <label>Specification</label>
                <select name="variants[0][specification_id]" class="form-control">
                  <option value="">-- Select Specification --</option>
                  @if(isset($specifications))
                    @foreach($specifications as $spec)
                      <option value="{{ $spec->id }}">{{ $spec->name }} - {{ $spec->value }}</option>
                    @endforeach
                  @endif
                </select>
              </div>
              <div class="col-md-2">
                <label>Price <span class="text-danger">*</span></label>
                <input type="number" name="variants[0][price]" placeholder="Variant Price" class="form-control" required>
              </div>
              <div class="col-md-1">
                <label>Stock <span class="text-danger">*</span></label>
                <input type="number" name="variants[0][stock]" placeholder="Stock" class="form-control" required>
              </div>
              <div class="col-md-2">
                <label>SKU</label>
                <input type="text" name="variants[0][sku]" placeholder="Variant SKU" class="form-control">
              </div>
              <div class="col-md-1">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-danger btn-sm remove-variation-row" style="display:none;">Remove</button>
              </div>
            </div>
          </div>
          <button type="button" id="add-variation-row" class="btn btn-primary btn-sm mt-2">Add Variation</button>
        </div>
        {{-- End Product Variations Section --}}

        <div class="form-group">
          <label for="brand_id">Brand</label>
          {{-- {{$brands}} --}}

          <select name="brand_id" class="form-control">
              <option value="">--Select Brand--</option>
             @foreach($brands as $brand)
              <option value="{{$brand->id}}">{{$brand->title}}</option>
             @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="condition">Condition</label>
          <select name="condition" class="form-control">
              <option value="">--Select Condition--</option>
              <option value="default">Default</option>
              <option value="new">New</option>
              <option value="hot">Hot</option>
          </select>
        </div>

        <div class="form-group">
          <label for="stock">Quantity <span class="text-danger">*</span></label>
          <input id="quantity" type="number" name="stock" min="0" placeholder="Enter quantity"  value="{{old('stock')}}" class="form-control">
          @error('stock')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group">
          <label for="inputPhoto" class="col-form-label">Photo <span class="text-danger">*</span></label>
          <div class="input-group">
              <span class="input-group-btn">
                  <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
                  <i class="fa fa-picture-o"></i> Choose
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
          <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
          <select name="status" class="form-control">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
          </select>
          @error('status')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group mb-3">
          <button type="reset" class="btn btn-warning">Reset</button>
           <button class="btn btn-success" type="submit">Submit</button>
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
        alert('Error uploading file: ' + data.message);
    });

    // Summernote configuration with explicit image upload settings
    const summernoteConfig = {
        placeholder: "Write detail description.....",
        tabsize: 2,
        height: 150,
        callbacks: {
        onImageUploadError: function(msg) {
            console.error('Image upload error:', msg);
            alert('Image upload failed: ' + msg);
        }
        }
    };

    $('#summary').summernote({ ...summernoteConfig, height: 100 });
    $('#description').summernote(summernoteConfig);

    // Product Variations Script
    let variationIndex = 1; // Start index for new rows

    $('#add-variation-row').on('click', function() {
        // Prepare options for dropdowns - this is a bit verbose here, ideally, this could be cleaner perhaps with a hidden template row
        let colorsOptions = '<option value="">-- Select Color --</option>';
        @if(isset($colors))
            @foreach($colors as $color)
                colorsOptions += `<option value="{{ $color->id }}">{{ $color->name }}</option>`;
            @endforeach
        @endif

        let sizesOptions = '<option value="">-- Select Size --</option>';
        @if(isset($sizes))
            @foreach($sizes as $size)
                sizesOptions += `<option value="{{ $size->id }}">{{ $size->name }}</option>`;
            @endforeach
        @endif

        let specificationsOptions = '<option value="">-- Select Specification --</option>';
        @if(isset($specifications))
            @foreach($specifications as $spec)
                specificationsOptions += `<option value="{{ $spec->id }}">{{ $spec->name }} - {{ $spec->value }}</option>`;
            @endforeach
        @endif

        const variationRowHtml = `
        <div class="variation-row row mb-2">
            <div class="col-md-2">
            <select name="variants[${variationIndex}][color_id]" class="form-control">
                ${colorsOptions}
            </select>
            </div>
            <div class="col-md-2">
            <select name="variants[${variationIndex}][size_id]" class="form-control">
                ${sizesOptions}
            </select>
            </div>
            <div class="col-md-2">
            <select name="variants[${variationIndex}][specification_id]" class="form-control">
                ${specificationsOptions}
            </select>
            </div>
            <div class="col-md-2">
            <input type="number" name="variants[${variationIndex}][price]" placeholder="Variant Price" class="form-control" required>
            </div>
            <div class="col-md-1">
            <input type="number" name="variants[${variationIndex}][stock]" placeholder="Stock" class="form-control" required>
            </div>
            <div class="col-md-2">
            <input type="text" name="variants[${variationIndex}][sku]" placeholder="Variant SKU" class="form-control">
            </div>
            <div class="col_md-1">
            <button type="button" class="btn btn-danger btn-sm remove-variation-row">Remove</button>
            </div>
        </div>`;
        $('#product-variations-container').append(variationRowHtml);
        variationIndex++;
        updateRemoveButtons();
    });

    $('#product-variations-container').on('click', '.remove-variation-row', function() {
        $(this).closest('.variation-row').remove();
        updateRemoveButtons();
    });

    function updateRemoveButtons() {
        if ($('#product-variations-container .variation-row').length > 1) {
        $('.remove-variation-row').show();
        } else {
        $('.remove-variation-row').first().hide(); // Hide remove button for the first row if it's the only one
        }
    }
    updateRemoveButtons(); // Initial check
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
          var html_option="<option value=''>----Select sub category----</option>"
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
@endpush