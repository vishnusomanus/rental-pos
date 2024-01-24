@extends('layouts.admin')

@section('title', 'Import Customer')
@section('content-header', 'Import Customer')

@section('content-actions')
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#instructionsModal">
        Instructions
    </button>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="card-body">
            <form action="{{ route('import.customers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="file">Select File:</label>
                    <input type="file" name="file" id="file" class="form-control" accept=".csv, .xlsx, .xls" required>
                </div>

                <button type="submit" class="btn btn-primary">Import</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="instructionsModal" tabindex="-1" role="dialog" aria-labelledby="instructionsModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="instructionsModalLabel">Instructions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Instructions on how to create the import file:</p>
                <ol>
                    <li>First Name: The First Name of the customer.</li>
                    <li>Last Name: The Last Name of the customer.</li>
                    <li>Email: The email of the customer.</li>
                    <li>Phone: The phone of the customer.</li>
                    <li>Address: The address of the customer.</li>
                </ol>
                
            </div>
            <div class="modal-footer">
                <a href="{{ asset('downloads/customers.csv') }}" class="btn btn-primary"
                   download>Download Sample File</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script>
    $(document).ready(function () {
        bsCustomFileInput.init();
    });
</script>
@endsection

