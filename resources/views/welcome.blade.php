<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/mamah_melambai.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Pempek Mamah">
    <title>Pempek Mamah Dhani</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: transparent;
        }

        [x-cloak] {
            display: none !important;
        }

        .wave-container {
            background-image: url('/assets/images/bg-waveTR.png');
            background-repeat: repeat-x;
            background-size: contain;
            background-position: center bottom;
            background-attachment: local; 
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .search-glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        @keyframes wind-drift {
            from { background-position: 0 0; }
            to { background-position: 600px 0; }
        }
        .pink-wave-bg {
            position: relative;
            overflow: visible !important;
            background-color: #e91e63;
        }
        .pink-wave-bg::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("{{ asset('assets/images/seamless4.jpg') }}");
            background-repeat: repeat;
            background-size: 400px auto;
            opacity: 0.14;
            animation: wind-drift 35s linear infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes shimmer {
        0% { transform: translateX(-100%) rotate(45deg); }
        100% { transform: translateX(100%) rotate(45deg); }
        }

        .glow-effect {
            position: relative;
            overflow: hidden;
        }

        .glow-effect::after {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to right,
                rgba(255, 255, 255, 0) 40%,
                rgba(255, 255, 255, 0.4) 50%,
                rgba(255, 255, 255, 0) 60%
            );
            animation: shimmer 3s infinite;
        }

        #searchResults {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
            pointer-events: auto; 
        }

        #searchInput {
            transition: all 0.3s ease;
        }
        .ring-red-300 {
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.2);
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 pb-24">
<!-- @include('partials.chat-widget') ini bagian ikon melayang yang chat widget ya-->
@php
    $userWishlists = auth()->check() ? \App\Models\Wishlist::where('user_id', auth()->id())->pluck('product_id')->toArray() : [];
