@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">{{__('specification.page_title_create')}}</h5>
    <div class="card-body">
      <form method="post" action="{{route('specification.store')}}">
        {{csrf_field()}}
        <div class="form-group">
          <label for="inputName" class="col-form-label">{{__('specification.form_label_name_required')}}</label>
          <input id="inputName" type="text" name="name" placeholder="{{__('admin_common.form_placeholder_name')}}"  value="{{old('name')}}" class="form-control">
          @error('name')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="inputValue" class="col-form-label">{{__('specification.form_label_value_required')}}</label>
          <input id="inputValue" type="text" name="value" placeholder="{{__('specification.form_placeholder_value')}}"  value="{{old('value')}}" class="form-control">
          @error('value')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="status" class="col-form-label">{{__('admin_common.form_label_status_required')}}</label>
          <select name="status" class="form-control">
              <option value="active" {{old('status') == 'active' ? 'selected' : ''}}>{{__('admin_common.status_active')}}</option>
              <option value="inactive" {{old('status') == 'inactive' ? 'selected' : ''}}>{{__('admin_common.status_inactive')}}</option>
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
