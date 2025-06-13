@extends('backend.layouts.master')
@section('main-content')
<div class="card">
  <div class="row">
    <div class="col-md-12">
       @include('backend.layouts.notification')
    </div>
  </div>
  <h5 class="card-header">{{__('message.page_title_index')}}</h5>
  <div class="card-body">
    @if(count($messages)>0)
    <table class="table message-table" id="message-dataTable">
      <thead>
        <tr>
          <th scope="col">{{__('admin_common.table_header_id')}}</th>
          <th scope="col">{{__('message.table_header_name')}}</th>
          <th scope="col">{{__('message.table_header_subject')}}</th>
          <th scope="col">{{__('admin_common.table_header_date')}}</th>
          <th scope="col">{{__('admin_common.table_header_actions')}}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ( $messages as $message)

        <tr class="@if($message->read_at) border-left-success @else bg-light border-left-warning @endif">
          <td scope="row">{{$loop->index +1}}</td>
          <td>{{$message->name}} {{$message->read_at}}</td>
          <td>{{$message->subject}}</td>
          <td>{{$message->created_at->format('F d, Y h:i A')}}</td>
          <td>
            <a href="{{route('message.show',$message->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="{{__('admin_common.view_button_tooltip')}}" data-placement="bottom"><i class="fas fa-eye"></i></a>
            <form method="POST" action="{{route('message.destroy',[$message->id])}}">
              @csrf 
              @method('delete')
                  <button class="btn btn-danger btn-sm dltBtn" data-id={{$message->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="{{__('admin_common.delete_button_tooltip')}}"><i class="fas fa-trash-alt"></i></button>
            </form>
          </td>
        </tr>

        @endforeach
      </tbody>
    </table>
    <nav class="blog-pagination justify-content-center d-flex">
      {{$messages->links()}}
    </nav>
    @else
      <h2>{{__('message.no_messages_empty')}}</h2>
    @endif
  </div>
</div>
@endsection
@push('styles')
  <link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
  <style>
      div.dataTables_wrapper div.dataTables_paginate{
          display: none;
      }
      .zoom {
        transition: transform .2s; /* Animation */
      }

      .zoom:hover {
        transform: scale(3.2);
      }
  </style>
@endpush
@push('scripts')
  <script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="{{asset('backend/js/demo/datatables-demo.js')}}"></script>
  <script>
      
      $('#message-dataTable').DataTable( {
            "columnDefs":[
                {
                    "orderable":false,
                    "targets":[4]
                }
            ]
        } );

        // Sweet alert

        function deleteData(id){
            
        }
  </script>
  <script>
    $(document).ready(function(){
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
        $('.dltBtn').click(function(e){
          var form=$(this).closest('form');
            var dataID=$(this).data('id');
            // alert(dataID);
            e.preventDefault();
            swal({
                  title: "{{__('admin_common.sweetalert_title_are_you_sure')}}",
                  text: "{{__('admin_common.sweetalert_text_once_deleted')}}",
                  icon: "warning",
                  buttons: true,
                  dangerMode: true,
              })
              .then((willDelete) => {
                  if (willDelete) {
                    form.submit();
                  } else {
                      swal("{{__('admin_common.sweetalert_text_data_safe')}}");
                  }
              });
        })
    })
  </script>
@endpush