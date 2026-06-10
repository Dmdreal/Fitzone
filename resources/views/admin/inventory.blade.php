@extends('layouts.app')

@section('title', 'Inventory - Fitzone')

@section('content')
<h1>Inventory</h1>

<section class="card" style="margin-bottom:16px">
    <h2>Add Product</h2>
    <form method="POST" action="{{ route('admin.inventory.store') }}" class="form-grid">
        @csrf
        <label>Name <input name="name" required></label>
        <label>Category <input name="category" value="drink" required></label>
        <label>Price <input type="number" name="price" min="0" step="0.01" required></label>
        <label>Stock <input type="number" name="stock_quantity" min="0" value="0" required></label>
        <label>Low Stock Alert <input type="number" name="low_stock_threshold" min="0" value="5" required></label>
        <label style="align-self:end"><button class="btn" type="submit">Add Product</button></label>
    </form>
</section>

<section class="card" style="margin-bottom:16px">
    <h2>Products</h2>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Restock</th></tr></thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ ucfirst($product->category) }}</td>
                        <td>KES {{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->stock_quantity }}</td>
                        <td><span class="badge {{ $product->stock_status === 'ok' ? 'green' : ($product->stock_status === 'low' ? 'amber' : 'red') }}">{{ ucfirst($product->stock_status) }}</span></td>
                        <td>
                            <form method="POST" action="{{ route('admin.inventory.restock', $product) }}" style="display:flex;gap:8px;min-width:180px">
                                @csrf
                                <input type="number" name="quantity" min="1" value="10" required>
                                <button class="btn ghost" type="submit">Add</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">No products yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="card">
    <h2>Inventory Logs</h2>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Product</th><th>Type</th><th>Change</th><th>After</th><th>User</th><th>Notes</th></tr></thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td>{{ $log->product->name }}</td>
                        <td>{{ ucfirst($log->type) }}</td>
                        <td>{{ $log->quantity_change }}</td>
                        <td>{{ $log->stock_after }}</td>
                        <td>{{ $log->user?->name ?? '-' }}</td>
                        <td>{{ $log->notes }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6">No inventory logs yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
