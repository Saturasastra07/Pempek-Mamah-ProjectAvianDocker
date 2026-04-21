<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Wishlist - Pempek Mamah Dhani</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fdfaf9; }
        [x-cloak] { display: none !important; }
        
        .kroak-left {
            box-shadow: 4px 4px 0 4px white;
        }
    </style>
</head>
<body class="pb-24">
    <div x-data="layoutApp()" x-cloak>
        @include('partials.sidebar')

    <div class="max-w-md mx-auto min-h-screen">
        <nav class="flex items-center justify-between px-6 pt-10 pb-6 sticky top-0 z-50 bg-[#fdfaf9]/90 backdrop-blur-md">
            <button onclick="localStorage.removeItem('activeModalData'); window.location.href='{{ route('home') }}'" class="w-10 h-10 flex items-center justify-center text-gray-800 hover:text-[#e91e63] transition-colors">
                <i data-lucide="arrow-left" class="w-6 h-6"></i>
            </button>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Wishlist</h1>
            <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm text-gray-800 hover:text-[#e91e63] transition-colors relative">
                <i data-lucide="bell" class="w-5 h-5"></i>
                <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-[#e91e63] rounded-full border-2 border-white"></span>
            </button>
        </nav>

        <div class="px-5 space-y-4">
            @forelse($wishlists as $item)
            <div class="bg-white relative rounded-3xl overflow-hidden p-3 pt-3 pr-3 pb-3 flex gap-4 shadow-[0_4px_20px_-2px_rgba(0,0,0,0.08)]">
            <div class="w-28 h-28 rounded-t-2xl overflow-hidden shrink-0 border border-white">
                <img src="{{ asset('assets/images/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
            </div>

            <div class="absolute bottom-2 left-3 bg-white rounded-tl-[18px] pt-2 pl-2 flex items-center gap-1.5 z-10">
                <div class="absolute bottom-0 -left-3 w-3 h-3 overflow-hidden pointer-events-none">
                    <div class="w-full h-full bg-white rounded-br-full shadow-[4px_4px_0_4px_white]"></div>
                </div>

                <button 
                    onclick="addToCartFromWishlist({
                        id: {{ $item->product->id }},
                        name: '{{ $item->product->name }}',
                        price: {{ $item->product->discount_price ?? $item->product->price }}
                    }, this)"
                    class="bg-opacity-10 bg-[#e91e63] text-[#e91e63] hover:bg-[#e91e63] hover:text-white transition-colors rounded-full px-3 py-1.5 flex items-center gap-1.5 shadow-sm active:scale-95">
                    <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                    <span class="text-[10px] font-bold tracking-wide">Tambah</span>
                </button>
                
                <button class="bg-[#e91e63] bg-opacity-10 text-[#e91e63] hover:bg-[#e91e63] hover:text-white transition-colors rounded-full p-1.5 shadow-sm">
                    <i data-lucide="heart" class="w-3.5 h-3.5 fill-current"></i>
                </button>
            </div>

            <div x-data="{ 
                deleting: false,
                async deleteItem(id) {
                    if(!confirm('Hapus dari wishlist?')) return;
                    this.deleting = true;
                    
                    try {
                        let response = await fetch(`/wishlist/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        });
                        
                        let result = await response.json();
                        
                        if(result.success) {
                            window.dispatchEvent(new CustomEvent('set-wishlist', { 
                                detail: { id: result.product_id, status: false } 
                            }));
                            
                            let savedData = localStorage.getItem('activeModalData');
                            if(savedData) {
                                let parsedData = JSON.parse(savedData);
                                if(parsedData.id == result.product_id) {
                                    parsedData.isWishlisted = false;
                                    localStorage.setItem('activeModalData', JSON.stringify(parsedData));
                                }
                            }

                            $el.closest('.bg-white').remove();

                            let inCart = JSON.parse(localStorage.getItem('inCartProducts') || '[]');
                            inCart = inCart.filter(id => id !== Number(result.product_id));
                            localStorage.setItem('inCartProducts', JSON.stringify(inCart));

                            const container = document.querySelector('.px-5.space-y-4');
                            const remaining = container.querySelectorAll('.rounded-3xl');
                            if (remaining.length === 0) {
                                container.innerHTML = '<div class=\'flex flex-col items-center justify-center py-20 text-center\'>'
                                    + '<div class=\'w-24 h-24 bg-pink-50 rounded-full flex items-center justify-center mb-4\'>'
                                    + '<i data-lucide=\'heart\' class=\'w-10 h-10 text-pink-300\'></i>'
                                    + '</div>'
                                    + '<h3 class=\'font-bold text-gray-900 text-lg mb-1\'>Belum ada favorit</h3>'
                                    + '<p class=\'text-sm text-gray-400\'>Yuk, cari menu pempek kesukaanmu!</p>'
                                    + '<a href=\'/\' class=\'mt-6 bg-[#e91e63] text-white px-6 py-2.5 rounded-full font-bold text-sm shadow-lg shadow-pink-200 active:scale-95 transition-all\'>Eksplor Menu</a>'
                                    + '</div>';
                                lucide.createIcons();
                            }
                        }
                    } catch (error) {
                        console.error('Gagal menghapus:', error);
                    } finally {
                        this.deleting = false;
                    }
                }
            }">
                <button @click="deleteItem({{ $item->id }})" 
                        :class="deleting ? 'opacity-50 cursor-not-allowed' : ''"
                        class="absolute bottom-3 right-3 bg-gray-100 text-gray-400 hover:bg-red-500 hover:text-white transition-colors rounded-full p-2 z-20">
                    <i data-lucide="trash-2" class="w-4 h-4" x-show="!deleting"></i>
                    <span x-show="deleting" class="text-[8px]">...</span>
                </button>
            </div>

                <div class="flex-1 py-1.6 flex flex-col justify-between relative">
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm leading-tight line-clamp-2 pr-2">
                            {{ $item->product->name }}
                        </h3>
                        
                        <p class="text-[11px] font-medium text-gray-400 mt-1.5">
                            {{ $item->product->category }} 
                            <span class="mx-1 text-gray-200">|</span> 
                            {{ $item->product->sold_count }} terjual
                        </p>

                        <div class="flex items-center gap-0.5 mt-2">
                            @php
                                $rating = floor($item->product->rating_avg ?? 5); 
                            @endphp
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $rating)
                                    <i data-lucide="star" class="w-3.5 h-3.5 fill-yellow-400 text-yellow-400"></i>
                                @else
                                    <i data-lucide="star" class="w-3.5 h-3.5 text-gray-200"></i>
                                @endif
                            @endfor
                        </div>
                    </div>

                    <div class="font-black text-md text-gray-700 tracking-tight flex items-end">
                        Rp {{ number_format($item->product->discount_price ?? $item->product->price, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            @empty
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-24 h-24 bg-pink-50 rounded-full flex items-center justify-center mb-4">
                        <i data-lucide="heart" class="w-10 h-10 text-pink-300"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg mb-1">Belum ada favorit</h3>
                    <p class="text-sm text-gray-400">Yuk, cari menu pempek kesukaanmu!</p>
                    <a href="{{ route('home') }}" class="mt-6 bg-[#e91e63] text-white px-6 py-2.5 rounded-full font-bold text-sm shadow-lg shadow-pink-200 active:scale-95 transition-all">
                        Eksplor Menu
                    </a>
                </div>
            @endforelse
        </div>
    @include('partials.bottom-nav')
    </div>
</div>

    <script src="{{ asset('js/layout-app.js') }}"></script>

<script>
    lucide.createIcons();

    function addToCartFromWishlist(product, btn) {
        const spanText = btn.querySelector('span');
        
        if (btn.disabled) return;
        btn.disabled = true;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: product.id,
                quantity: 1,
                toppings: [],
                extra_sides: [],
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                document.querySelectorAll('.global-cart-badge').forEach(badge => {
                    badge.innerText = result.cart_count;
                    badge.style.display = 'block';
                });

                spanText.innerHTML = 'Ditambahkan';

                const card = btn.closest('[class*="rounded-3xl"]');
                if (card) {
                    const priceEl = card.querySelector('.tracking-tight.flex');
                    if (priceEl) {
                        priceEl.style.transition = 'transform 0.3s ease';
                        priceEl.style.transform = 'translateX(20px)';
                    }
                }

                let inCart = JSON.parse(localStorage.getItem('inCartProducts') || '[]');
                if (!inCart.includes(Number(product.id))) inCart.push(Number(product.id));
                localStorage.setItem('inCartProducts', JSON.stringify(inCart));

            } else {
                alert(result.message || 'Gagal menambah ke keranjang');
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btn.disabled = false;
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        function syncCartButtons() {
            const inCart = JSON.parse(localStorage.getItem('inCartProducts') || '[]');

            document.querySelectorAll('[onclick^="addToCartFromWishlist"]').forEach(btn => {
                const match = btn.getAttribute('onclick').match(/id:\s*(\d+)/);
                if (!match) return;
                const productId = Number(match[1]);

                const card = btn.closest('[class*="rounded-3xl"]');
                const priceEl = card ? card.querySelector('.tracking-tight.flex') : null;

                if (inCart.includes(productId)) {
                    btn.querySelector('span').innerHTML = 'Ditambahkan';
                    btn.disabled = true;
                    if (priceEl) priceEl.style.transform = 'translateX(20px)';
                } else {
                    btn.querySelector('span').innerHTML = 'Tambah';
                    btn.disabled = false;
                    if (priceEl) priceEl.style.transform = 'translateX(0)';
                }
            });
        }

        syncCartButtons();

        window.addEventListener('pageshow', syncCartButtons);
        window.addEventListener('storage', (e) => {
            if (e.key === 'inCartProducts') syncCartButtons();
        });
    });
</script>
</body>
</html>