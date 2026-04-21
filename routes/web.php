<?php
// web.php
use App\Http\Controllers\PempekController;
use App\Http\Controllers\AuthController;
use App\Models\Cart;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ChatController;

// Halaman Utama
Route::get('/', [PempekController::class, 'index'])->name('home');
Route::get('/get-reviews/{id}', [PempekController::class, 'getReviews']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/update-photo', [AuthController::class, 'updatePhoto'])->name('update.photo');
Route::post('/set-default-address/{id}', [AddressController::class, 'setDefault'])->name('address.set-default')->middleware('auth');
Route::post('/save-address', [AddressController::class, 'store'])->name('address.store')->middleware('auth');
Route::put('/update-address/{id}', [AddressController::class, 'update'])->name('address.update');
Route::post('/cart/update-note/{id}', [CartController::class, 'updateNote']);
Route::get('/cart', [PempekController::class, 'viewCart'])->name('cart')->middleware('auth');
Route::get('/profile/change-name', [AuthController::class, 'showChangeName'])->name('profile.change-name');
Route::any('/profile/review-name', [AuthController::class, 'reviewName'])->name('profile.review-name');
Route::post('/profile/update-name', [AuthController::class, 'updateName'])->name('profile.update-name');
Route::get('/profile/change-password', [AuthController::class, 'showChangePassword'])->name('profile.change-password');
Route::post('/profile/update-password-final', [AuthController::class, 'updatePasswordFinal'])->name('profile.update-password-final');
Route::post('/profile/update-photo', [AuthController::class, 'updatePhoto'])->name('profile.update-photo');

Route::post('/order/store', [OrderController::class, 'store'])->middleware('auth');
Route::get('/order/{id}/status', [OrderController::class, 'show'])->middleware('auth');
Route::delete('/order/{id}/delete', [OrderController::class, 'destroy'])->middleware('auth');
Route::get('/riwayat-pesanan', [OrderController::class, 'history'])->name('order.history')->middleware('auth');

Route::get('/order/{id}/courier-location', function($id) {
    $order = \App\Models\Order::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();
    return response()->json([
        'lat' => $order->courier_lat,
        'lng' => $order->courier_lng,
    ]);
})->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/settings/phone', [ProfileController::class, 'changePhone'])->name('settings.phone');
    Route::post('/settings/update-phone', [ProfileController::class, 'updatePhone'])->name('settings.update-phone');
});

Route::get('/order/{id}/receipt', [ReceiptController::class, 'show'])->name('order.receipt')->middleware('auth');

// Setup Helper
Route::get('/setup-storage', function () {
    Artisan::call('storage:link');
    return 'Folder Storage sudah berhasil muncul di Public! Silakan cek sekarang.';
});

Route::get('/wishlist', [PempekController::class, 'wishlist'])->name('wishlist')->middleware('auth');
Route::post('/wishlist/toggle', [PempekController::class, 'toggleWishlist'])->name('wishlist.toggle')->middleware('auth');
Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy')->middleware('auth');

Route::post('/cart/add', function (Request $request) {
    if (!Auth::check()) {
        return response()->json([
            'success' => false, 
            'message' => 'Silakan login terlebih dahulu untuk memesan!'
        ]);
    }

    Cart::create([
        'user_id' => Auth::id(),
        'product_id' => $request->product_id,
        'quantity' => $request->quantity,
        'toppings' => json_encode($request->toppings),
        'extra_sides' => json_encode($request->extra_sides),
    ]);

    $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');
    return response()->json([
        'success' => true,
        'message' => 'Berhasil ditambahkan ke keranjang!',
        'cart_count' => $cartCount
    ]);
});

Route::delete('/cart/remove/{id}', function ($id) {
    if (!Auth::check()) return response()->json(['success' => false]);

    Cart::where('id', $id)->where('user_id', Auth::id())->delete();
    $cartCount = Cart::where('user_id', Auth::id())->sum('quantity') ?: 0;

    return response()->json([
        'success' => true,
        'new_count' => $cartCount
    ]);
});

Route::get('/pusat-bantuan', [ChatController::class, 'index'])->name('pusat-bantuan')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::post('/chat/open', [ChatController::class, 'open']);
    Route::post('/chat/{conversation}/send', [ChatController::class, 'send']);
    Route::post('/chat/{conversation}/quick-reply', [ChatController::class, 'quickReply']);
    Route::post('/chat/{conversation}/resolve', [ChatController::class, 'resolve']);
    Route::post('/chat/{conversation}/refresh-welcome', [ChatController::class, 'refreshWelcome'])->middleware('auth');
    Route::post('/chat/{conversation}/react/{message}', [ChatController::class, 'react']);
    Route::delete('/chat/{conversation}/unsend/{message}', [ChatController::class, 'unsend']);
    Route::post('/message/delete', [ChatController::class, 'delete']);
});

Route::get('/pusat-pengguna', function () {
    $allAddresses = \App\Models\Address::where('user_id', auth()->id())->get();
    $address = $allAddresses->where('is_default', 1)->first();
    
    return view('profile', compact('address', 'allAddresses'));
})->name('profile')->middleware('auth');

Route::get('/update-geocode', function() {
    $addresses = \App\Models\Address::whereNull('lat')->orWhereNull('lng')->get();
    $results = [];

    foreach ($addresses as $address) {
        $q = $address->district . ', ' . $address->city . ', Indonesia';
        
        try {
            $res = \Illuminate\Support\Facades\Http::timeout(5)
                ->withHeaders(['User-Agent' => 'PempekMamahDhaniApp/1.0'])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $q, 'format' => 'json', 'limit' => 1
                ]);
            
            $data = $res->json();
            
            if (!empty($data)) {
                $address->update(['lat' => $data[0]['lat'], 'lng' => $data[0]['lon']]);
                $results[] = "✓ {$address->label} ({$address->city}): {$data[0]['lat']}, {$data[0]['lon']}";
            } else {
                $results[] = "✗ Tidak ditemukan: {$q}";
            }
            
            sleep(1);
            
        } catch (\Exception $e) {
            $results[] = "✗ Error: " . $e->getMessage();
        }
    }

    return response()->json([
        'total' => $addresses->count(),
        'results' => $results
    ]);
});

