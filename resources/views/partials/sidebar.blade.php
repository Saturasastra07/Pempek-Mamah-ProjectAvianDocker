<style>
    @keyframes gradient-flow {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    .animate-gradient {
        background-size: 200% 200%;
        animation: gradient-flow 6s ease infinite;
    }

    [x-cloak] { display: none !important; }
</style>

<div>
    <div x-show="open" 
         x-transition.opacity.duration.500ms
         @click="open = false"
         class="fixed inset-0 bg-black/60 backdrop-blur-[2px] z-[65] ">
    </div>

    <div x-show="open"
        x-cloak
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-500"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         style="clip-path: inset(0px 0px 0px -50px);"
         class="fixed right-0 top-0 h-[calc(100vh-76px)] w-[280px] max-w-[280px] bg-[#1a1a1a] text-white z-[70] shadow-[0_0_50px_rgba(0,0,0,0.5)] flex flex-col">
        
        <div class="relative overflow-hidden">
            <div class="absolute inset-0 animate-gradient bg-gradient-to-br from-[#e91e63] via-[#ff4081] to-[#880e4f]"></div>
            <div class="absolute inset-0 opacity-30 bg-[radial-gradient(circle_at_50%_50%,rgba(255,255,255,0.4),transparent_70%)]"></div>

            <div class="relative p-6 flex flex-col items-center border-b border-white/10">
            <div class="relative mb-4">
                <div x-show="open"
                    x-transition:enter="transition ease-out duration-700 delay-100"
                    x-transition:enter-start="opacity-0 scale-50 translate-x-12 -translate-y-10"
                    x-transition:enter-end="opacity-100 scale-100 translate-x-0 translate-y-0"
                    x-transition:leave="transition ease-in duration-500"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" 
                    class="w-24 h-24 rounded-full border-2 border-white/60 p-1 shadow-[0_0_25px_rgba(255,255,255,0.3)] bg-white/10 backdrop-blur-sm">
                    
                    <div class="w-full h-full rounded-full overflow-hidden">
                        @auth
                            <img id="previewImage" src="{{ auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=fff&color=e91e63' }}" 
                                class="w-full h-full object-cover">
                        @else
                            <img src="{{ asset('assets/images/mamah_melambai.png') }}" class="w-full h-full object-cover">
                        @endauth
                    </div>

                    @auth
                    <label for="photoInput" class="absolute -bottom-1 -right-1 bg-white w-7 h-7 rounded-full flex items-center justify-center shadow-lg cursor-pointer active:scale-90 transition-transform border border-pink-100 z-10">
                        <i data-lucide="camera" class="w-3.5 h-3.5 text-[#e91e63]"></i>
                        <input type="file" id="photoInput" class="hidden" accept="image/*" onchange="uploadPhoto(this)">
                    </label>
                    @endauth
                </div>
            </div>

            <h2 x-show="open"
                x-transition:enter="transition ease-out duration-500 delay-300"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="font-black text-base text-white drop-shadow-md uppercase truncate tracking-tight text-center w-full px-4">
                {{ auth()->check() ? auth()->user()->name : 'Teman Makan' }}
            </h2>

            @auth
            <p x-show="open"
                x-transition:enter="transition ease-out duration-500 delay-[350ms]"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="mt-1 text-[10px] text-white/70 font-medium tracking-widest leading-none text-center">
                {{ auth()->user()->phone }}
            </p>

            <div x-show="open"
                x-transition:enter="transition ease-out duration-500 delay-400"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="mt-3 px-3 py-1.5 bg-black/20 rounded-full border border-white/10">
                <p class="text-[8px] text-pink-200 uppercase tracking-[0.15em] font-bold leading-none"
                    x-data x-text="$store.userStore.addressText"> 
                </p>
            </div>
            @endauth
        </div></div>

        <nav class="p-4 space-y-1 flex-1 overflow-y-auto custom-scrollbar">
            <div class="pb-1">
                <p class="text-[10px] text-gray-500 font-bold px-4 uppercase tracking-widest">Customer Area</p>
            </div>
            
            <a href="{{ route('profile') }}" class="flex items-center justify-between gap-4 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all group">
                <div class="flex items-center gap-4">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                    <span class="text-xs font-semibold">Pusat Pengguna</span>
                </div>

                <i data-lucide="chevron-right" 
                    class="w-4 h-4 opacity-60 group-hover:translate-x-1 transition-all">
                </i>
            </a>

            <a href="{{ route('cart') }}" class="flex items-center justify-between px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all group">
                <div class="flex items-center gap-4">
                    <i data-lucide="shopping-cart" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                    <span class="text-xs font-semibold">Keranjang Saya</span> </div>
                <span class="global-cart-badge bg-[#e91e63] text-white text-[9px] font-black px-1.5 py-0.5 rounded-full shadow-md" 
                    style="{{ ($totalCart ?? 0) > 0 ? '' : 'display: none;' }}">
                    {{ $totalCart ?? 0 }}
                </span>

                <i data-lucide="chevron-right" 
                    class="w-4 h-4 opacity-60 group-hover:translate-x-1 transition-all">
                </i>
            </a>
            
            <a href="{{ route('order.history') }}" class="flex items-center justify-between gap-4 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all group">
                <div class="flex items-center gap-4">
                    <i data-lucide="shopping-bag" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                    <span class="text-xs font-semibold">Riwayat Pesanan</span>
                </div>

                <i data-lucide="chevron-right" 
                    class="w-4 h-4 opacity-60 group-hover:translate-x-1 transition-all">
                </i>
            </a>

            <a href="{{ route('wishlist') }}" class="flex items-center justify-between px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all group">
                <div class="flex items-center gap-4">
                    <i data-lucide="heart" class="w-4 h-4"></i>
                    <span class="text-xs font-semibold">Favorit Saya</span>
                </div>

                <i data-lucide="chevron-right" 
                    class="w-4 h-4 opacity-60 group-hover:translate-x-1 transition-all">
                </i>
            </a>

            <a href="{{ route('pusat-bantuan') }}" class="flex items-center justify-between px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all group">
                <div class="flex items-center gap-4">
                    <i data-lucide="help-circle" class="w-4 h-4"></i>
                    <span class="text-xs font-semibold">Pusat Bantuan</span>
                </div>
                
                <i data-lucide="chevron-right" 
                    class="w-4 h-4 opacity-60 group-hover:translate-x-1 transition-all">
                </i>
            </a>

            <div class="my-4 border-t border-white/5 mb-3"></div>

            <div class="pt-3 pb-1">
                <p class="text-[10px] text-gray-500 font-bold px-4 uppercase tracking-widest">Management System</p>
            </div>

            <a href="#" class="flex items-center justify-between gap-4 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all group">
                <div class="flex items-center gap-4">
                    <i data-lucide="database" class="w-4 h-4"></i>
                <span class="text-xs font-semibold">Kelola Produk</span>
                </div>

                <i data-lucide="chevron-right" 
                    class="w-4 h-4 opacity-60 group-hover:translate-x-1 transition-all">
                </i>
            </a>

            <a href="#" class="flex items-center justify-between gap-4 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all group">
                <div class="flex items-center gap-4">    
                    <i data-lucide="settings" class="w-4 h-4"></i>
                
                <span class="text-xs font-semibold">System Settings</span>
                </div>
                
                <i data-lucide="chevron-right" 
                    class="w-4 h-4 opacity-60 group-hover:translate-x-1 transition-all">
                </i>
            </a>
        </nav>

        <div class="p-6 border-t border-white/5">
            @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-4 w-full px-4 py-3 rounded-xl text-red-400 hover:bg-red-400/10 transition-all border border-red-400/20 group">
                        <i data-lucide="log-out" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                        <span class="text-[12px] font-bold uppercase tracking-wider">Keluar Akun</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="flex items-center justify-center gap-4 w-full px-4 py-3 rounded-xl text-pink-400 hover:bg-pink-400/10 transition-all border border-pink-400/20 group uppercase">
                    <span class="text-sm font-bold tracking-wider">Masuk Sekarang</span>
                </a>
            @endauth
        </div>
    </div>
</div>

<script>
function uploadPhoto(input) {
    if (input.files && input.files[0]) {
        let formData = new FormData();
        formData.append('photo', input.files[0]);
        formData.append('_token', '{{ csrf_token() }}');

        document.getElementById('previewImage').style.opacity = '0.5';

        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById('previewImage').src = e.target.result;
            document.getElementById('previewImage').style.opacity = '1';
        };
        reader.readAsDataURL(input.files[0]);

        fetch("{{ route('update.photo') }}", {
            method: "POST",
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                document.getElementById('previewImage').src = data.path;
                const globalImages = document.querySelectorAll('.global-profile-img');
                globalImages.forEach(img => {
                    img.src = data.path;
                });

                console.log('Foto Berhasil Disimpan di Database');
            }
        })
        .catch(err => {
            alert('Gagal upload foto, coba lagi ya!');
            document.getElementById('previewImage').style.opacity = '1';
        });
    }
}

document.addEventListener('alpine:init', () => {
        if (!Alpine.store('userStore')) {
            Alpine.store('userStore', {
                addressText: localStorage.getItem('globalUserAddress') || '{{ auth()->user()->district ?? "" }} {{ auth()->user()->city ?? "" }}',
                fullAddress: localStorage.getItem('globalFullAddress') || '{{ auth()->user()->full_address ?? "Alamat belum diatur" }}',
                
                updateAddress(fullStr, shortStr) {
                    this.fullAddress = fullStr;
                    this.addressText = shortStr;
                    localStorage.setItem('globalFullAddress', fullStr);
                    localStorage.setItem('globalUserAddress', shortStr);
                }
            });
        }
    });
</script>