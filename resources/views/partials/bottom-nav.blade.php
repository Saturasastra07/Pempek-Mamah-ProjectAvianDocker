<div class="nav-bottom fixed bottom-0 left-0 right-0 z-[70]"
    :class="open ? 'rounded-tl-[32px] rounded-tr-none' : 'rounded-t-[32px]'"
    style="overflow: hidden;">

    <div class="bg-white/80 backdrop-blur-md border-t border-pink-100/60 px-5 py-3.5 flex justify-between items-center shadow-[0_-10px_30px_rgba(233,30,99,0.08)]">

    <div @click="activeNav = 'home'; open = false; window.location.href='{{ route('home') }}'" class="flex items-center gap-1.5 cursor-pointer transition-all duration-300 rounded-full px-5 py-2.5" :class="activeNav === 'home' ? 'bg-pink-50' : ''">
        <img src="{{ asset('assets/images/iconHome.png') }}" alt="home" class="w-8 h-8 object-contain flex-shrink-0">
        <span x-show="activeNav === 'home'" class="text-[14px] font-bold text-[#e91e63] whitespace-nowrap">Beranda</span>
    </div>

    <div @click="activeNav = 'cart'; window.location.href='{{ route('cart') }}'" class="flex items-center gap-1.5 cursor-pointer transition-all duration-300 rounded-full px-5 py-2.5" :class="activeNav === 'cart' ? 'bg-pink-50' : ''">
        <img src="{{ asset('assets/images/iconCart.png') }}" alt="cart" class="w-8 h-8 object-contain flex-shrink-0">
        <span x-show="activeNav === 'cart'" class="text-[14px] font-bold text-[#e91e63] whitespace-nowrap">Keranjang</span>
    </div>

    <div @click="activeNav = 'wishlist'; window.location.href='{{ route('wishlist') }}'" class="flex items-center gap-1.5 cursor-pointer transition-all duration-300 rounded-full px-5 py-2.5" :class="activeNav === 'wishlist' ? 'bg-pink-50' : ''">
        <img src="{{ asset('assets/images/iconWishlist.png') }}" alt="wishlist" class="w-8 h-8 object-contain flex-shrink-0">
        <span x-show="activeNav === 'wishlist'" class="text-[14px] font-bold text-[#e91e63] whitespace-nowrap">Favorit</span>
    </div>

    <div @click="activeNav = 'account'; open = true" class="flex items-center gap-1.5 cursor-pointer transition-all duration-300 rounded-full px-5 py-2.5" :class="activeNav === 'account' ? 'bg-pink-50' : ''">
        <img src="{{ asset('assets/images/iconAccount.png') }}" alt="account" class="w-8 h-8 object-contain flex-shrink-0">
        <span x-show="activeNav === 'account'" class="text-[14px] font-bold text-[#e91e63] whitespace-nowrap">Profil</span>
    </div>
</div>