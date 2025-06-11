@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Edit Product</h5>
    <div class="card-body">
      <form method="post" action="{{route('product.update',$product->id)}}">
        @csrf 
        @method('PATCH')
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
          <input id="inputTitle" type="text" name="title" placeholder="Enter title"  value="{{$product->title}}" class="form-control">
          @error('title')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="summary" class="col-form-label">Summary <span class="text-danger">*</span></label>
          <textarea class="form-control" id="summary" name="summary">{{$product->summary}}</textarea>
          @error('summary')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="description" class="col-form-label">Description</label>
          <textarea class="form-control" id="description" name="description">{{$product->description}}</textarea>
          @error('description')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>


        <div class="form-group">
          <label for="is_featured">Is Featured</label><br>
          <input type="checkbox" name='is_featured' id='is_featured' value='{{$product->is_featured}}' {{(($product->is_featured) ? 'checked' : '')}}> Yes                        
        </div>
              {{-- {{$categories}} --}}

        <div class="form-group">
          <label for="cat_id">Category <span class="text-danger">*</span></label>
          <select name="cat_id" id="cat_id" class="form-control">
              <option value="">--Select any category--</option>
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
          <label for="child_cat_id">Sub Category</label>
          <select name="child_cat_id" id="child_cat_id" class="form-control">
              <option value="">--Select any sub category--</option>
              
          </select>
        </div>

        <div class="form-group">
          <label for="price" class="col-form-label">Price(NRS) <span class="text-danger">*</span></label>
          <input id="price" type="number" name="price" placeholder="Enter price"  value="{{$product->price}}" class="form-control">
          @error('price')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="discount" class="col-form-label">Discount(%)</label>
          <input id="discount" type="number" name="discount" min="0" max="100" placeholder="Enter discount"  value="{{$product->discount}}" class="form-control">
          @error('discount')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        {{-- Old Size field removed --}}

        {{-- Product Variations Section --}}
        <div class="form-group">
            <label class="col-form-label">Product Variations</label>
            <div id="product-variations-container">
                @php $variant_idx = 0; @endphp
                @if($product->variants && $product->variants->count() > 0)
                    @foreach($product->variants as $variant)
                        <div class="variation-row row mb-2">
                            <input type="hidden" name="variants[{{ $variant_idx }}][id]" value="{{ $variant->id }}">
                            <div class="col-md-2">
                                <label>Color</label>
                                <select name="variants[{{ $variant_idx }}][color_id]" class="form-control">
                                    <option value="">-- Select Color --</option>
                                    @foreach($colors as $color)
                                        <option value="{{ $color->id }}" {{ $variant->color_id == $color->id ? 'selected' : '' }}>{{ $color->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Size</label>
                                <select name="variants[{{ $variant_idx }}][size_id]" class="form-control">
                                    <option value="">-- Select Size --</option>
                                    @foreach($sizes as $size)
                                        <option value="{{ $size->id }}" {{ $variant->size_id == $size->id ? 'selected' : '' }}>{{ $size->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Specification</label>
                                <select name="variants[{{ $variant_idx }}][specification_id]" class="form-control">
                                    <option value="">-- Select Specification --</option>
                                    @foreach($specifications as $spec)
                                        <option value="{{ $spec->id }}" {{ $variant->specification_id == $spec->id ? 'selected' : '' }}>{{ $spec->name }} - {{ $spec->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Price <span class="text-danger">*</span></label>
                                <input type="number" name="variants[{{ $variant_idx }}][price]" placeholder="Variant Price" class="form-control" value="{{ $variant->price }}" required>
                            </div>
                            <div class="col-md-1">
                                <label>Stock <span class="text-danger">*</span></label>
                                <input type="number" name="variants[{{ $variant_idx }}][stock]" placeholder="Stock" class="form-control" value="{{ $variant->stock }}" required>
                            </div>
                            <div class="col-md-2">
                                <label>SKU</label>
                                <input type="text" name="variants[{{ $variant_idx }}][sku]" placeholder="Variant SKU" class="form-control" value="{{ $variant->sku }}">
                            </div>
                            <div class="col-md-1">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm remove-variation-row">Remove</button>
                            </div>
                        </div>
                        @php $variant_idx++; @endphp
                    @endforeach
                @else
                    {{-- Display one empty row if no variants exist --}}
                    <div class="variation-row row mb-2">
                        <div class="col-md-2">
                            <label>Color</label>
                            <select name="variants[0][color_id]" class="form-control">
                                <option value="">-- Select Color --</option>
                                @foreach($colors as $color)
                                    <option value="{{ $color->id }}">{{ $color->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Size</label>
                            <select name="variants[0][size_id]" class="form-control">
                                <option value="">-- Select Size --</option>
                                @foreach($sizes as $size)
                                    <option value="{{ $size->id }}">{{ $size->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Specification</label>
                            <select name="variants[0][specification_id]" class="form-control">
                                <option value="">-- Select Specification --</option>
                                @foreach($specifications as $spec)
                                    <option value="{{ $spec->id }}">{{ $spec->name }} - {{ $spec->value }}</option>
                                @endforeach
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
                    @php $variant_idx = 1; @endphp {{-- Next new row will be index 1 if no variants --}}
                @endif
            </div>
            <button type="button" id="add-variation-row" class="btn btn-primary btn-sm mt-2">Add New Variation</button>
        </div>
        {{-- End Product Variations Section --}}

        <div class="form-group">
          <label for="brand_id">Brand</label>
          <select name="brand_id" class="form-control">
              <option value="">--Select Brand--</option>
             @foreach($brands as $brand)
              <option value="{{$brand->id}}" {{(($product->brand_id==$brand->id)? 'selected':'')}}>{{$brand->title}}</option>
             @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="condition">Condition</label>
          <select name="condition" class="form-control">
              <option value="">--Select Condition--</option>
              <option value="default" {{(($product->condition=='default')? 'selected':'')}}>Default</option>
              <option value="new" {{(($product->condition=='new')? 'selected':'')}}>New</option>
              <option value="hot" {{(($product->condition=='hot')? 'selected':'')}}>Hot</option>
          </select>
        </div>

        <div class="form-group">
          <label for="stock">Quantity <span class="text-danger">*</span></label>
          <input id="quantity" type="number" name="stock" min="0" placeholder="Enter quantity"  value="{{$product->stock}}" class="form-control">
          @error('stock')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group">
          <label for="inputPhoto" class="col-form-label">Photo <span class="text-danger">*</span></label>
          <div class="input-group">
              <span class="input-group-btn">
                  <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
                  <i class="fas fa-image"></i> Choose
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
          <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
          <select name="status" class="form-control">
            <option value="active" {{(($product->status=='active')? 'selected' : '')}}>Active</option>
            <option value="inactive" {{(($product->status=='inactive')? 'selected' : '')}}>Inactive</option>
        </select>
          @error('status')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group mb-3">
           <button class="btn btn-success" type="submit">Update</button>
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
      placeholder: "Write short description.....",
        tabsize: 2,
        height: 150
    });
    });
    $(document).ready(function() {
      $('#description').summernote({
        placeholder: "Write detail Description.....",
          tabsize: 2,
          height: 150
      });
    });
</script>

<script>
    // Initialize variationIndex based on existing variants for adding new rows
    let variationIndex = {{ $product->variants->count() > 0 ? $product->variants->count() : 1 }};

    $(document).ready(function() {
        $('#lfm').filemanager('image');

        $('#summary').summernote({
            placeholder: "Write short description.....",
            tabsize: 2,
            height: 150
        });
        $('#description').summernote({
            placeholder: "Write detail Description.....",
            tabsize: 2,
            height: 150
        });

        // Product Variations Script (Adapted for edit view)
        $('#add-variation-row').on('click', function() {
            let colorsOptions = '<option value="">-- Select Color --</option>';
            @foreach($colors as $color)
                colorsOptions += `<option value="{{ $color->id }}">{{ $color->name }}</option>`;
            @endforeach

            let sizesOptions = '<option value="">-- Select Size --</option>';
            @foreach($sizes as $size)
                sizesOptions += `<option value="{{ $size->id }}">{{ $size->name }}</option>`;
            @endforeach

            let specificationsOptions = '<option value="">-- Select Specification --</option>';
            @foreach($specifications as $spec)
                specificationsOptions += `<option value="{{ $spec->id }}">{{ $spec->name }} - {{ $spec->value }}</option>`;
            @endforeach

            const variationRowHtml = `
            <div class="variation-row row mb-2">
                <input type="hidden" name="variants[${variationIndex}][id]" value=""> {{-- New variants have no ID initially --}}
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
                <div class="col-md-1">
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
            // Note: For existing items, removal is handled by backend by not receiving the variant ID.
            // If you want to mark for deletion instantly (e.g., add a hidden field like variants[index][delete]=1),
            // that would require more complex JS and backend logic.
        });

        function updateRemoveButtons() {
             const rows = $('#product-variations-container .variation-row');
            if (rows.length > 1) {
                rows.find('.remove-variation-row').show();
            } else if (rows.length === 1) {
                // Only hide if it's an "empty" row for a new product or if all existing were removed and one new one added
                // This logic might need refinement based on desired UX for "at least one variant"
                 if(rows.first().find('input[name$="[id]"]').val() === "") { // Check if it's a new row (no ID)
                    rows.first().find('.remove-variation-row').hide();
                 } else {
                    rows.first().find('.remove-variation-row').show();
                 }
            } else { // No rows
                // Nothing to do
            }
        }
        updateRemoveButtons(); // Initial check
    });

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
                        var html_option="<option value=''>--Select any one--</option>";
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