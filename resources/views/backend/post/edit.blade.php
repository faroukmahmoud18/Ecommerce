@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">{{__('post.page_title_edit')}}</h5>
    <div class="card-body">
      <form method="post" action="{{route('post.update',$post->id)}}">
        @csrf 
        @method('PATCH')
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">{{__('post.form_label_title_required')}}</label>
          <input id="inputTitle" type="text" name="title" placeholder="{{__('admin_common.form_placeholder_title')}}"  value="{{$post->title}}" class="form-control">
          @error('title')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="quote" class="col-form-label">{{__('post.form_label_quote')}}</label>
          <textarea class="form-control" id="quote" name="quote">{{$post->quote}}</textarea>
          @error('quote')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="summary" class="col-form-label">{{__('post.form_label_summary_required')}}</label>
          <textarea class="form-control" id="summary" name="summary">{{$post->summary}}</textarea>
          @error('summary')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="description" class="col-form-label">{{__('post.form_label_description')}}</label>
          <textarea class="form-control" id="description" name="description">{{$post->description}}</textarea>
          @error('description')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="post_cat_id">{{__('post.form_label_category_required')}}</label>
          <select name="post_cat_id" class="form-control">
              <option value="">{{__('post.form_select_placeholder_category')}}</option>
              @foreach($categories as $key=>$data)
                  <option value='{{$data->id}}' {{(($data->id==$post->post_cat_id)? 'selected' : '')}}>{{$data->title}}</option>
              @endforeach
          </select>
        </div>
        {{-- {{$post->tags}} --}}
        @php 
                $post_tags=explode(',',$post->tags);
                // dd($tags);
              @endphp
        <div class="form-group">
          <label for="tags">{{__('post.form_label_tags')}}</label>
          <select name="tags[]" multiple  data-live-search="true" class="form-control selectpicker">
              <option value="">{{__('post.form_select_placeholder_tag')}}</option>
              @foreach($tags as $key=>$data)
              
              <option value="{{$data->title}}"  {{(( in_array( "$data->title",$post_tags ) ) ? 'selected' : '')}}>{{$data->title}}</option>
              @endforeach
          </select>
        </div>
        <div class="form-group">
          <label for="added_by">{{__('post.form_label_author')}}</label>
          <select name="added_by" class="form-control">
              <option value="">{{__('product.form_select_placeholder_any_one')}}</option>
              @foreach($users as $key=>$data)
                <option value='{{$data->id}}' {{(($post->added_by==$data->id)? 'selected' : '')}}>{{$data->name}}</option>
              @endforeach
          </select>
        </div>
        <div class="form-group">
          <label for="inputPhoto" class="col-form-label">{{__('post.form_label_photo_required')}}</label>
          <div class="input-group">
              <span class="input-group-btn">
                  <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
                  <i class="fa fa-picture-o"></i> {{__('admin_common.button_choose')}}
                  </a>
              </span>
          <input id="thumbnail" class="form-control" type="text" name="photo" value="{{$post->photo}}">
        </div>
        <div id="holder" style="margin-top:15px;max-height:100px;"></div>

          @error('photo')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        
        <div class="form-group">
          <label for="status" class="col-form-label">{{__('admin_common.form_label_status_required')}}</label>
          <select name="status" class="form-control">
            <option value="active" {{(($post->status=='active')? 'selected' : '')}}>{{__('admin_common.status_active')}}</option>
            <option value="inactive" {{(($post->status=='inactive')? 'selected' : '')}}>{{__('admin_common.status_inactive')}}</option>
        </select>
          @error('status')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group mb-3">
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
      $('#quote').summernote({
        placeholder: "{{__('post.form_placeholder_quote_short')}}",
          tabsize: 2,
          height: 100
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
@endpush