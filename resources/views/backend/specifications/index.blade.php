@extends('backend.layouts.master')

@section('main-content')
<div class="card">
  <h5 class="card-header">Specifications</h5>
  <div class="card-body">
    <div class="row">
      <div class="col-md-12 mb-3 text-right">
        <a href="{{ route('specifications.create') }}" class="btn btn-primary">Add New Specification</a>
      </div>
    </div>
    @if(session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">
        {{ session('error') }}
      </div>
    @endif
    <div class="table-responsive">
      <table class="table table-bordered" id="specifications-dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Value</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($specifications as $specification)
            <tr>
              <td>{{ $specification->id }}</td>
              <td>{{ $specification->name }}</td>
              <td>{{ $specification->value }}</td>
              <td>
                <a href="{{ route('specifications.edit', $specification->id) }}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                <form method="POST" action="{{ route('specifications.destroy', $specification->id) }}">
                  @csrf
                  @method('delete')
                  <button class="btn btn-danger btn-sm dltBtn" data-id="{{$specification->id}}" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center">No specifications found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      {{-- Pagination --}}
      <div class="d-flex justify-content-center">
          {{ $specifications->links('vendor.pagination.bootstrap-4') }}
      </div>
    </div>
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
  </style>
@endpush

@push('scripts')
  <!-- Page level plugins -->
  <script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="{{asset('backend/js/demo/datatables-demo.js')}}"></script>
  <script>
      $('#specifications-dataTable').DataTable();

        // Sweet alert
        function deleteData(id){
            // Specific logic for deleting data can be handled here if needed
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
          e.preventDefault();
          swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this data!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                   form.submit();
                } else {
                    swal("Your data is safe!");
                }
            });
        })
      })
  </script>
@endpush
