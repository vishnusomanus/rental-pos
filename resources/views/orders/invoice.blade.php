<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
{{$order}}
<div class=" invoice">
    <div class="header">
        <div class="logo">
            <img src="data:image/png;base64,{{ $base64Image }}" width="60">
            <h3>POS</h3>
            <div class="invo">INVOICE</div>
        </div>
        <div class="order_data">
            <strong>Invoice Number:</strong> 123 <br/>
            <strong>Date:</strong> 123 <br/>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="address_area">
        <div class="address_each">
            <strong>Bill From:</strong><br/>
            Company Name <br/>
            Company address,  Company Name <br/>
            +91 9567774130
        </div>
        <div class="address_each">
            <strong>Bill To:</strong><br/>
            {{ $order->customer->first_name }} {{ $order->customer->last_name }} <br/>
            {{ $order->customer->address }} <br/>
            {{ $order->customer->phone }} <br/>
        </div>
    </div>
    <div class="inventorys">
        <table>
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
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->days }}</td>
                        <td>{{config('settings.currency_symbol')}}{{ $item->product->price }}</td>
                        <td>{{config('settings.currency_symbol')}}{{ $item->product->price * $item->quantity * $item->days}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<h1>Invoice for Order ID</h1>
<style>
    .logo h3, .logo img {
        display: inline-block;
        vertical-align: middle;
    }
    .logo img {
        margin-top: 5px;
        margin-right: 5px;
    }
    .logo {
        position: relative;
        float: left;
        width: 100%;
        border-top: 3px solid #607D8B;
        padding-top: 10px;
    }
    .invo {
        position: absolute;
        right: 0;
        top: 0;
        font-size: 40px;
        max-height: 45px;
        background: #607D8B;
        color: #fff;
        padding: 2px 15px;
        border-left: 5px solid #fff;
        margin: 20px 0;
    }
    .invo::after {
        content: '';
        height: 53px;
        width: 15px;
        background: #607d8b;
        display: block;
        position: absolute;
        left: -17px;
        top: 0;
    }
    .invo::before {
        content: '';
        height: 53px;
        width: 5px;
        background: #607d8b;
        display: block;
        position: absolute;
        left: -24px;
        top: 0;
    }
    .invoice {
        font-family: 'Roboto', sans-serif;
    }
    .address_area, {
        display: block;
        border-top: 3px solid #607D8B;
        margin-top: 20px;
    }
    .inventorys {
        display: block;
        border-top: 3px solid #607D8B;
    }
    .address_each {
        width: 49%;
        display: inline-block;
        margin-top: 25px;
    }
    .clearfix{
        display: block;
        width: 100%;
    }
    table {
        width: 100%;
    }
    thead {
        background: #b5c4cb;
    }
</style>