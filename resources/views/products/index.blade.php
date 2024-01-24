@extends('layouts.admin')

@section('title', __('product.Product_List'))
@section('content-header', __('product.Product_List'))
@section('content-actions')
<a href="{{route('products.create')}}" class="btn btn-primary">{{ __('product.Create_Product') }}</a>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
<div class="card product-list">
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-md-7">
                <form class="form-inline" action="{{ route('products.index') }}">
                    <div class="input-group input-group-md">
                        <input class="form-control form-control-navbar" type="search" name="search" placeholder="Search" aria-label="Search" value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <a href="{{ route('products.export.csv') }}" class="btn btn-sm btn-secondary mt-2">CSV Export</a>
                    <a href="{{ route('products.export.pdf') }}" class="btn btn-sm btn-secondary mt-2 ml-2" target="_blank">PDF Export</a>
                    <a href="{{ route('products.export.qr') }}" class="btn btn-sm btn-secondary mt-2 ml-2" target="_blank">QR code Export</a>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('product.Name') }}</th>
                        <th>{{ __('product.Image') }}</th>
                        <th>{{ __('product.Barcode') }}</th>
                        <th>{{ __('product.Price') }}</th>
                        <th>{{ __('product.Quantity') }}</th>
                        <th>{{ __('product.Status') }}</th>
                        <th>{{ __('product.Created_At') }}</th>
                        <th>{{ __('product.Updated_At') }}</th>
                        <th>{{ __('product.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                    <tr>
                        <td>{{$product->name}}</td>
                        <td><img class="product-img" src="{{ Storage::url($product->image) }}" alt=""></td>
                        <td>{{$product->barcode}}</td>
                        <td>{{$product->price}}</td>
                        <td>{{$product->quantity}}</td>
                        <td>
                            <span class="right badge badge-{{ $product->status ? 'success' : 'danger' }}">{{$product->status ? __('common.Active') : __('common.Inactive') }}</span>
                        </td>
                        <td>{{$product->created_at}}</td>
                        <td>{{$product->updated_at}}</td>
                        <td>
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                            <button class="btn btn-danger btn-delete" data-url="{{route('products.destroy', $product)}}"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $products->render() }}
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
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: {{ __('product.sure') }},
                text: {{ __('product.really_delete') }},
                icon: {{ __('product.Create_Product') }}'warning',
                showCancelButton: true,
                confirmButtonText: {{ __('product.yes_delete') }},
                cancelButtonText: {{ __('product.No') }},
                reverseButtons: true
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
</script>
@endsection
