<?php

namespace App\Http\Controllers;

use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        return view('admin.inventory', [
            'products' => Product::orderBy('category')->orderBy('name')->get(),
            'logs' => InventoryLog::with(['product', 'user'])->latest()->take(20)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:80'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
        ]);

        Product::create($data + ['is_active' => true]);

        return back()->with('status', 'Product added.');
    }

    public function restock(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
        ]);

        $product->increment('stock_quantity', $data['quantity']);
        $product->refresh();

        InventoryLog::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'type' => 'restock',
            'quantity_change' => $data['quantity'],
            'stock_after' => $product->stock_quantity,
            'notes' => 'Admin restock',
        ]);

        return back()->with('status', $product->name.' restocked.');
    }

    public function orders(): View
    {
        return view('admin.orders', [
            'orders' => Order::with(['member', 'handler', 'items.product'])->latest()->get(),
            'revenue' => Order::where('status', 'completed')->sum('total_amount'),
        ]);
    }
}
