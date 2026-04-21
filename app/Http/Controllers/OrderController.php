<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $cartItems = Cart::where('user_id', $user->id)
            ->with('product')
            ->get()
            ->filter(fn($c) => $c->product !== null);

        if ($cartItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Keranjang kosong']);
        }

        // Hitung subtotal
        $subtotal = $cartItems->sum(function($cart) {
            $price = $cart->product->discount_price ?? $cart->product->price;
            $addonTotal = collect($cart->toppings ?? [])->sum('price');
            return ($price + $addonTotal) * $cart->quantity;
        });

        $shippingCost = $request->delivery_mode === 'pickup' ? 0 : 10000;
        $discount = $request->discount ?? 0;
        $grandTotal = $subtotal + $shippingCost - $discount;

        $receiverLat = null;
        $receiverLng = null;

        if ($request->is_dropship && $request->dropship_receiver_address) {
            try {
                $q = $request->dropship_receiver_district . ', ' . $request->dropship_receiver_city . ', Indonesia';
                $res = \Illuminate\Support\Facades\Http::timeout(5)
                    ->withHeaders(['User-Agent' => 'PempekMamahDhaniApp/1.0'])
                    ->get('https://nominatim.openstreetmap.org/search', [
                        'q' => $q, 'format' => 'json', 'limit' => 1
                    ]);
                $data = $res->json();
                if (!empty($data)) {
                    $receiverLat = $data[0]['lat'];
                    $receiverLng = $data[0]['lon'];
                }
            } catch (\Exception $e) {}
        }

        $order = Order::create([
            'user_id'        => $user->id,
            'order_code'     => 'PMD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)),
            'status'         => 'pending',
            'delivery_mode'  => $request->delivery_mode ?? 'delivery',
            'address_id'     => $request->delivery_mode === 'pickup' ? null : $request->address_id,
            'payment_method' => $request->payment_method ?? 'cod',
            'subtotal'       => $subtotal,
            'shipping_cost'  => $shippingCost,
            'discount'       => $discount,
            'grand_total'    => $grandTotal,
            'voucher_ids'    => $request->voucher_ids ?? [],
            'is_dropship'    => $request->is_dropship ?? false,
            'dropship_name'  => $request->dropship_name,
            'dropship_phone' => $request->dropship_phone,
            'dropship_receiver_name'     => $request->dropship_receiver_name,
            'dropship_receiver_phone'    => $request->dropship_receiver_phone,
            'dropship_receiver_address'  => $request->dropship_receiver_address,
            'dropship_receiver_district' => $request->dropship_receiver_district,
            'dropship_receiver_city'     => $request->dropship_receiver_city,
            'dropship_receiver_lat' => $receiverLat,
            'dropship_receiver_lng' => $receiverLng,
            'notes'          => $request->notes,
        ]);


        foreach ($cartItems as $cart) {
            $price = $cart->product->discount_price ?? $cart->product->price;
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $cart->product_id,
                'product_name' => $cart->product->name,
                'price'        => $price,
                'quantity'     => $cart->quantity,
                'addons'       => $cart->toppings ?? [],
                'notes'        => $cart->notes,
            ]);
        }

        // Hapus cart setelah order dibuat
        Cart::where('user_id', $user->id)->delete();

        return response()->json([
            'success'    => true,
            'order_id'   => $order->id,
            'order_code' => $order->order_code,
        ]);
    }

    public function history()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['items.product'])
            ->latest()
            ->get();

        return view('order-history', compact('orders'));
    }

    public function destroy($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $order->items()->delete();
        $order->delete();

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->with(['items.product', 'address'])
            ->firstOrFail();

        return view('order-status', compact('order'));
    }
}