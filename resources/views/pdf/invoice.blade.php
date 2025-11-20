<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        .table th, .table td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        .total { text-align: right; font-weight: bold; }
    </style>
</head>
<body>

<h2>Invoice #{{ $order->id }}</h2>

<p><strong>Customer:</strong> {{ $order->user->name }}</p>
<p><strong>Email:</strong> {{ $order->user->email }}</p>
<p><strong>Date:</strong> {{ $order->created_at->format('d M Y') }}</p>

<h3>Order Items</h3>

<table class="table">
    <thead>
    <tr>
        <th>Variant</th>
        <th>SKU</th>
        <th>Price</th>
        <th>Qty</th>
        <th>Subtotal</th>
    </tr>
    </thead>

    <tbody>
    @foreach($order->items as $item)
        <tr>
            <td>{{ $item->variant->name }}</td>
            <td>{{ $item->variant->sku }}</td>
            <td>{{ number_format($item->unit_price, 2) }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<h3 class="total">Total: ${{ number_format($order->total, 2) }}</h3>

</body>
</html>
