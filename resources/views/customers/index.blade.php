@extends('layouts.admin')

@section('title', __('customer.Customer_List'))
@section('content-header', __('customer.Customer_List'))
@section('content-actions')
<a href="{{route('customers.create')}}" class="btn btn-primary">{{ __('customer.Add_Customer') }}</a>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
<div class="card">
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-md-7">
                <form class="form-inline" action="{{ route('customers.index') }}">
                    <div class="input-group input-group-md">
                        <input class="form-control form-control-navbar" type="search" name="search" placeholder="Search" aria-label="Search" value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <a href="{{ route('customers.export.csv') }}" class="btn btn-sm btn-secondary mt-2">CSV Export</a>
                    <a href="{{ route('customers.export.pdf') }}" class="btn btn-sm btn-secondary mt-2 ml-2" target="_blank">PDF Export</a>
                </form>
            </div>
        </div>
        <div class="table-responsive swipe-container">
            <div class="swipe-overlay"><i class="fas fa-chevron-left"></i> Swipe here <i class="fas fa-chevron-right"></i></div>
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('customer.Avatar') }}</th>
                        <th>{{ __('customer.First_Name') }}</th>
                        <th>{{ __('customer.Last_Name') }}</th>
                        <th>{{ __('customer.Email') }}</th>
                        <th>{{ __('customer.Phone') }}</th>
                        <th>{{ __('customer.Address') }}</th>
                        <th>{{ __('common.Created_At') }}</th>
                        <th>{{ __('customer.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                    <tr>
                        <td>
                            <img width="50" src="{{$customer->getAvatarUrl()}}" alt="">
                        </td>
                        <td>{{$customer->first_name}}</td>
                        <td>{{$customer->last_name}}</td>
                        <td>{{$customer->email}}</td>
                        <td>{{$customer->phone}}</td>
                        <td>{{$customer->address}}</td>
                        <td>{{$customer->created_at}}</td>
                        <td style="width: 100px;">
                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                            <button class="btn btn-danger btn-delete btn-sm" data-url="{{route('customers.destroy', $customer)}}"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $customers->render() }}
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script type="module">
    $(document).ready(function() {
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
        })
    })
    var container = $('.table-responsive');
    if (container.length > 0 && container.get(0).scrollWidth > container.innerWidth()) {
        container.scroll(function() {
            var scrollLeft = $(this).scrollLeft();
            if (scrollLeft > 0) {
                $('.swipe-overlay').hide();
            }
        });
    } else {
        $('.swipe-overlay').hide();
    }
</script>
@endsection
