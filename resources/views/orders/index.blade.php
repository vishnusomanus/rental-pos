@extends('layouts.admin')

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
        <div class="table-responsive">
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
                        <td><a target="_blank" href="{{ route('orders.invoice', $order->id) }}" class="btn btn-outline-dark btn-sm text-black"><i class="fas fa-print"></i></a></td>
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
    </div>
</div>
@endsection

