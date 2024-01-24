@extends('layouts.admin')

@section('title', 'Edit White Label')
@section('content-header', 'Edit White Label')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('white-labels.update', $whiteLabel) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="domain">Domain</label>
                <input type="text" id="domain" name="domain" value="{{ old('domain', $whiteLabel->domain) }}" class="form-control @error('domain') is-invalid @enderror">
                @error('domain')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $whiteLabel->description) }}</textarea>
                @error('description')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="url">URL</label>
                <input type="text" id="url" name="url" value="{{ old('url', $whiteLabel->url) }}" class="form-control @error('url') is-invalid @enderror">
                @error('url')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('white-labels.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
