<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function destroy($id)
    {
        $wishlist = Wishlist::where('id', $id)
                            ->where('user_id', auth()->id())
                            ->first();

        if ($wishlist) {
            $productId = $wishlist->product_id;
            $wishlist->delete();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil dihapus dari favorit',
                'product_id' => $productId
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Data tidak ditemukan'
        ], 404);
    }
}