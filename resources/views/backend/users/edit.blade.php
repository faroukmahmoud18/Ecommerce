@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">{{__('user.page_title_edit')}}</h5>
    <div class="card-body">
      <form method="post" action="{{route('users.update',$user->id)}}">
        @csrf 
        @method('PATCH')
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">{{__('user.form_label_name')}}</label>
        <input id="inputTitle" type="text" name="name" placeholder="{{__('user.form_placeholder_name')}}"  value="{{$user->name}}" class="form-control">
        @error('name')
        <span class="text-danger">{{$message}}</span>
        @enderror
        </div>

        <div class="form-group">
            <label for="inputEmail" class="col-form-label">{{__('user.form_label_email')}}</label>
          <input id="inputEmail" type="email" name="email" placeholder="{{__('user.form_placeholder_email')}}"  value="{{$user->email}}" class="form-control">
          @error('email')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        {{-- <div class="form-group">
            <label for="inputPassword" class="col-form-label">Password</label>
          <input id="inputPassword" type="password" name="password" placeholder="Enter password"  value="{{$user->password}}" class="form-control">
          @error('password')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div> --}}

        <div class="form-group">
        <label for="inputPhoto" class="col-form-label">{{__('user.form_label_photo')}}</label>
        <div class="input-group">
            <span class="input-group-btn">
                <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
                <i class="fa fa-picture-o"></i> {{__('admin_common.button_choose')}}
                </a>
            </span>
            <input id="thumbnail" class="form-control" type="text" name="photo" value="{{$user->photo}}">
        </div>
        <img id="holder" style="margin-top:15px;max-height:100px;">
          @error('photo')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        @php 
        $roles=DB::table('users')->select('role')->where('id',$user->id)->get();
        // dd($roles);
        @endphp
        <div class="form-group">
            <label for="role" class="col-form-label">{{__('user.form_label_role')}}</label>
            <select name="role" class="form-control">
                <option value="">{{__('user.form_select_placeholder_role')}}</option>
                {{-- Note: The original loop logic for options was a bit flawed if $roles contained multiple entries or different values.
                     Assuming 'admin' and 'user' are the primary static role options to display.
                     If roles are fully dynamic, this part should ideally populate from a roles table.
                     For localization, we'll assume 'Admin' and 'User' are the texts to translate. --}}
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>{{__('user.form_option_role_admin')}}</option>
                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>{{__('user.form_option_role_user')}}</option>
            </select>
          @error('role')
          <span class="text-danger">{{$message}}</span>
          @enderror
          </div>
          <div class="form-group">
            <label for="status" class="col-form-label">{{__('admin_common.form_label_status')}}</label>
            <select name="status" class="form-control">
                <option value="active" {{(($user->status=='active') ? 'selected' : '')}}>{{__('admin_common.status_active')}}</option>
                <option value="inactive" {{(($user->status=='inactive') ? 'selected' : '')}}>{{__('admin_common.status_inactive')}}</option>
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

@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script>
    $('#lfm').filemanager('image');
</script>
@endpush