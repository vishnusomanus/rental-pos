@extends('layouts.admin')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">

@section('title', __('order.Orders_List'))
@section('content-header', __('order.Orders_List'))
@section('content-actions')
    <a href="{{route('cart.index')}}" class="btn btn-primary">{{ __('cart.title') }}</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-md-7">
                <form class="form-inline" action="{{ route('orders.index') }}">
                    <div class="input-group input-group-md">
                        <input class="form-control form-control-navbar" type="search" name="search" placeholder="Search" aria-label="Search" value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <a href="{{ route('orders.export.csv') }}" class="btn btn-sm btn-secondary mt-2">CSV Export</a>
                    <a href="{{ route('orders.export.pdf') }}" class="btn btn-sm btn-secondary mt-2 ml-2" target="_blank">PDF Export</a>
                </form>
            </div>
            <div class="col-md-5">
                <form action="{{route('orders.index')}}">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="date" name="start_date" class="form-control" value="{{request('start_date')}}" />
                        </div>
                        <div class="col-md-5">
                            <input type="date" name="end_date" class="form-control" value="{{request('end_date')}}" />
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary" type="submit">{{ __('order.submit') }}</button>
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
                        <th>{{ __('order.Customer_Name') }}</th>
                        <th>{{ __('order.Total') }}</th>
                        <th>{{ __('order.Received_Amount') }}</th>
                        <th>{{ __('order.Status') }}</th>
                        <th>{{ __('order.To_Pay') }}</th>
                        <th>{{ __('order.Created_At') }}</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                    <tr>
                        <td>{{$order->getCustomerName()}}</td>
                        <td>{{ config('settings.currency_symbol') }} {{$order->formattedTotal()}}</td>
                        <td>{{ config('settings.currency_symbol') }} {{$order->formattedReceivedAmount()}}</td>
                        <td>
                            @if($order->receivedAmount() == 0)
                                <span class="badge badge-danger">{{ __('order.Not_Paid') }}</span>
                            @elseif($order->receivedAmount() < $order->total())
                                <span class="badge badge-warning">{{ __('order.Partial') }}</span>
                            @elseif($order->receivedAmount() == $order->total())
                                <span class="badge badge-success">{{ __('order.Paid') }}</span>
                            @elseif($order->receivedAmount() > $order->total())
                                <span class="badge badge-info">{{ __('order.Change') }}</span>
                            @endif
                        </td>
                        <td>{{config('settings.currency_symbol')}} {{number_format($order->total() - $order->receivedAmount(), 2)}}</td>
                        <td>{{$order->created_at}}</td>
                        <td>
                            <a target="_blank" href="{{ route('orders.invoice', $order->id) }}" class="btn btn-outline-dark btn-sm text-black"><i class="fas fa-print"></i></a>
                            <button class="btn btn-outline-dark btn-sm text-black" data-toggle="modal" data-target="#orderImagesModal{{$order->id}}">
                                <i class="fas fa-image"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>{{ config('settings.currency_symbol') }} {{ number_format($total, 2) }}</th>
                        <th>{{ config('settings.currency_symbol') }} {{ number_format($receivedAmount, 2) }}</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        {{ $orders->render() }}
        @foreach ($orders as $order)
            <!-- Modal for order images -->
            <div class="modal fade" id="orderImagesModal{{$order->id}}" tabindex="-1" role="dialog" aria-labelledby="orderImagesModalLabel{{$order->id}}" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="orderImagesModalLabel{{$order->id}}">Order Images</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                @foreach($order->orderImages as $image)
                                    <div class="col-md-3 col-sm-6 col-xs-6 mb-3">
                                        <a href="{{ $image->image }}" data-fancybox="gallery{{$order->id}}">
                                            <img src="{{ $image->image }}" alt="{{ $image->description }}" class="img-fluid">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
    <script >


        $(document).ready(function() {
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
            jQuery('[data-fancybox]').fancybox({
                buttons: [
                    'zoom',
                    'slideShow',
                    'fullScreen',
                    'thumbs',
                    'close'
                ],
                loop: true
            });
        });
    </script>
@endsection