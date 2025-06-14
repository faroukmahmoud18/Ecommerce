@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">{{__('color.page_title_edit')}}</h5>
    <div class="card-body">
      <form method="post" action="{{route('color.update',$color->id)}}">
        @csrf
        @method('PATCH')
        <div class="form-group">
          <label for="inputName" class="col-form-label">{{__('color.form_label_name_required')}}</label>
          <input id="inputName" type="text" name="name" placeholder="{{__('admin_common.form_placeholder_name')}}"  value="{{$color->name}}" class="form-control">
          @error('name')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="inputHexCode" class="col-form-label">{{__('color.form_label_hex_code')}}</label>
          <input id="inputHexCode" type="text" name="hex_code" placeholder="{{__('color.form_placeholder_hex_code', ['example' => '#RRGGBB or #RGB'])}}"  value="{{$color->hex_code}}" class="form-control">
          @error('hex_code')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="status" class="col-form-label">{{__('admin_common.form_label_status_required')}}</label>
          <select name="status" class="form-control">
              <option value="active" {{(($color->status=='active')? 'selected' : '')}}>{{__('admin_common.status_active')}}</option>
              <option value="inactive" {{(($color->status=='inactive')? 'selected' : '')}}>{{__('admin_common.status_inactive')}}</option>
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