@endphp
<div x-data="{ 
    open: new URLSearchParams(window.location.search).get('sidebar') === 'open', 
    activeCategory: 'Semua',
    activeNav: window.location.pathname === '/' ? 'home' : window.location.pathname.includes('cart') ? 'cart' : window.location.pathname.includes('wishlist') ? 'wishlist' : null
}" x-effect="activeNav = open ? 'account' : (window.location.pathname === '/' ? 'home' : window.location.pathname.includes('cart') ? 'cart' : window.location.pathname.includes('wishlist') ? 'wishlist' : null)" x-cloak>
@include('partials.sidebar')

    <div class="pink-wave-bg pt-10 pb-4 px-4 sticky top-0 z-50 shadow-md !overflow-visible">
        <div class="relative z-10">
        <div class="flex items-center justify-between text-white">
        <div class="flex items-center gap-1">
            <!-- <i data-lucide="map-pin" class="w-8 h-8 text-white"></i> -->
            <div class="text-[12px] leading-tight" x-data>
                <p class="opacity-100">Kirim ke</p>
                @auth
                    <p class="font-bold leading-snug line-clamp-2 max-w-[200px]">
                        <span x-text="$store.userStore.fullAddress"></span> - 
                        <span x-text="$store.userStore.addressText"></span>
                    </p>
                @else
                    <p class="font-bold italic opacity-80 text-[10px]">Silahkan login dahulu</p>
                @endauth
            </div>
        </div>

        @auth
            <button @click="open = true" class="focus:outline-none relative">
                <div :class="open ? 'opacity-0 scale-50' : 'opacity-100 scale-100'" 
                    class="w-14 h-14 rounded-full -mt-6 overflow-hidden bg-pink-200 flex-shrink-0 transition-all duration-500 ease-in-out shadow-lg border-2 border-white/30">
                    @if(auth()->user()->profile_photo)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" class="global-profile-img w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-pink-600 font-black text-xs uppercase">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    @endif
                </div>
            </button>
        @else
            <a href="{{ route('login') }}" class="focus:outline-none relative">
                <div class="w-12 h-12 rounded-full -mt-6 bg-white flex items-center justify-center flex-shrink-0 shadow-lg active:scale-90 transition-all duration-300 border-2 border-pink-100">
                    <span class="text-[#e91e63] font-black text-[10px] uppercase tracking-tighter">Masuk</span>
                </div>
            </a>
        @endauth
    </div>
            
            <div class="mt-4 relative flex items-center group">
                <i data-lucide="search" class="absolute left-4 w-4 h-4 text-pink-400"></i>
                <input type="text" id="searchInput" placeholder="Cari menu favoritmu..." 
                    class="w-full py-2.5 px-10 rounded-md text-sm border-none focus:ring-2 focus:ring-pink-300 shadow-inner"
                    autocomplete="off">

                <button id="micButton" class="absolute right-4 text-pink-400 hover:text-pink-600 active:scale-90 transition-all">
                    <i data-lucide="mic" class="w-4 h-4"></i>
                </button>
                
                <div id="searchResults" 
                    class="absolute top-full left-0 right-0 mt-2 bg-white/70 backdrop-blur-md rounded-xl shadow-2xl z-[99] hidden max-h-[350px] overflow-y-auto border border-white/25 p-1">
                </div>
            </div>

        </div>
    </div>

    <div class="mt-5">
        <div class="flex overflow-x-auto pb-6 custom-scrollbar gap-4 px-5 relative">
            
            <button @click="activeCategory = 'Semua'" class="flex flex-col items-center flex-shrink-0 relative">
                <div :class="activeCategory === 'Semua' ? : ''"
                    class="w-20 h-20 flex items-center justify-center transition-all duration-300">
                    <img src="{{ asset('storage/products/pempek-semuaMN.jpg') }}" class="w-18 h-18 object-contain">
                </div>
                <span :class="activeCategory === 'Semua' ? 'text-pink-600 font-black' : 'text-gray-400 font-bold'"
                    class="text-[10px] mt-1 uppercase tracking-tighter">Semua</span>
            </button>

            <button @click="activeCategory = 'Adaan'" class="flex flex-col items-center flex-shrink-0 relative">
                <div :class="activeCategory === 'Adaan' ?  : ''"
                    class="w-20 h-20 flex items-center justify-center transition-all duration-300">
                    <img src="{{ asset('storage/products/pempek-adaanMN.png') }}" class="w-18 h-18 object-contain">
                </div>
                <span :class="activeCategory === 'Adaan' ? 'text-pink-600 font-black' : 'text-gray-400 font-bold'"
                    class="text-[10px] mt-1 uppercase tracking-tighter">Pempek Adaan</span>
            </button>

            <button @click="activeCategory = 'Kapal Selam'" class="flex flex-col items-center flex-shrink-0 relative">
                <div :class="activeCategory === 'Kapal Selam' ? : ''"
                    class="w-20 h-20 flex items-center justify-center transition-all duration-300">
                    <img src="{{ asset('storage/products/pempek-kapal-selamMN.png') }}" class="w-18 h-18 object-contain">
                </div>
                <span :class="activeCategory === 'Kapal Selam' ? 'text-pink-600 font-black' : 'text-gray-400 font-bold'"
                    class="text-[10px] mt-1 uppercase tracking-tighter">Kapal Selam</span>
            </button>

            <button @click="activeCategory = 'Lenjer'" class="flex flex-col items-center flex-shrink-0 relative">
                <div :class="activeCategory === 'Lenjer' ?  : ''"
                    class="w-20 h-20 flex items-center justify-center transition-all duration-300">
                    <img src="{{ asset('storage/products/pempek-lenjerMN.png') }}" class="w-16 h-16 object-contain">
                </div>
                <span :class="activeCategory === 'Lenjer' ? 'text-pink-600 font-black' : 'text-gray-400 font-bold'"
                    class="text-[10px] mt-1 uppercase tracking-tighter">Pempek Lenjer</span>
            </button>

            <button @click="activeCategory = 'Pempek Bakar'" class="flex flex-col items-center flex-shrink-0 relative">
                <div :class="activeCategory === 'Kapal Selam' ? : ''"
                    class="w-20 h-20 flex items-center justify-center transition-all duration-300">
                    <img src="{{ asset('storage/products/pempek-bakarMN.png') }}" class="w-16 h-16 object-contain">
                </div>
                <span :class="activeCategory === 'Pempek Bakar' ? 'text-pink-600 font-black' : 'text-gray-400 font-bold'"
                    class="text-[10px] mt-1 uppercase tracking-tighter">Pempek Bakar</span>
            </button>

        </div>
    </div>

    <div class="px-4">
        <div class="flex justify-between items-center mb-1">
            <h2 class="font-bold text-gray-900 text-base uppercase tracking-tight">
                <span x-text="activeCategory"></span> Untukmu
            </h2>
            <span class="text-[#e91e63] text-[10px] font-bold">Lihat Semua</span>
        </div>

        @foreach($products->take(4) as $p)
            <div x-show="activeCategory === 'Semua' || activeCategory === '{{ $p->category }}'"
                class="flex py-3 gap-4 items-start cursor-pointer active:opacity-70 transition-opacity border-b border-gray-50 last:border-none"
                onclick="openModalMakan({
                    id: {{ $p->id }}, 
                    name: '{{ $p->name }}',
                    price: {{ $p->discount_price ?? $p->price }},
                    category_id: {{ $p->category_id }},
                    image: '{{ asset('assets/images/' . $p->image) }}',
                    desc: '{{ addslashes($p->premium_description) }}',
                    calories: {{ $p->calories ?? 0 }},
                    protein: '{{ $p->protein }}',
                    ingredient: '{{ $p->key_ingredient }}',
                    serving: '{{ $p->serving_suggestion }}',
                    isWishlisted: {{ in_array($p->id, $userWishlists) ? 'true' : 'false' }}
                })">
                
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        @if($p->discount_price < $p->price)
                            <span class="bg-orange-100 text-orange-600 text-[8px] font-black px-1.5 py-0.5 rounded uppercase">Diskon</span>
                        @endif
                        <h3 class="font-medium text-gray-900 text-sm truncate tracking-tight">{{ $p->name }}</h3>
                    </div>

                    <div class="flex items-center gap-2 mb-2 text-[10px]">
                        <span class="flex items-center text-gray-700 font-bold">
                            <span class="text-yellow-400 mr-0.5">⭐</span> 
                            {{ $p->rating_avg > 0 ? $p->rating_avg : '4.8' }}
                        </span>
                        <span class="text-gray-400 font-medium -ml-1">({{ $p->rating_count > 0 ? $p->rating_count : '27' }})</span>
                        <span class="text-gray-300">|</span>
                        <span class="text-gray-400">{{ $p->sold_count }} Terjual</span>
                    </div>

                    <p class="text-gray-400 text-[10px] leading-relaxed line-clamp-2 mb-3 pr-2">{{ $p->description }}</p>

                    <div class="flex items-center gap-2 mt-auto">
                        <span class="text-[#e91e63] font-black text-sm">Rp{{ number_format($p->discount_price, 0, ',', '.') }}</span>
                        @if($p->discount_price < $p->price)
                            <span class="text-gray-300 line-through text-[9px]">Rp{{ number_format($p->price, 0, ',', '.') }}</span>
                        @endif
                    </div>
                </div> <div class="relative flex flex-col items-center">
                    <div class="w-[140px] h-24 rounded-xl overflow-hidden bg-white border border-gray-100 shadow-sm flex-shrink-0">
                        <img src="{{ asset('assets/images/' . $p->image) }}" class="w-full h-full object-cover">
                    </div>
                    <button class="absolute -bottom-2 bg-white border border-gray-200 text-[#e91e63] shadow-md px-4 py-1.5 rounded-lg font-black text-[10px] hover:bg-pink-50 transition-all uppercase tracking-tighter">
                        Tambah
                    </button>
                </div>
            </div>
    @endforeach

    <div class="wave-container my-6 px-4">
        <div class="flex overflow-x-auto gap-3 no-scrollbar snap-x">
            
            <div class="glow-effect flex-none w-[160px] bg-pink-50 rounded-2xl p-4 snap-start flex flex-col items-center justify-center text-center border border-pink-100 shadow-sm">
            <div class="w-35 h-35 p-1 mb-3">
                <img src="{{ asset('assets/images/mamah_melambai.png') }}" 
                    class="w-full h-full object-contain">
            </div>

            <h4 class="text-pink-600 font-bold text-[10px] mb-1 uppercase tracking-wider">
                @auth
                    Hai, {{ explode(' ', Auth::user()->name)[0] }}! 
                @else
                    Hai, Teman Makan!
                @endauth
            </h4>

            <p class="text-gray-500 text-[9px] leading-snug">
                Buruan ambil diskon 50% sebelum habis!
            </p>
        </div>

            @foreach($products->whereNotNull('discount_price') as $p)
                <div class="flex-none w-[160px] bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden snap-start cursor-pointer active:scale-95 transition-transform"
                    onclick="openModalMakan({
                        id: {{ $p->id }}, 
                        name: '{{ $p->name }}',
                        price: {{ $p->discount_price ?? $p->price }},
                        category_id: {{ $p->category_id }},
                        image: '{{ asset('assets/images/' . $p->image) }}',
                        desc: '{{ addslashes($p->premium_description ?? $p->description) }}',
                        calories: {{ $p->calories ?? 0 }},
                        protein: '{{ $p->protein ?? "-" }}',
                        ingredient: '{{ $p->key_ingredient ?? "-" }}',
                        serving: '{{ $p->serving_suggestion ?? "-" }}',
                        isWishlisted: {{ in_array($p->id, $userWishlists) ? 'true' : 'false' }}
                    })">
                    
                    <div class="h-28 w-full relative">
                        <img src="{{ asset('assets/images/' . $p->image) }}" 
                            class="w-full h-full object-cover">
                        <div class="absolute top-2 left-3 bg-orange-500 text-white text-[8px] font-bold px-2 py-1">
                            Diskon Puas
                        </div>
                    </div>
                    
                    <div class="p-3">
                        <span class="text-pink-500 font-bold text-[10px] uppercase tracking-wider block mb-1">Promo</span>
                        <h3 class="text-gray-800 font-bold text-[12px] truncate mb-0.5">{{ $p->name }}</h3>
                        <p class="text-gray-400 text-[9px] line-clamp-2 leading-tight mb-2">
                            {{ $p->description }}
                        </p>
                        
                        <div class="flex flex-wrap gap-1">
                            @php
                                $persen = round((($p->price - $p->discount_price) / $p->price) * 100);
                            @endphp
                            <span class="bg-pink-50 mt-1 text-pink-600 text-[8px] font-bold px-1.5 py-0.5 rounded border border-pink-100">
                                {{ $persen }}% off
                            </span>
                            <span class="bg-gray-50 mt-1 text-gray-500 text-[8px] font-bold px-1.5 py-0.5 rounded border border-gray-100">
                                {{ $persen - 10 }}% off
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="flex-none w-[60px] flex items-center justify-center snap-start pr-2">
                <div class="w-10 h-10 bg-white shadow-md rounded-full flex items-center justify-center border border-gray-50">
                    <i data-lucide="arrow-right" class="w-5 h-5 text-pink-500"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-10" x-data="{ 
        active: 0, 
        loop: null, 
        resumeTimeout: null,
        startAutoPlay() {
            this.stopAutoPlay();
            this.loop = setInterval(() => {
                this.active = (this.active + 1) % 3;
                const slider = this.$refs.slider;
                const cardWidth = slider.firstElementChild.offsetWidth + 12;
                slider.scrollTo({
                    left: cardWidth * this.active,
                    behavior: 'smooth'
                });
            }, 2500);
        },
        stopAutoPlay() {
            clearInterval(this.loop);
        },
        handleInteraction() {
            this.stopAutoPlay();
            clearTimeout(this.resumeTimeout);
            this.resumeTimeout = setTimeout(() => {
                this.startAutoPlay();
            }, 6000);
        }
    }" x-init="startAutoPlay()">

        <div x-ref="slider" 
            @scroll.passive="handleInteraction()"
            @touchstart="handleInteraction()"
            class="flex overflow-x-auto snap-x snap-mandatory no-scrollbar gap-3 pb-2">
            
            <div class="flex-none w-[85%] snap-center rounded-xl overflow-hidden shadow-sm border border-gray-100">
                <img src="{{ asset('assets/images/TakjilBanner.png') }}" class="w-full aspect-[21/10] object-cover">
            </div>

            <div class="flex-none w-[85%] snap-center rounded-xl overflow-hidden shadow-sm border border-gray-100">
                <img src="{{ asset('assets/images/TarhibRamadhan.png') }}" class="w-full aspect-[21/10] object-cover">
            </div>

            <div class="flex-none w-[85%] snap-center rounded-xl overflow-hidden shadow-sm border border-gray-100">
                <img src="{{ asset('assets/images/Marhaban.png') }}" class="w-full aspect-[21/10] object-cover">
            </div>
        </div>
    </div>

    <!-- <div class="px-4 mt-12 mb-10">
        <div class="relative group active:scale-[0.98] transition-transform duration-300 cursor-pointer"
        onclick="openModalMakan({
            id: 4,
            name: 'Tekwan Kuah Udang',
            price: 14000,
            category_id: 2,
            image: '{{ asset('assets/images/tekwanRL.jpg') }}',
            desc: 'Bulatan ikan tenggiri premium dengan kuah udang bening yang gurihnya nagih! Tanpa pengawet, 100% alami.',
            calories: 248,
            protein: '12g',
            ingredient: 'Ikan Tenggiri Giling Premium & Kaldu Udang Segar',
            serving: 'Nikmat disantap panas dengan perasan jeruk kunci.'
        })"
        
        <div class="mb-4 pl-2">
            <h3 class="text-[10px] font-black text-[#e91e63] uppercase tracking-[0.2em] mb-1">Menu Rekomendasi</h3>
            <h2 class="text-2xl font-black text-gray-900 leading-none">Tekwan <span class="text-pink-600">Kuah Udang</span></h2>
        </div>

        <div class="relative group active:scale-[0.98] transition-transform duration-300">
            <div class="absolute -top-3 -right-2 z-20 bg-yellow-400 text-gray-900 font-black px-4 py-2 rounded-2xl rotate-12 shadow-lg text-sm">
                Rp14rb
            </div>

            <div class="rounded-[32px] overflow-hidden shadow-[0_20px_50px_rgba(233,30,99,0.15)] border border-pink-50">
                <div class="relative w-full h-60">
                    <img src="{{ asset('assets/images/tekwanRL.jpg') }}" class="w-full h-full object-cover">
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>

                    <div class="absolute bottom-0 left-0 max-w-[70%] bg-white/50 backdrop-blur-sm rounded-tr-[40px] pl-6 pr-8 pt-4 pb-5 shadow-[-10px_0_20px_rgba(0,0,0,0.05)] border-t border-r border-white/40">
                        <div class="flex items-center gap-1 mb-2">
                            <span class="text-yellow-400 text-xs">⭐⭐⭐⭐⭐</span>
                            <span class="text-[9px] font-bold text-gray-700">(4.9)</span>
                        </div>
                        
                        <p class="text-[11px] font-bold text-gray-800 leading-relaxed italic">
                            "Bulatan ikan tenggiri premium dengan kuah udang bening yang gurihnya nagih! Tanpa pengawet, 100% alami."
                        </p>

                        <button class="mt-3 flex items-center gap-2 text-[#e91e63] font-black text-[10px] uppercase tracking-tighter">
                            Pesan Sekarang 
                            <i data-lucide="arrow-right" class="w-3 h-3" stroke-width="3"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <div class="flex justify-between items-center mb-2 mt-6">
        <h2 class="font-black text-gray-900 text-base uppercase tracking-tight">Minuman Segar</h2>
        <a href="#" class="text-[#e91e63] text-xs font-bold hover:underline tracking-tight">Lihat Semua</a>
    </div>
    @foreach($products->whereIn('category_id', [8, 9])->take(3) as $p)
            <div class="flex py-3 gap-4 items-start cursor-pointer active:opacity-70 transition-opacity border-b border-gray-50 last:border-none"
                onclick="openModalMakan({
                    id: {{ $p->id }}, 
                    name: '{{ $p->name }}',
                    price: {{ $p->discount_price ?? $p->price }},
                    category_id: {{ $p->category_id }},
                    image: '{{ asset('assets/images/' . $p->image) }}',
                    desc: '{{ addslashes($p->premium_description) }}',
                    calories: {{ $p->calories ?? 0 }},
                    protein: '{{ $p->protein }}',
                    ingredient: '{{ $p->key_ingredient }}',
                    serving: '{{ $p->serving_suggestion }}',
                    isWishlisted: {{ in_array($p->id, $userWishlists) ? 'true' : 'false' }}
                })">
                
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        @if($p->discount_price && $p->discount_price < $p->price)
                            <span class="bg-orange-100 text-orange-600 text-[8px] font-black px-1.5 py-0.5 rounded uppercase">Diskon</span>
                        @endif
                        <h3 class="font-medium text-gray-900 text-sm truncate tracking-tight">{{ $p->name }}</h3>
                    </div>

                    <div class="flex items-center gap-2 mb-2 text-[10px]">
                        <span class="flex items-center text-gray-700 font-bold">
                            <span class="text-yellow-400 mr-0.5">⭐</span> 
                            {{ $p->rating_avg > 0 ? $p->rating_avg : '4.8' }}
                        </span>
                        <span class="text-gray-400 font-medium -ml-1">({{ $p->rating_count > 0 ? $p->rating_count : '27' }})</span>
                        <span class="text-gray-300">|</span>
                        <span class="text-gray-400">100+ Terjual</span>
                    </div>

                    <p class="text-gray-400 text-[10px] leading-relaxed line-clamp-2 mb-3 pr-2">{{ $p->description }}</p>

                    <div class="flex items-center gap-2 mt-auto">
                        <span class="text-[#e91e63] font-black text-sm">Rp{{ number_format($p->discount_price ?? $p->price, 0, ',', '.') }}</span>
                        @if($p->discount_price && $p->discount_price < $p->price)
                            <span class="text-gray-300 line-through text-[9px]">Rp{{ number_format($p->price, 0, ',', '.') }}</span>
                        @endif
                    </div>
                </div> <div class="relative flex flex-col items-center">
                    <div class="w-[140px] h-24 rounded-xl overflow-hidden bg-white border border-gray-100 shadow-sm flex-shrink-0">
                        <img src="{{ asset('assets/images/' . $p->image) }}" class="w-full h-full object-cover">
                    </div>
                    <button class="absolute -bottom-2 bg-white border border-gray-200 text-[#e91e63] shadow-md px-4 py-1.5 rounded-lg font-black text-[10px] hover:bg-pink-50 transition-all uppercase tracking-tighter">
                        Tambah
                    </button>
                </div>
            </div>
    @endforeach

    <div class="flex items-center mt-6">
        <h2 class="font-black text-gray-900 text-base uppercase tracking-tight">Spesial Topping</h2>
    </div>

    <div class="wave-container my-6 px-4 mt-5">
        <div class="flex overflow-x-auto gap-3 no-scrollbar snap-x">
            
            <!-- <div class="glow-effect flex-none w-[210px] self-stretch bg-pink-50 rounded-2xl snap-start border border-pink-100 shadow-sm overflow-hidden">
                <img src="{{ asset('assets/images/cover-topping-scroll.png') }}" 
                    class="w-full h-full object-cover object-center">
            </div> -->


            @php
                // Custom urutan ID: Bengkuang, Ebi, Timun, Jamur, Bawang
                $urutan = [16, 12, 11, 14, 17,  10];
                
                // Urutkan sisa array
                $sortedToppings = $products->whereIn('category_id', [10, 11])->sortBy(function($p) use ($urutan) {
                    $pos = array_search($p->id, $urutan);
                    return $pos === false ? 999 : $pos;
                });
            @endphp

            @foreach($sortedToppings as $p)
            <div class="flex-none w-[160px] bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden snap-start cursor-pointer active:scale-95 transition-transform"
                onclick="openModalMakan({
                    id: {{ $p->id }}, 
                    name: '{{ $p->name }}',
                    price: {{ $p->discount_price ?? $p->price }},
                    category_id: {{ $p->category_id }},
                    image: '{{ asset("assets/images/" . $p->image) }}', 
                    desc: '{{ addslashes($p->premium_description ?? $p->description) }}',
                    calories: {{ $p->calories ?? 0 }},
                    protein: '{{ $p->protein ?? "-" }}',
                    ingredient: '{{ $p->key_ingredient ?? "-" }}',
                    serving: '{{ $p->serving_suggestion ?? "-" }}',
                    isWishlisted: {{ in_array($p->id, $userWishlists) ? 'true' : 'false' }}
                })">
                
                <div class="h-28 w-full relative">
                    <img src="{{ asset('assets/images/' . $p->image) }}" 
                        class="w-full h-full object-cover">
                    
                    <div class="absolute top-2 left-3 bg-orange-500 text-white text-[8px] font-bold px-2 py-1 rounded">
                        Wajib Coba
                    </div>
                </div>
                
                <div class="p-3">
                    <span class="text-pink-500 font-bold text-[10px] uppercase tracking-wider block mb-1">Ekstra</span>
                    <h3 class="text-gray-800 font-bold text-[12px] truncate mb-0.5">{{ $p->name }}</h3>
                    <p class="text-gray-400 text-[9px] line-clamp-2 leading-tight mb-2">
                        {{ $p->description }}
                    </p>
                    
                    <div class="flex flex-wrap gap-1">
                        @php
                            // Logika aman untuk persentase
                            $hargaAsli = $p->price > 0 ? $p->price : 1;
                            $hargaDiskon = $p->discount_price ?? $p->price;
                            $persen = round((($hargaAsli - $hargaDiskon) / $hargaAsli) * 100);
                        @endphp
                        
                        @if($persen > 0)
                            <span class="bg-pink-50 mt-1 text-pink-600 text-[8px] font-bold px-1.5 py-0.5 rounded border border-pink-100">
                                {{ $persen }}% off
                            </span>
                            <span class="bg-gray-50 mt-1 text-gray-500 text-[8px] font-bold px-1.5 py-0.5 rounded border border-gray-100">
                                {{ $persen - 10 }}% off
                            </span>
                        @else
                            <span class="bg-pink-50 mt-1 text-pink-600 text-[10px] font-bold px-1.5 py-0.5 rounded border border-pink-100">
                                Rp{{ number_format($p->price, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach

            <div class="flex-none w-[60px] flex items-center justify-center snap-start pr-2">
                <div class="w-10 h-10 bg-white shadow-md rounded-full flex items-center justify-center border border-gray-50">
                    <i data-lucide="arrow-right" class="w-5 h-5 text-pink-500"></i>
                </div>
            </div>
        </div>
    </div>

    @include('partials.bottom-nav')
    @include('modals.modal-makanan')
    @include('modals.modal-minuman')
    <script src="{{ asset('js/modal-logic.js') }}"></script>
</div>
</body>
<script>
const allProducts = {!! $products->toJson() !!}; 
const searchInput = document.getElementById('searchInput');
const micBtn = document.getElementById('micButton');
const resultsContainer = document.getElementById('searchResults');

lucide.createIcons();
document.addEventListener('DOMContentLoaded', () => {
    const scrollContainer = document.querySelector('#modalPremium .overflow-y-auto');
    
    if (scrollContainer) {
        scrollContainer.addEventListener('scroll', (e) => {
            localStorage.setItem('modalScrollPos', e.target.scrollTop);
        });

        const savedModal = localStorage.getItem('activeModalData');
        const savedScroll = localStorage.getItem('modalScrollPos');
        
        if (savedModal) {
            openModalMakan(JSON.parse(savedModal));
            setTimeout(() => {
                if (savedScroll) {
                    scrollContainer.scrollTo({ top: parseInt(savedScroll), behavior: 'instant' });
                }
            }, 50); 
        }
    }
});

const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

if (SpeechRecognition && micBtn) {
    const recognition = new SpeechRecognition();
    recognition.lang = 'id-ID';
    recognition.interimResults = false;

    micBtn.addEventListener('click', () => {
        micBtn.classList.add('animate-pulse', 'text-red-500');
        searchInput.placeholder = "Mendengarkan...";
        searchInput.classList.add('ring-2', 'ring-red-300');
        
        recognition.start();
    });

    recognition.onresult = (event) => {
        const transcript = event.results[0][0].transcript;
        searchInput.value = transcript;

        resetMicUI();
        searchInput.dispatchEvent(new Event('input'));
    };

    recognition.onerror = () => {
        resetMicUI();
        searchInput.placeholder = "Gagal mendengar, coba lagi...";
    };

    recognition.onend = () => {
        resetMicUI();
    };

    function resetMicUI() {
        micBtn.classList.remove('animate-pulse', 'text-red-500');
        searchInput.placeholder = "Cari menu favoritmu...";
        searchInput.classList.remove('ring-2', 'ring-red-300');
    }
}

if (searchInput) {
    searchInput.addEventListener('input', function(e) {
        const keyword = e.target.value.toLowerCase().trim();
        
        if (keyword.length < 1) {
            resultsContainer.classList.add('hidden');
            return;
        }

        const filtered = allProducts.filter(p => 
            p.name.toLowerCase().includes(keyword) || 
            (p.description && p.description.toLowerCase().includes(keyword))
        );

        renderSearchResults(filtered);
    });
}

function renderSearchResults(products) {
    resultsContainer.innerHTML = '';
    
    if (products.length === 0) {
        resultsContainer.innerHTML = `
            <div class="flex flex-col items-center justify-center py-8 px-5 text-center">
                <div class="w-36 h-36 mb-4">
                    <img src="{{ asset('assets/images/mamah_melambai.png') }}" 
                         class="w-full h-full object-contain drop-shadow-md" 
                         alt="Mamah Melambai">
                </div>
                <p class="text-[11px] font-black text-gray-900 leading-tight uppercase tracking-wide">
                    Yah, menu tidak ditemukan...
                </p>
                <p class="text-[10px] text-gray-400 italic mt-1 font-medium">
                    Coba cari kata kunci lain ya, Kak!
                </p>
            </div>
        `;
    } else {
        products.forEach(p => {
            let starsHtml = '';
            const rating = p.rating_avg || 4.5;
            for (let i = 1; i <= 5; i++) {
                if (i <= Math.floor(rating)) {
                    starsHtml += '<span class="text-yellow-400 text-[10px]">⭐</span>';
                } else if (i === Math.ceil(rating) && rating % 1 !== 0) {
                    starsHtml += `<div class="relative inline-block text-[10px] leading-none">
                        <span class="text-gray-200">⭐</span>
                        <span class="absolute top-0 left-0 text-yellow-400 overflow-hidden" style="width: 50%">⭐</span>
                    </div>`;
                } else {
                    starsHtml += '<span class="text-gray-200 text-[10px]">⭐</span>';
                }
            }

            const item = document.createElement('div');
            item.className = "flex gap-3 p-3 hover:bg-pink-50/50 active:bg-pink-100 border-b border-gray-50 last:border-none cursor-pointer rounded-lg";
            
            const imagePath = `{{ asset('assets/images') }}/${p.image}`;

            item.onclick = () => { 
                openModalMakan({ 
                    id: p.id, 
                    name: p.name, 
                    price: p.discount_price ?? p.price, 
                    category_id: p.category_id, 
                    image: `{{ asset('assets/images') }}/${p.image}`, 
                    desc: p.premium_description || p.description,
                    calories: p.calories ?? 0,
                    protein: p.protein ?? '-'
                }); 
                resultsContainer.classList.add('hidden'); 
                searchInput.value = ''; 
            };

            item.innerHTML = `
                <div class="w-14 h-14 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                    <img src="${imagePath}" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0 flex flex-col justify-between py-0.5">
                    <h4 class="text-[12px] font-black text-gray-900 truncate uppercase tracking-tight leading-tight">${p.name}</h4>
                    <div class="flex items-center gap-1">
                        <div class="flex items-center h-3">${starsHtml}</div>
                        <span class="text-[9px] text-gray-400 font-bold ml-0.5">(${p.rating_count || 27})</span>
                    </div>
                    <p class="text-[10px] text-gray-400 truncate leading-tight font-medium italic">
                        ${p.description || 'Menu lezat Mamah Dhani...'}
                    </p>
                </div>`;
            resultsContainer.appendChild(item);
        });
    }
    resultsContainer.classList.remove('hidden');
}

document.addEventListener('click', (e) => {
    if (resultsContainer && !searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
        resultsContainer.classList.add('hidden');
    }
});


</script>
</html>