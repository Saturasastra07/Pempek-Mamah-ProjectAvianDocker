<div id="modalPremium" class="fixed inset-0 bg-white z-[100] hidden flex-col h-screen overflow-hidden">
    
    <div class="px-5 py-3 flex justify-between items-center bg-white/50 backdrop-blur-md sticky top-0 z-40 border-b border-gray-50/50">
        <button onclick="closeModal()" class="w-10 h-10 flex items-center justify-center active:scale-90 transition-all">
            <i data-lucide="arrow-left" class="w-5 h-5 text-gray-700"></i>
        </button>

        <span class="text-[15px] font-black text-gray-800 uppercase tracking-[0.2em]">Detail Menu</span>
        @php
            $realCartCount = auth()->check() ? \App\Models\Cart::where('user_id', auth()->id())->sum('quantity') : 0;
        @endphp
        <button onclick="window.location.href='{{ route('cart') }}'" class="relative p-2 text-[#e91e63] hover:bg-pink-50 rounded-full transition-colors active:scale-90">
            <i data-lucide="shopping-cart" class="w-6 h-6"></i>
            
            <span class="global-cart-badge absolute top-0 right-0 transform translate-x-1/4 -translate-y-1/4 bg-red-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full border-2 border-white shadow-sm"
                style="{{ $realCartCount > 0 ? '' : 'display: none;' }}">
                {{ $realCartCount }}
            </span>
        </button>
    </div>
    <div class="flex-1 -mt-8 overflow-y-auto pb-20">
        <div class="bg-gray-50 w-full py-8 h-74 flex justify-center relative overflow-hidden">
            <img id="modalImage" src="" class="w-full h-full object-cover">
            
            <div id="wishlistAlpineData" 
            x-data="{ 
                inWishlist: false, 
                loading: false,
                productId: null,
            
                async toggle() {
                    if(this.loading || !this.productId) return;
                    this.inWishlist = !this.inWishlist;
                    this.loading = true;

                let savedData = localStorage.getItem('activeModalData');
                if(savedData) {
                    let parsedData = JSON.parse(savedData);
                    parsedData.isWishlisted = this.inWishlist;
                    localStorage.setItem('activeModalData', JSON.stringify(parsedData));
                }
                    
                    try {
                        const response = await fetch('{{ route('wishlist.toggle') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content
                            },
                            body: JSON.stringify({ product_id: this.productId })
                        });
                        
                        if (!response.ok) throw new Error('Gagal ke server');
                        
                    } catch (error) {
                        this.inWishlist = !this.inWishlist; 
                        console.error('Wishlist error:', error);
                    } finally {
                        this.loading = false;
                    }
                }
            }"
            
            @set-wishlist.window="
                productId = $event.detail.id; 
                inWishlist = ($event.detail.status === true || $event.detail.status === 'true' || $event.detail.status === 1);
            ">

            <button @click.prevent="toggle()" 
                    :disabled="loading"
                    class="absolute bottom-4 right-8 bg-white p-3 rounded-full shadow-lg active:scale-90 transition-all"
                    :class="inWishlist ? 'text-red-500' : 'text-gray-300 hover:text-red-400'">
                
                <i data-lucide="heart" class="w-6 h-6" :class="inWishlist ? 'fill-current' : ''"></i>
            </button>
        </div>
    </div>

        <div class="px-5 -mt-4 relative">
            <div class="flex items-center gap-3 z-20">
                <h2 id="modalName" class="text-2xl font-bold text-[#e91e63]"></h2>
            </div>
            <div class="flex items-center gap-1 mb-6">
                <span class="text-yellow-400 text-sm">⭐⭐⭐⭐⭐</span>
                <span class="text-xs text-gray-400 font-medium">(59 ratings)</span>
            </div>

            <div class="flex justify-between items-center -mt-3 mb-8">
                <span id="modalPrice" class="text-2xl font-black text-gray-900"></span>
                <div class="flex items-center bg-[#e91e63] text-white rounded-full px-3 py-1.5 gap-4 shadow-md">
                    <button onclick="updateQty(-1)" class="font-black text-lg w-6 h-6 flex items-center justify-center">-</button>
                    <span id="modalQty" class="font-bold text-sm">1</span>
                    <button onclick="updateQty(1)" class="font-bold text-lg w-6 h-6 flex items-center justify-center">+</button>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-sm font-bold text-gray-900 mb-1">Deskripsi</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    <span id="modalDesc"></span>
                    <span class="text-[#e91e63] font-medium block mt-1">
                        (Setiap porsi mengandung 
                    <span id="modalCalories">   
                    </span> kalori & <span id="modalProtein">
                    </span> protein)</span>
                </p>
            </div>

    <div class="mb-8 -mt-2">
        <h3 class="text-sm font-bold text-gray-900 mb-3 tracking-tight">Rekomendasi Pendamping</h3>
        <div class="flex overflow-x-auto gap-3 no-scrollbar pb-2 pr-4 snap-x">
            @foreach($products->whereNotIn('category_id', [10, 11])->shuffle() as $side)
            <div class="side-item flex-none w-28 rounded-xl border border-gray-100 overflow-hidden shadow-sm snap-start relative group bg-white hidden" 
                data-id="{{ $side->id }}" 
                data-is-drink="{{ in_array($side->category_id, [8,9]) ? 'true' : 'false' }}">
                
                <div class="cursor-pointer" onclick="openModalMakan({
                    id: {{ $side->id }},
                    name: '{{ $side->name }}',
                    price: {{ $side->discount_price ?? $side->price }},
                    category_id: {{ $side->category_id }},
                    image: '{{ asset('assets/images/' . $side->image) }}',
                    desc: '{{ addslashes($side->premium_description ?? $side->description) }}'
                })">
                    <img src="{{ asset('assets/images/' . $side->image) }}" class="w-full h-20 object-cover bg-gray-50">
                    <div class="p-2">
                        <p class="text-[10px] text-gray-800 font-bold truncate leading-tight">{{ $side->name }}</p>
                    </div>
                </div>

                <div class="px-2 pb-2 flex justify-between items-center -mt-1">
                    <span class="text-[9px] font-black text-[#e91e63]">
                        Rp{{ number_format($side->discount_price ?? $side->price, 0, ',', '.') }}
                    </span>
                    <button onclick="addSideToTotal({{ $side->discount_price ?? $side->price }}, '{{ $side->name }}')" 
                            class="text-orange-500 active:scale-90 transition-transform">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
            @endforeach

        </div>
    </div>

    <div id="temperatureOption" class="px-2 mb-2 hidden">
        <h3 class="text-sm font-bold text-gray-900 mb-1">Pilihan Suhu</h3>
        
        <label id="iceOption" class="group flex items-center justify-start py-3 rounded-2xl cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="relative flex items-center">
                    <input type="radio" name="temp" value="Ice" class="peer h-4 w-4 cursor-pointer appearance-none rounded-full border border-gray-300 checked:bg-[#e91e63] checked:border-[#e91e63] transition-all">
                    <div class="absolute h-1.5 w-1.5 bg-white rounded-full opacity-0 peer-checked:opacity-100 left-[5px] pointer-events-none"></div>
                </div>
                <p class="text-sm font-md text-gray-800">Sajikan Dingin (Es)</p>
            </div>
        </label>

        <label id="hotOption" class="group flex items-center justify-start py-3 rounded-2xl cursor-pointer -mt-4">
            <div class="flex items-center gap-3">
                <div class="relative flex items-center">
                    <input type="radio" name="temp" value="Hot" class="peer h-4 w-4 cursor-pointer appearance-none rounded-full border border-gray-300 checked:bg-[#e91e63] checked:border-[#e91e63] transition-all">
                    <div class="absolute h-1.5 w-1.5 bg-white rounded-full opacity-0 peer-checked:opacity-100 left-[5px] pointer-events-none"></div>
                </div>
                <p class="text-sm font-md text-gray-800">Sajikan Hangat/Panas</p>
            </div>
        </label>
    </div>

    <div id="extraOption" class="px-2 -mt-2 hidden">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Tambah Ekstra?</h3>
        @foreach($products->whereIn('category_id', [10, 11]) as $top)
            <label class="topping-item group flex items-center justify-start py-3 rounded-2xl cursor-pointer -mt-4 hidden" 
                data-category="{{ $top->category_id }}">
                <div class="flex items-center gap-3">
                    <div class="relative flex items-center">
                        <input type="checkbox" value="{{ $top->price }}" onclick="updateUI()" 
                            class="peer h-4 w-4 cursor-pointer appearance-none rounded-md border border-gray-300 checked:bg-[#e91e63] checked:border-[#e91e63] transition-all">
                        <i data-lucide="check" class="absolute h-2.5 w-2.5 text-white opacity-0 peer-checked:opacity-100 left-[3px] pointer-events-none"></i>
                    </div>
                    
                    <div>
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-md text-gray-800">{{ $top->name }}</p>
                            <span class="text-xs font-black text-[#e91e63]">+ Rp{{ number_format($top->price, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-[10px] text-gray-400 truncate w-60">{{ $top->description }}</p>
                    </div>
                </div>
            </label>
        @endforeach
    </div>

    <div class="w-full bg-gray-50 py-8 my-6 border-y border-gray-100">
        <div class="px-4">
            <h3 class="text-[14px] font-black text-gray-900 uppercase tracking-wider mb-6 flex items-center gap-2">
                <span class="w-1 h-4 bg-[#e91e63] rounded-full"></span>informasi pesanan
            </h3>

            <div class="space-y-6">
                </div>
        </div>

        <div class="px-4 mb-2 space-y-6">
            <div class="grid grid-cols-[25px_1fr] gap-4 items-start">
                <i data-lucide="info" class="w-6 h-6 text-blue-500"></i>
                <div>
                    <h4 class="text-[13px] font-bold text-gray-900 mb-1">Kualitas Terjamin</h4>
                    <p class="text-[12px] text-gray-500 leading-relaxed">
                        Dibuat dari ikan tenggiri asli dengan bahan alami tanpa pengawet. Kami memastikan setiap porsi digoreng dadakan saat pesanan masuk agar tetap renyah dan hangat sampai di tanganmu.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-[25px_1fr] gap-4 items-start -mt-4">
                <i data-lucide="package-check" class="w-6 h-6 text-emerald-500"></i>
                <div>
                    <h4 class="text-[13px] font-bold text-gray-900 mb-1">Kemasan Aman</h4>
                    <p class="text-[12px] text-gray-500 leading-relaxed">
                        Cuko dibungkus plastik tebal anti-bocor. Untuk pempek, kami menggunakan wadah yang menjaga suhu agar tidak cepat dingin selama perjalanan.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-[25px_1fr] gap-4 items-start -mt-10">
                <i data-lucide="timer" class="w-6 h-6 text-amber-500"></i>
                <div>
                    <h4 class="text-[13px] font-bold text-gray-900 mb-1">Estimasi Penyajian</h4>
                    <p class="text-[12px] text-gray-500 leading-relaxed">
                        Proses goreng dan packing membutuhkan waktu sekitar
                        <span class="font-bold text-amber-500">10-15 menit</span>.
                        Mohon kesabarannya ya agar rasa tetap maksimal!
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Catatan yaaa 
    <div class="mb-10">
        <div class="flex items-center gap-2 mb-3">
            <i data-lucide="message-square-quote" class="w-4 h-4 text-gray-400"></i>
            <h3 class="text-sm font-bold text-gray-900">Catatan Pesanan</h3>
        </div>
        <textarea id="modalNote" rows="3" 
            placeholder="Contoh: Bang, cukonya dipisah ya..." 
            class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 text-xs text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-pink-100 focus:bg-white focus:border-pink-200 transition-all resize-none shadow-inner"></textarea>
    </div> -->

    <div class="py-8 relative overflow-hidden rounded-3xl -mt-5">
        <div class="absolute inset-0 z-0 opacity-40 pointer-events-none">
            <img src="{{ asset('assets/images/bg-waveTR.png') }}"
                class="w-full h-full object-cover">
        </div>

        <div class="relative z-10">
            <div class="flex justify-between items-center mb-4 px-5">
                <h3 class="text-[14px] font-black text-gray-900 uppercase tracking-wider">
                    Ulasan Pelanggan
                </h3>
                <span class="text-[#e91e63] text-[10px] font-bold cursor-pointer active:opacity-70">
                    Lihat Semua
                </span>
            </div>
            
            <div id="reviewContainer" class="px-5 flex overflow-x-auto gap-4 no-scrollbar pb-4 bg-transparent relative">
            </div>
        </div>
    </div>

    <div class="fixed bottom-0 left-0 right-0 bg-white/80 backdrop-blur-md border-t border-white/50 shadow-[0_-10px_20px_rgba(0,0,0,0.05)] rounded-t-3xl z-[100]">
    <div id="orderSummary" class="px-5 py-3 border-b border-pink-100/70 hidden animate-fade-in">
        <p class="text-[9px] font-black text-gray-400 mb-2 uppercase tracking-[0.15em]">Tambahan Kamu:</p>
        <div id="summaryList" class="flex flex-wrap gap-2 max-h-20 overflow-y-auto no-scrollbar">
            </div>
    </div>

    <div class="px-5 py-4 flex justify-between items-center">
        <div class="flex flex-col">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Total:</span>
            <span id="modalTotalPrice" class="text-xl font-black text-gray-900 mt-1"></span>
        </div>
        <button type="button" 
            onclick="addToCart(event)" 
            class="bg-[#e91e63] text-white px-8 py-3.5 rounded-2xl font-bold flex items-center gap-2 shadow-lg shadow-pink-200 active:scale-95 transition-transform uppercase text-xs tracking-wider">
            <i data-lucide="shopping-cart" class="w-4 h-4"></i>
            Add to Cart
        </button>
    </div>
</div>