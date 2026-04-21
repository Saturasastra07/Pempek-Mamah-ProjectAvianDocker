<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PempekController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('welcome', compact('products'));
    }

    public function getReviews($id)
    {
        $reviews = DB::table('product_reviews')
                    ->where('product_id', $id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json($reviews);
    }

    public function viewCart()
    {
        $vouchers = \App\Models\Voucher::all();
        $cartData = \App\Models\Cart::where('user_id', auth()->id())
            ->join('products', 'carts.product_id', '=', 'products.id')
            ->select(
                'carts.id as cart_id', 
                'carts.product_id',
                'carts.quantity', 
                'carts.toppings', 
                'carts.notes',
                'products.name', 
                'products.image', 
                'products.price as base_price',
                'products.discount_price'
            )
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->cart_id,
                    'product_id' => (int)$item->product_id,
                    'name' => $item->name,
                    'image' => $item->image,
                    'basePrice' => (int)$item->base_price,
                    'discountPrice' => $item->discount_price 
                        ? (int)$item->discount_price 
                        : null,
                    'quantity' => (int)$item->quantity,
                    'rating' => 4.8,
                    'checked' => true,
                    'addons' => is_array($item->toppings) ? $item->toppings : (json_decode($item->toppings, true) ?: []),
                    'notes' => $item->notes
                ];
            });

        $allAddresses = \App\Models\Address::where('user_id', auth()->id())
                        ->orderBy('is_default', 'desc')
                        ->get();

        return view('cart', [
            'dbItems' => $cartData,
            'allAddresses' => $allAddresses,
            'vouchers' => $vouchers
        ]);
    }

    public function wishlist() {
        $wishlists = \App\Models\Wishlist::with('product')->where('user_id', auth()->id())->get();
        return view('wishlist', compact('wishlists'));
    }

    public function toggleWishlist(Request $request) {
        $userId = auth()->id();
        $productId = $request->product_id;
        $wishlist = \App\Models\Wishlist::where('user_id', $userId)
                                        ->where('product_id', $productId)
                                        ->first();

        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['status' => 'removed']);
        } else {
            \App\Models\Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId
            ]);
            return response()->json(['status' => 'added']);
        }
    }
}