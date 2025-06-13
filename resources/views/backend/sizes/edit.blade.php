@extends('backend.layouts.master')

@section('main-content')
<div class="card">
    <h5 class="card-header">Edit Size</h5>
    <div class="card-body">
      <form method="post" action="{{ route('sizes.update', $size->id) }}">
        @csrf
        @method('PATCH')
        <div class="form-group">
          <label for="inputName" class="col-form-label">Name <span class="text-danger">*</span></label>
          <input id="inputName" type="text" name="name" placeholder="Enter size name"  value="{{ old('name', $size->name) }}" class="form-control @error('name') is-invalid @enderror">
          @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
          @enderror
        </div>

        <div class="form-group mb-3">
          <a href="{{ route('sizes.index') }}" class="btn btn-warning">Cancel</a>
          <button class="btn btn-success" type="submit">Update</button>
        </div>
      </form>
    </div>
</div>
@endsection

@push('styles')
{{-- Add any specific styles for this page if needed --}}
@endpush

@push('scripts')
{{-- Add any specific scripts for this page if needed --}}
@endpush
