@extends('layouts.admin')

@section('title', 'White Labels')
@section('content-header', 'White Labels')
@section('content-actions')
<a href="{{ route('white-labels.create') }}" class="btn btn-primary">Add White Label</a>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-md-7">
                <form class="form-inline" action="{{ route('white-labels.index') }}">
                    <div class="input-group input-group-md">
                        <input class="form-control form-control-navbar" type="search" name="search" placeholder="Search" aria-label="Search" value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive swipe-container">
            <div class="swipe-overlay"><i class="fas fa-chevron-left"></i> Swipe here <i class="fas fa-chevron-right"></i></div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Domain</th>
                        <th>Description</th>
                        <th>URL</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($whiteLabels as $whiteLabel)
                    <tr>
                        <td>{{ $whiteLabel->domain }}</td>
                        <td>{{ $whiteLabel->description }}</td>
                        <td>{{ $whiteLabel->url }}</td>
                        <td>{{ $whiteLabel->created_at }}</td>
                        
                        <td>
                            <a href="{{ route('white-labels.edit', $whiteLabel) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                            <button class="btn btn-danger btn-delete btn-sm" data-url="{{ route('white-labels.destroy', $whiteLabel) }}"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $whiteLabels->render() }}
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script type="module">
    $(document).on('click', '.btn-delete', function() {
        var $this = $(this);

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value) {
                $.post($this.data('url'), {
                    _method: 'DELETE',
                    _token: '{{csrf_token()}}'
                }, function(res) {
                    $this.closest('tr').fadeOut(500, function() {
                        $(this).remove();
                    })
                })
            }
        })
    });
    
    $('.table-responsive').scroll(function() {
        var scrollLeft = $(this).scrollLeft();
        if (scrollLeft > 0) {
            $('.swipe-overlay').hide();
        }
    });
</script>
@endsection
