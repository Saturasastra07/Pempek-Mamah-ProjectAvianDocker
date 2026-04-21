<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Voucher;
use App\Models\Address;

class CartController extends Controller
{   
    public function index()
    {
        $vouchers = Voucher::all(); 
        $allAddresses = \App\Models\Address::where('user_id', auth()->id())->get();
        $dbItems = \App\Models\Cart::where('user_id', auth()->id())
        ->with('product')
        ->get()
        ->filter(fn($cart) => $cart->product !== null)
        ->map(function($cart) {
            return [
                'id'            => $cart->id,
                'product_id'    => $cart->product_id,
                'name'          => $cart->product->name,
                'image'         => $cart->product->image,
                'basePrice'     => $cart->product->price,
                'discountPrice' => $cart->product->discount_price,
                'rating'        => $cart->product->rating_avg ?? 4.8,
                'quantity'      => $cart->quantity,
                'addons'        => $cart->toppings ?? [],
                'notes'         => $cart->notes,
                'checked'       => true,
                'is_gift'       => false,
            ];
        })->values();

        return view('cart', compact('vouchers', 'dbItems', 'allAddresses'));
    }

    public function updateNote(Request $request, $id)
    {
        $cart = Cart::findOrFail($id);
        $cart->notes = $request->note;
        $cart->save();

        return response()->json(['success' => true]);
    }
}