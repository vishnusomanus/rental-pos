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
                <th>{{ __('product.Name') }}</th>
                <th>{{ __('product.Image') }}</th>
                <th>{{ __('product.Barcode') }}</th>
                <th>{{ __('product.Price') }}</th>
                <th>{{ __('product.Quantity') }}</th>
                <th>{{ __('product.Status') }}</th>
                <th>{{ __('product.Created_At') }}</th>
                <th>{{ __('product.Updated_At') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td><img class="product-img" src="{{ Storage::url($product->image) }}" alt=""></td>
                    <td>{{ $product->barcode }}</td>
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td>{{ $product->status ? __('common.Active') : __('common.Inactive') }}</td>
                    <td>{{ $product->created_at }}</td>
                    <td>{{ $product->updated_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
