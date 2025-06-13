@extends('backend.layouts.master')

@section('title',__('review.page_title_edit'))

@section('main-content')
<div class="card">
  <h5 class="card-header">{{__('review.form_header_edit_review')}}</h5>
  <div class="card-body">
    <form action="{{route('review.update',$review->id)}}" method="POST">
      @csrf
      @method('PATCH')
      <div class="form-group">
        <label for="name">{{__('review.form_label_review_by')}}:</label>
        <input type="text" disabled class="form-control" value="{{$review->user_info->name}}">
      </div>
      <div class="form-group">
        <label for="review">{{__('review.form_label_review')}}</label>
      <textarea name="review" id="" cols="20" rows="10" class="form-control">{{$review->review}}</textarea>
      </div>
      <div class="form-group">
        <label for="status">{{__('review.form_label_status')}} :</label>
        <select name="status" id="" class="form-control">
          <option value="">{{__('admin_common.form_select_placeholder_status')}}</option>
          <option value="active" {{(($review->status=='active')? 'selected' : '')}}>{{__('admin_common.status_active')}}</option>
          <option value="inactive" {{(($review->status=='inactive')? 'selected' : '')}}>{{__('admin_common.status_inactive')}}</option>
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