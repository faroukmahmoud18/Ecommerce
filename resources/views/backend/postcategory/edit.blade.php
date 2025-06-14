@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">{{__('post_category.page_title_edit')}}</h5>
    <div class="card-body">
      <form method="post" action="{{route('post-category.update',$postCategory->id)}}">
        @csrf 
        @method('PATCH')
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">{{__('admin_common.form_label_title')}}</label>
          <input id="inputTitle" type="text" name="title" placeholder="{{__('admin_common.form_placeholder_title')}}"  value="{{$postCategory->title}}" class="form-control">
          @error('title')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="status" class="col-form-label">{{__('admin_common.form_label_status')}}</label>
          <select name="status" class="form-control">
            <option value="active" {{(($postCategory->status=='active') ? 'selected' : '')}}>{{__('admin_common.status_active')}}</option>
            <option value="inactive" {{(($postCategory->status=='inactive') ? 'selected' : '')}}>{{__('admin_common.status_inactive')}}</option>
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
