@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">{{__('post_tag.page_title_create')}}</h5>
    <div class="card-body">
      <form method="post" action="{{route('post-tag.store')}}">
        {{csrf_field()}}
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">{{__('admin_common.form_label_title')}}</label>
          <input id="inputTitle" type="text" name="title" placeholder="{{__('admin_common.form_placeholder_title')}}"  value="{{old('title')}}" class="form-control">
          @error('title')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="status" class="col-form-label">{{__('admin_common.form_label_status')}}</label>
          <select name="status" class="form-control">
              <option value="active">{{__('admin_common.status_active')}}</option>
              <option value="inactive">{{__('admin_common.status_inactive')}}</option>
          </select>
          @error('status')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group mb-3">
          <button type="reset" class="btn btn-warning">{{__('admin_common.button_reset')}}</button>
           <button class="btn btn-success" type="submit">{{__('admin_common.button_submit')}}</button>
        </div>
      </form>
    </div>
</div>

@endsection
