@extends('backend.layouts.master')

@section('title',__('comment.page_title_edit'))

@section('main-content')
<div class="card">
  <h5 class="card-header">{{__('comment.form_header_edit_comment')}}</h5>
  <div class="card-body">
    <form action="{{route('comment.update',$comment->id)}}" method="POST">
      @csrf
      @method('PATCH')
      <div class="form-group">
        <label for="name">{{__('comment.form_label_by_colon')}}</label>
        <input type="text" disabled class="form-control" value="{{$comment->user_info->name}}">
      </div>
      <div class="form-group">
        <label for="comment">{{__('comment.form_label_comment')}}</label>
      <textarea name="comment" id="" cols="20" rows="10" class="form-control">{{$comment->comment}}</textarea>
      </div>
      <div class="form-group">
        <label for="status">{{__('admin_common.form_label_status')}} :</label>
        <select name="status" id="" class="form-control">
          <option value="">{{__('admin_common.form_select_placeholder_status')}}</option>
          <option value="active" {{(($comment->status=='active')? 'selected' : '')}}>{{__('admin_common.status_active')}}</option>
          <option value="inactive" {{(($comment->status=='inactive')? 'selected' : '')}}>{{__('admin_common.status_inactive')}}</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">{{__('admin_common.button_update')}}</button>
    </form>
  </div>
</div>
@endsection

@push('styles')
<style>
    .order-info,.shipping-info{
        background:#ECECEC;
        padding:20px;
    }
    .order-info h4,.shipping-info h4{
        text-decoration: underline;
    }
</style>
@endpush