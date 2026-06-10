<?php

namespace App\Http\Controllers;

use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CafeController extends Controller
{
    public function shop(): View
    {
        $user = Auth::user();
        $user->ensureMemberIdentity();

        return view('client.cafe', [
            'wallet' => Wallet::firstOrCreate(['member_id' => $user->id], ['balance' => 0]),
            'products' => Product::where('is_active', true)->orderBy('category')->orderBy('name')->get(),
            'orders' => $user->cafeOrders()->with('items.product')->latest()->take(8)->get(),
        ]);
    }

    
    public function wallet(): View
    {
        $wallet = Wallet::firstOrCreate(['member_id' => Auth::id()], ['balance' => 0]);

        return view('client.wallet', [
            'wallet' => $wallet,
            'orders' => Auth::user()->cafeOrders()->with('items.product')->latest()->take(10)->get(),
            'topUps' => Payment::where('member_id', Auth::id())
                ->where('reference', 'like', 'CAFE-WALLET-%')
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }

    public function deposit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:1000000'],
        ]);

        return back()
            ->withInput($data)
            ->with('warning', 'Wallet deposits are now added through M-PESA STK confirmation.');
    }

    public function order(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'integer', 'min:0', 'max:99'],
        ]);

        $quantities = collect($data['items'])->filter(fn ($quantity) => (int) $quantity > 0);

        if ($quantities->isEmpty()) {
            return back()->with('warning', 'Choose at least one café item.');
        }

        try {
            DB::transaction(function () use ($quantities) {
                $wallet = Wallet::where('member_id', Auth::id())->lockForUpdate()->firstOrCreate(
                    ['member_id' => Auth::id()],
                    ['balance' => 0]
                );

                $products = Product::whereIn('id', $quantities->keys())->lockForUpdate()->get()->keyBy('id');
                $total = 0;

                foreach ($quantities as $productId => $quantity) {
                    $product = $products->get((int) $productId);
                    abort_if(! $product || ! $product->is_active, 422, 'A selected item is unavailable.');
                    abort_if($product->stock_quantity < $quantity, 422, $product->name.' is low on stock.');
                    $total += (float) $product->price * (int) $quantity;
                }

                abort_if((float) $wallet->balance < $total, 422, 'Insufficient wallet balance.');

                $order = Order::create([
                    'member_id' => Auth::id(),
                    'order_number' => 'ORD-'.now()->format('YmdHis').'-'.Auth::id(),
                    'status' => 'pending',
                    'total_amount' => $total,
                    'paid_at' => now(),
                ]);

                foreach ($quantities as $productId => $quantity) {
                    $product = $products->get((int) $productId);
                    $quantity = (int) $quantity;
                    $lineTotal = (float) $product->price * $quantity;

                    $order->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $product->price,
                        'line_total' => $lineTotal,
                    ]);

                    $product->decrement('stock_quantity', $quantity);
                    $product->refresh();

                    InventoryLog::create([
                        'product_id' => $product->id,
                        'user_id' => Auth::id(),
                        'type' => 'sale',
                        'quantity_change' => -$quantity,
                        'stock_after' => $product->stock_quantity,
                        'notes' => 'Order '.$order->order_number,
                    ]);
                }

                $wallet->decrement('balance', $total);
            });
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            return back()->with('warning', $exception->getMessage());
        }

        return back()->with('status', 'Order sent to the café. Your wallet was deducted.');
    }

    public function dashboard(): View
    {
        return view('cafe.dashboard', [
            'pendingCount' => Order::where('status', 'pending')->count(),
            'orders' => Order::with(['member', 'items.product'])
                ->whereIn('status', ['pending', 'accepted', 'preparing'])
                ->latest()
                ->get(),
            'recentOrders' => Order::with('member')->latest()->take(8)->get(),
        ]);
    }

    public function updateOrder(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:accepted,preparing,completed,cancelled'],
        ]);

        $order->update([
            'status' => $data['status'],
            'handled_by' => Auth::id(),
        ]);

        return back()->with('status', 'Order '.$order->order_number.' updated.');
    }
}
