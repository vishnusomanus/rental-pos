@extends('layouts.admin')

@section('title', 'Order Details')
@section('content-header', 'Order Details')
@section('content-actions')
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="invoice p-3 mb-3">
        <div class="row">
            <div class="col-12">
            <h4>
                <i class="fas fa-globe"></i> POS <small class="float-right">Date: {{$order->created_at}}</small>
            </h4>
            </div>
        </div>
        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col"> From <address>
                <strong>{{config('settings.app_name')}}</strong>
                <br> 
            {{config('settings.app_address')}} <br/>
            +91 {{config('settings.mobile')}}
            </address>
            </div>
            <div class="col-sm-4 invoice-col"> To <address>
                <strong>{{ $order->customer->first_name }} {{ $order->customer->last_name }}</strong>
                <br> {{ $order->customer->address }} <br/>
            {{ $order->customer->phone }} <br/>
            </address>
            </div>
            <div class="col-sm-4 invoice-col">
            <b>Invoice #{{$order->id}}</b>
            <!-- <br>
            <br>
            <b>Order ID:</b> 4F3S8J <br>
            <b>Payment Due:</b> 2/22/2014 <br>
            <b>Account:</b> 968-34567 -->
            </div>
        </div>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-striped">
                <thead>
                    <tr>
                        <td>Item</td>
                        <td>Quantity</td>
                        <td>Days</td>
                        <td>Rate</td>
                        <td>Amount</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                        <tbody>
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->days }}</td>
                            <td style="font-family: DejaVu Sans;">{{config('settings.currency_symbol')}}{{ $item->product->price }}</td>
                            <td style="font-family: DejaVu Sans;">{{config('settings.currency_symbol')}}{{ $item->product->price * $item->quantity * $item->days}}</td>
                        </tr>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
           
            </div>
            <div class="col-6">
            <!-- <p class="lead">Amount Due 2/22/2014</p> -->
            <div class="table-responsive">
                <table class="table">
                <tr>
                <td>Subtotal:</td>
                <td style="font-family: 'DejaVu Sans';">{{ config('settings.currency_symbol') }}{{$order->formattedTotal()}}</td>
                </tr>
                <!-- <tr>
                    <td>Tax:</td>
                    <td style="font-family: 'DejaVu Sans';">{{ config('settings.currency_symbol') }}0</td>
                </tr>
                <tr>
                    <td>Discount:</td>
                    <td style="font-family: 'DejaVu Sans';">{{ config('settings.currency_symbol') }}0</td>
                </tr> -->
                <tr>
                    <td>Paid:</td>
                    <td style="font-family: 'DejaVu Sans';">{{ config('settings.currency_symbol') }}{{$order->formattedReceivedAmount()}}</td>
                </tr>
                <tr>
                    <td>Total:</td>
                    <td style="font-family: 'DejaVu Sans'; font-weight: bold; font-size: 18px;">{{ config('settings.currency_symbol') }}{{number_format($order->total() - $order->receivedAmount(), 2)}}</td>
                </tr>
                </table>
            </div>
            </div>
        </div>
        <div class="row no-print">
            <div class="col-12">
            <a href="{{$order->id}}/invoice" rel="noopener" target="_blank" class="btn btn-default">
                <i class="fas fa-print"></i> Print </a>
                @if($order->receivedAmount() == 0 || $order->receivedAmount() < $order->total())
            <a href="{{$order->id}}/edit" class="btn btn-success float-right">
                <i class="far fa-credit-card"></i> Submit Payment </a>
                @endif
            </div>
        </div>
        </div>
    </div>
</div>
@endsection

@section('js')

@endsection