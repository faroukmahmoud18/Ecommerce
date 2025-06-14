@extends('backend.layouts.master')
@section('main-content')
<div class="card">
  <h5 class="card-header">{{__('message.page_title_show')}}</h5>
  <div class="card-body">
    @if($message)
        @if($message->photo)
        <img src="{{$message->photo}}" class="rounded-circle " style="margin-left:44%;">
        @else 
        <img src="{{asset('backend/img/avatar.png')}}" class="rounded-circle " style="margin-left:44%;">
        @endif
        <div class="py-4">{{__('message.label_from')}} <br>
           {{__('message.label_name_colon')}}{{$message->name}}<br>
           {{__('message.label_email_colon')}}{{$message->email}}<br>
           {{__('message.label_phone_colon')}}{{$message->phone}}
        </div>
        <hr/>
  <h5 class="text-center" style="text-decoration:underline"><strong>{{__('message.label_subject_colon')}}</strong> {{$message->subject}}</h5>
        <p class="py-5">{{$message->message}}</p>

    @endif

  </div>
</div>
@endsection