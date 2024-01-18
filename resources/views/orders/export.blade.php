<!DOCTYPE html>
<html>
<head>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>{{ __('order.Customer_Name') }}</th>
                <th>{{ __('order.Total') }}</th>
                <th>{{ __('order.Received_Amount') }}</th>
                {{-- <th>{{ __('order.Status') }}</th> --}}
                <th>{{ __('order.To_Pay') }}</th>
                <th>{{ __('order.Created_At') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->getCustomerName() }}</td>
                    <td style="font-family: 'DejaVu Sans';">{{ config('settings.currency_symbol') }} {{ $order->formattedTotal() }}</td>
                    <td style="font-family: 'DejaVu Sans';">{{ config('settings.currency_symbol') }} {{ $order->formattedReceivedAmount() }}</td>
                    {{-- <td>{{ $order->getStatus() }}</td> --}}
                    <td style="font-family: 'DejaVu Sans';">{{ config('settings.currency_symbol') }} {{ number_format($order->balance(), 2) }}</td>
                    <td>{{ $order->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th style="font-family: 'DejaVu Sans';">{{ config('settings.currency_symbol') }} {{ number_format($total, 2) }}</th>
                <th style="font-family: 'DejaVu Sans';">{{ config('settings.currency_symbol') }} {{ number_format($receivedAmount, 2) }}</th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
