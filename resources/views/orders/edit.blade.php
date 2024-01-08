@extends('layouts.admin')

@section('title', "Update Order")
@section('content-header', "Update Order")

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('orders.update', $order->id) }}" method="POST" >
                @csrf
                @method('PUT')

                <table class="table table-striped">
                <tbody>
                    <tr>
                        <td>Customer</td>
                        <td>{{ $order->customer->first_name }} {{ $order->customer->last_name }}</td>
                    </tr>
                    <tr>
                        <td>Products</td>
                        <td>
                            <table class="table table-striped">
                                <tr>
                                    <td>Name</td>
                                    <td>Quantity</td>
                                    <td>Days</td>
                                    <td>Price</td>
                                </tr>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $item->days }}</td>
                                        <td>{{config('settings.currency_symbol')}}{{ $item->product->price }}</td>
                                    </tr>
                                @endforeach
                            </table>
                            
                        </td>
                    </tr>
                    <tr>
                        <td>Total</td>
                        <td>
                            <strong class="btn-tool bg-green">
                            {{config('settings.currency_symbol')}}{{ $order->formattedTotal() }}
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <td>Received Amount</td>
                        <td>
                            <strong  class="btn-tool bg-orange text-white">
                            {{config('settings.currency_symbol')}}{{ $order->formattedReceivedAmount() }}
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <td>Balance</td>
                        <td>
                            <strong  class="btn-tool bg-red text-white">
                            {{config('settings.currency_symbol')}}{{ $order->balance() }}
                            </strong>
                        </td>
                    </tr>
                </tbody>
                </table>    

                <div class="form-group">
                    <label for="balance">Pay Balance Amount</label>
                    <input type="text" name="balance" class="form-control @error('balance') is-invalid @enderror"
                           id="balance"
                            value="{{ old('balance', $order->total() - $order->receivedAmount()) }}">
                    @error('first_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>




                <button class="btn btn-primary" type="submit">Update</button>
            </form>
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
