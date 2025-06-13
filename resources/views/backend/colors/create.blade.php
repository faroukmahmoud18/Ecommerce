@extends('backend.layouts.master')

@section('main-content')
<div class="card">
    <h5 class="card-header">Add Color</h5>
    <div class="card-body">
      <form method="post" action="{{ route('colors.store') }}">
        @csrf
        <div class="form-group">
          <label for="inputName" class="col-form-label">Name <span class="text-danger">*</span></label>
          <input id="inputName" type="text" name="name" placeholder="Enter color name"  value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror">
          @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
          @enderror
        </div>

        <div class="form-group">
          <label for="inputHexCode" class="col-form-label">Hex Code (e.g., #FF0000)</label>
          <input id="inputHexCode" type="text" name="hex_code" placeholder="Enter hex code"  value="{{ old('hex_code') }}" class="form-control @error('hex_code') is-invalid @enderror">
          @error('hex_code')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
          @enderror
        </div>

        <div class="form-group mb-3">
          <a href="{{ route('colors.index') }}" class="btn btn-warning">Cancel</a>
          <button class="btn btn-success" type="submit">Submit</button>
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
