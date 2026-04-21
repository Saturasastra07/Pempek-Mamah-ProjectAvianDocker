<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function show($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->with(['items.product', 'address'])
            ->firstOrFail();

        return view('receipt', compact('order'));
    }
}