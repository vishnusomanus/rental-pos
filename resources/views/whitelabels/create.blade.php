@extends('layouts.admin')

@section('title', 'Create White Label')
@section('content-header', 'Create White Label')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('white-labels.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="domain">Domain</label>
                <input type="text" id="domain" name="domain" value="{{ old('domain') }}" class="form-control @error('domain') is-invalid @enderror">
                @error('domain')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="url">URL</label>
                <input type="text" id="url" name="url" value="{{ old('url') }}" class="form-control @error('url') is-invalid @enderror">
                @error('url')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route('white-labels.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
