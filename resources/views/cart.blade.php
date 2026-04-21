<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Pempek Mamah Dhani</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pink: {
                            600: '#e91e63',
                            50: '#fce4ec',
                        }
                    }
                }
            }
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }
        .accent-pink-600 { accent-color: #e91e63; }
    </style>
</head>
<body class="bg-gray-50 pb-0">
<div x-data="layoutApp()" x-cloak>
    @include('partials.sidebar')
    <div x-data="cartData()" x-cloak>
    <div class="sticky top-0 bg-white z-50 px-4 py-3 flex items-center justify-between border-b border-gray-100 shadow-sm">
        <button onclick="window.history.back()" class="text-gray-800 hover:text-[#e91e63] transition-colors">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </button>
        <h1 class="text-lg font-black text-gray-900 uppercase tracking-tight">Keranjang</h1>
        <button class="text-gray-800 hover:text-[#e91e63] transition-colors">
            <i data-lucide="share-2" class="w-5 h-5"></i>
        </button>
    </div>

    <div class="bg-white px-4 py-4 border-b border-gray-100">
        <h2 class="font-bold text-base text-gray-900 mb-3">
            Product (<span x-text="cartItems.length"></span>)
        </h2>

        <div class="relative" @click.outside="showAddressDropdown = false">
            <button @click="showAddressDropdown = !showAddressDropdown" 
                    class="flex items-center gap-2 text-gray-700 hover:bg-white/70 p-2 -ml-2 backdrop-blur-md 
                    border border-white hover:bg-white/100 rounded-lg transition-colors w-full">
                <i data-lucide="map-pin" class="w-6.5 h-6.5 text-[#e91e63] flex-shrink-0"></i>

                <div class="flex flex-col items-start flex-1 min-w-0 pr-4">
                    <span class="text-xs text-gray-400 font-medium mb-0.5">
                        Dikirim ke:
                    </span>

                    <span class="font-bold text-sm text-gray-900 truncate w-full text-left"
                        x-text="addressLabels[selectedAddressIndex].label">
                    </span>

                    <span class="text-[10px] text-gray-400 font-normal leading-tight line-clamp-2 text-left mt-0.5"
                        x-text="addressLabels[selectedAddressIndex].full_address">
                    </span>
                </div>
                <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 transition-transform duration-300" :class="showAddressDropdown ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="showAddressDropdown" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="absolute top-full left-0 right-0 bg-white 
                 border-x border-b border-gray-100 shadow-[0_15px_30px_-5px_rgba(0,0,0,0.1)] rounded-b-2xl mt-0 z-50 overflow-hidden py-1">
                
                <template x-for="(addr, index) in addressLabels" :key="addr.id">
                    <button @click="selectedAddressIndex = index; showAddressDropdown = false"
                            class="w-full text-left px-6 py-2.5 hover:bg-pink-50 flex items-center justify-between"
                            :class="index === selectedAddressIndex ? 'bg-white text-[#e91e63] font-bold' : 'text-gray-700'">
                        <span x-text="addr.label"></span>
                        <i x-show="index === selectedAddressIndex" data-lucide="check" class="w-4 h-4 text-[#e91e63]"></i>
                    </button>
                </template>

                <div class="border-t border-gray-100 my-1"></div>
                <button @click="showAddressModal = true; addressTab = 'add'; showAddressDropdown = false" 
                        class="w-full text-left px-4 py-2.5 text-gray-700 font-bold hover:bg-pink-50 flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    Tambah Alamat Baru
                </button>
            </div>
        </div>
    </div>

    <div x-show="showNoteModal" 
        class="fixed inset-0 z-[100] flex items-end justify-center bg-black/50 backdrop-blur-[2px]"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-cloak>
        
        <div @click.away="showNoteModal = false" 
            class="bg-white w-full max-w-md rounded-t-[40px] shadow-2xl transform transition-all"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0">
            
            <div class="flex justify-center pt-3">
                <div class="w-12 h-1.5 bg-gray-200 rounded-full"></div>
            </div>

            <div class="px-8 py-6">
                <div class="mb-6">
                    <h2 class="text-base font-black text-gray-900 uppercase tracking-tight" x-text="'Catatan: ' + (selectedItem?.name || '')"></h2>
                    <p class="text-xs text-gray-400 font-medium">Contoh: Cuka dipisah, jangan pakai mie, dll.</p>
                </div>

                <div class="relative">
                    <textarea 
                        x-model="tempNote"
                        class="w-full h-44 p-5 bg-gray-50 border-2 border-transparent focus:border-pink-100 rounded-[24px] text-sm text-gray-700 placeholder:text-gray-300 transition-all resize-none no-scrollbar font-semibold"
                        placeholder="Tulis pesan untuk penjual..."></textarea>
                    
                    <div class="absolute bottom-4 right-5">
                        <span class="text-[10px] font-bold px-2 py-1 bg-white/80 backdrop-blur rounded-full shadow-sm" 
                            :class="tempNote.length > 200 ? 'text-red-500' : 'text-gray-400'">
                            <span x-text="tempNote.length"></span>/200
                        </span>
                    </div>
                </div>

                <div class="mt-8 flex gap-4">
                    <button @click="showNoteModal = false" 
                            class="flex-1 py-4 text-xs font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-colors">
                        Batal
                    </button>
                    <button @click="selectedItem.note = tempNote; showNoteModal = false; updateNoteInDatabase(selectedItem.id, tempNote)"
                            class="flex-[2] bg-[#e91e63] text-white py-4 rounded-2xl text-xs font-black uppercase tracking-widest shadow-[0_10px_20px_rgba(233,30,99,0.3)] active:scale-95 transition-all">
                        Simpan Catatan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div x-show="cartItems.length === 0"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="flex flex-col items-center justify-center py-20 text-center px-6 mt-3 bg-white">
        <div class="w-24 h-24 bg-pink-50 rounded-full flex items-center justify-center mb-5">
            <i data-lucide="shopping-cart" class="w-10 h-10 text-pink-300"></i>
        </div>
        <h3 class="font-black text-gray-900 text-lg mb-1 uppercase tracking-tight">Keranjang Kosong</h3>
        <p class="text-sm text-gray-400 font-medium">Yuk, tambahkan menu pempek kesukaanmu!</p>
        <a href="{{ route('home') }}"
           class="mt-6 bg-[#e91e63] text-white px-8 py-3 rounded-md font-black text-xs uppercase tracking-widest shadow-[0_10px_20px_rgba(233,30,99,0.25)] active:scale-95 transition-all">
            Eksplor Menu
        </a>
    </div>

    <div x-show="cartItems.length > 0" class="bg-white mt-3">
        <template x-for="(item, index) in cartItems" :key="item.id">
            <div class="p-4 border-b border-gray-50 last:border-b-0 bg-white relative">
                <div class="flex items-start gap-3">
                    <div class="pt-8 flex-shrink-0">
                        <input type="checkbox" x-model="item.checked" 
                            class="w-5 h-5 rounded border-gray-300 accent-[#e91e63] cursor-pointer">
                    </div>

                    <div class="flex-shrink-0">
                        <div class="w-20 h-20 bg-gray-100 rounded-xl overflow-hidden border border-gray-100">
                            <img :src="item.image.startsWith('http') ? item.image : '/storage/' + item.image" 
                                class="w-full h-full object-cover" 
                                onerror="this.src='https://ui-avatars.com/api/?name=Pempek&background=fce4ec&color=e91e63'">
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <div class="pr-6">
                                <h3 class="font-bold text-sm text-gray-900 line-clamp-2 leading-tight" x-text="item.name"></h3>
                                
                                <template x-if="item.is_gift">
                                    <span class="bg-green-100 text-green-600 text-[8px] px-2 py-0.5 rounded-full font-black uppercase mb-1 inline-block">
                                        Hadiah Voucher
                                    </span>
                                </template>

                                <div class="mt-1.5" x-data="{ open: false }">
                                    <button @click="open = !open" 
                                            class="flex items-center gap-1.5 text-[10px] font-bold"
                                            :class="item.addons.length > 0 ? 'text-[#e91e63]' : 'text-gray-400 cursor-default'">
                                        <span x-text="item.addons.length > 0 ? 'Lihat Topping (' + item.addons.length + ')' : '+ Tambah Topping'"></span>
                                        <template x-if="item.addons.length > 0">
                                            <i data-lucide="chevron-down" class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                                        </template>
                                    </button>
                                    <div x-show="open" x-collapse>
                                        <ul class="mt-1 space-y-0.5 ml-1 border-l-2 border-pink-50 pl-2 text-[10px] text-gray-500 font-medium">
                                            <template x-for="addon in item.addons">
                                                <li class="py-0.5">+ <span x-text="typeof addon === 'object' ? (addon.name || addon) : addon"></span></li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>

                                <div class="mt-2 text-[#e91e63] font-black text-sm">
                                    Rp<span x-text="new Intl.NumberFormat('id-ID').format(calculateItemTotal(item))"></span>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-0.5 flex-shrink-0">
                                <i data-lucide="star" class="w-3 h-3 text-yellow-400 fill-yellow-400"></i>
                                <span class="text-[10px] font-bold text-gray-700" x-text="item.rating"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 flex justify-between items-center pl-8 ml-3"> 
                    <button 
                        @click="selectedItem = item; tempNote = item.note || ''; showNoteModal = true"
                        class="text-[#e91e63] text-xs font-bold hover:underline tracking-tight transition-all active:scale-95"
                    >
                        <span x-text="item.note && item.note.trim() !== '' ? 'Lihat catatan anda...' : 'Tambah pesan'"></span>
                    </button>

                    <div class="flex items-center gap-3">
                        <template x-if="!item.is_gift">
                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden h-8">
                            <button @click="item.quantity > 1 ? item.quantity-- : null" class="px-2.5 h-full text-gray-500">
                                <i data-lucide="minus" class="w-3.5 h-3.5"></i>
                            </button>
                            <input type="text" :value="item.quantity" class="w-8 h-full text-center text-xs font-bold" readonly>
                            <button @click="item.quantity++" class="px-2.5 h-full text-[#e91e63]">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                        </template>

                        <template x-if="item.is_gift">
                            <div class="flex items-center px-3 h-8 bg-gray-50 rounded-lg border border-gray-100">
                                <span class="text-[14px] font-medium text-gray-400">Bonus: 1</span>
                            </div>
                        </template>

                        <button @click="removeItem(item.id, index, item.product_id)" class="text-gray-300 hover:text-[#e91e63] p-1">
                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <div x-show="cartItems.length > 0"
        class="bg-white mt-3 px-4 py-2 flex items-center justify-between border-b border-gray-50 cursor-pointer active:bg-gray-50 transition-colors"
        @click="showVoucherModal = !showVoucherModal">

        <div class="flex items-center gap-3">
            <div class="w-10 h-10 flex items-center justify-center">
                <img src="{{ asset('assets/images/iconVoucher.png') }}" 
                    alt="voucher" 
                    class="w-8 h-8 object-contain">
            </div>
            <span class="text-xs font-light text-gray-700">Voucher Mamah Dhani</span>
        </div>
        <div class="flex items-center">
            <span 
                x-show="availableVoucherCount > 0"
                x-text="availableVoucherCount"
                class="text-gray-500 text-sm font-light px-2 py-0.5">
            </span>
            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 transition-transform duration-300"
                :class="showVoucherModal ? 'rotate-90' : ''"></i>
        </div>
    </div>

    <div x-show="showVoucherModal" x-collapse class="bg-white px-4 pb-4 space-y-2">
        <div class="border-t border-gray-50 mb-2"></div>
        @foreach($vouchers as $voucher)
        <div class="relative flex items-center justify-between p-2 rounded-md overflow-hidden transition-colors duration-300"
            :class="selectedVouchers.includes({{ $voucher->id }}) ? 'bg-pink-50/50' : 'bg-gray-100'">
            <div class="absolute -left-2 top-1/2 -translate-y-1/2 w-4 h-4 bg-white rounded-full z-10"></div>
            <div class="flex flex-col pl-2">
                <span class="text-[9px] font-black uppercase tracking-wider {{ $voucher->type == 'discount' ? 'text-[#e91e63]' : 'text-gray-800' }}">
                    {{ $voucher->title }}
                </span>
                <span class="text-[8px] text-gray-400 font-medium leading-none mt-1">{{ $voucher->description }}</span>
            </div>
            <div class="h-8 border-l border-dashed mx-4 {{ $voucher->type == 'discount' ? 'border-pink-200' : 'border-gray-200' }}"></div>
            <div class="flex items-center pl-4 pr-1">
                <button @click="
                        if(selectedVouchers.includes({{ $voucher->id }})) {
                            selectedVouchers = selectedVouchers.filter(id => id !== {{ $voucher->id }})
                        } else if(selectedVouchers.length < 2) {
                            selectedVouchers.push({{ $voucher->id }})
                        }
                        "
                    :class="selectedVouchers.includes({{ $voucher->id }}) 
                    ? 'bg-[#e91e63] text-white border-transparent' 
                    : 'bg-white border border-gray-200 text-gray-400'"
                    class="px-4 py-1.5 text-[9px] font-black rounded-lg uppercase transition-all active:scale-90">
                    <span x-text="selectedVouchers.includes({{ $voucher->id }}) ? 'Dipakai' : 'Pakai'"></span>
                </button>
            </div>
            <div class="absolute -right-2 top-1/2 -translate-y-1/2 w-4 h-4 bg-white rounded-full z-10"></div>
        </div>
        @endforeach
    </div>

    <div x-show="cartItems.length > 0"
        class="bg-white mt-3 px-4 pt-3 pb-1">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-light text-gray-900 tracking-tight">Metode Pembayaran</h3>
            <div class="flex items-center gap-1 text-gray-500 hover:text-[#e91e63]">
                <span class="text-xs font-light">Lihat Semua</span>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </div>
        </div>

        <div>
            <label class="flex items-center justify-between py-3 border-b border-gray-50 cursor-pointer">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 flex items-center justify-center">
                        <img src="{{ asset('assets/images/iconShopee.png') }}" alt="img" class="w-8 h-8 object-contain">
                    </div>
                    <div>
                        <p class="text-sm font-light text-gray-800">ShopeePay</p>
                        <p class="text-[10px] text-gray-400">Saldo Rp1.530</p>
                    </div>
                </div>
                <input type="radio" name="payment" value="shopeepay" x-model="selectedPayment" class="accent-[#e91e63] w-4 h-4">
            </label>

            <label class="flex items-center justify-between py-3 cursor-pointer">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 flex items-center justify-center">
                        <img src="{{ asset('assets/images/iconCash.png') }}" alt="img" class="w-8 h-8 object-contain">
                    </div>
                    <div>
                        <p class="text-sm font-light text-gray-800">Bayar Ditempat</p>
                        <p class="text-[10px] text-gray-400">COD</p>
                    </div>
                </div>
                <input type="radio" name="payment" value="cod" x-model="selectedPayment" class="accent-[#e91e63] w-4 h-4">
            </label>
        </div>
    </div>

    <div class="bg-white mt-3 px-4 py-4">
        <h3 class="text-sm font-light text-gray-900 tracking-tight mb-3">Rincian Pembayaran</h3>
        <div class="space-y-2.5">
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">Subtotal Pesanan</span>
                <span class="text-xs font-light text-gray-500" 
                    x-text="cartItems.length > 0 ? 'Rp' + new Intl.NumberFormat('id-ID').format(grandTotal) : '-'">
                </span>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">Subtotal Pengiriman</span>
                <span class="text-xs font-light text-gray-500" x-text="cartItems.length > 0 ? 'Rp10.000' : '-'"></span>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">Total Diskon Pengiriman</span>
                <span class="text-xs font-light text-[#e91e63]" x-text="cartItems.length > 0 ? '-Rp10.000' : '-'"></span>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">Metode Pembayaran</span>
                <span class="text-xs font-light text-gray-500" x-text="paymentLabel"></span>
            </div>

            <template x-for="id in selectedVouchers" :key="id">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Voucher Digunakan</span>
                    <span class="text-xs font-bold text-[#e91e63]"
                        x-text="vouchers.find(v => v.id === id)?.title">
                    </span>
                </div>
            </template>

            <div class="border-t border-gray-100 pt-2.5 flex justify-between items-center">
                <span class="text-sm font-light text-gray-900">Total Pembayaran</span>
                <span class="text-sm font-light text-gray-900">
                    Rp<span x-text="new Intl.NumberFormat('id-ID').format(grandTotal)"></span>
                </span>
            </div>
        </div>
    </div>

    <div class="bg-white mt-3 px-4 py-4">
        <div class="flex items-center justify-between">

            <div>
                <span class="text-sm font-semibold text-gray-800">Kirim sebagai Hadiah</span>
                <p class="text-[10px] text-gray-400 mt-0.5">Sembunyikan identitas pengirim asli</p>
            </div>

            <button @click="cartItems.length === 0 ? showEmptyCartToast() : (isDropship = !isDropship)"
                class="relative w-12 h-6 rounded-full transition-all duration-300"
                :class="isDropship ? 'bg-[#e91e63]' : 'bg-gray-200'">
                <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-300"
                    :class="isDropship ? 'translate-x-6' : 'translate-x-0'"></span>
            </button>
        </div>

        <div x-show="isDropship" x-transition class="mt-4 space-y-3">
            
            {{-- Nama Pengirim --}}
            <div>
                <label class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest ml-1">
                    Nama Pengirim <span class="text-gray-300">(tertera di paket)</span>
                </label>
                <input type="text" x-model="dropshipName"
                    placeholder="Nama yang tertera sebagai pengirim"
                    class="w-full mt-1 p-3 bg-gray-50 rounded-xl text-xs font-light outline-none border border-transparent focus:border-pink-100 transition-all">
            </div>

            <div class="border-t border-gray-50 pt-3">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Data Penerima</p>

                <div class="mb-3">
                    <label class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest ml-1">Nama Penerima</label>
                    <input type="text" x-model="dropshipReceiverName"
                        placeholder="Nama orang yang menerima hadiah"
                        class="w-full mt-1 p-3 bg-gray-50 rounded-xl text-xs font-light outline-none border border-transparent focus:border-pink-100 transition-all">
                </div>

                <div class="mb-3">
                    <label class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest ml-1">No. Telepon Penerima</label>
                    <input type="number" x-model="dropshipReceiverPhone"
                        placeholder="No. telepon untuk kurir"
                        class="w-full mt-1 p-3 bg-gray-50 rounded-xl text-xs font-light outline-none border border-transparent focus:border-pink-100 transition-all">
                </div>

                <div class="mb-3">
                    <label class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest ml-1">Alamat Lengkap</label>
                    <textarea x-model="dropshipReceiverAddress" rows="3"
                        placeholder="Alamat lengkap penerima hadiah"
                        class="w-full mt-1 p-3 bg-gray-50 rounded-xl text-xs font-light outline-none border border-transparent focus:border-pink-100 transition-all resize-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest ml-1">Kecamatan</label>
                        <input type="text" x-model="dropshipReceiverDistrict"
                            placeholder="Kecamatan"
                            class="w-full mt-1 p-3 bg-gray-50 rounded-xl text-xs font-light outline-none border border-transparent focus:border-pink-100 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest ml-1">Kota</label>
                        <input type="text" x-model="dropshipReceiverCity"
                            placeholder="Kota"
                            class="w-full mt-1 p-3 bg-gray-50 rounded-xl text-xs font-light outline-none border border-transparent focus:border-pink-100 transition-all">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="px-4 py-4 mt-1">
        <p class="text-xs text-gray-500 leading-relaxed">
            Dengan klik "Buat Pesanan", saya telah menyetujui 
            <span class="text-[#e91e63] font-bold">Syarat & Ketentuan</span> 
            yang berlaku di Pempek Mamah Dhani.
        </p>
    </div>

    <div class="bg-white px-4 py-4 pb-20">
        <div class="flex justify-end items-center gap-5 mb-3">
            <div>
                <div class="flex items-center gap-1.5">
                    <p class="text-xs text-gray-500">Total</p>
                    <p class="text-lg font-light text-[#e91e63]">
                        Rp<span x-text="new Intl.NumberFormat('id-ID').format(grandTotal)"></span>
                    </p>
                </div>

                <div class="flex justify-end items-center gap-1.5 -mt-[2px]">
                    <p x-show="totalSavings > 0" class="text-xs text-gray-500 font-light">Hemat</p>
                    <p class="text-sm font-light text-[#e91e63]">
                        Rp<span x-text="new Intl.NumberFormat('id-ID').format(totalSavings)"></span>
                    </p>
                </div>
            </div>
            <button @click="buatPesanan()"
                class="bg-[#e91e63] text-white font-black text-sm px-8 py-4 rounded-md active:scale-95 transition-all uppercase tracking-wider"
                :disabled="checkedCount === 0"
                :class="checkedCount === 0 ? 'opacity-50' : ''">
                Buat Pesanan
            </button>
        </div>
    </div>

    <div x-show="showAddressModal" 
        class="fixed inset-0 z-[110] flex items-end justify-center bg-black/60 backdrop-blur-[2px]"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-cloak>
        
        <div @click.away="showAddressModal = false" 
            class="bg-white w-full max-w-md rounded-t-[40px] shadow-2xl transform transition-all max-h-[90vh] flex flex-col"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0">
            
            <div class="flex justify-center pt-3 pb-2 sticky top-0 bg-white z-10">
                <div class="w-12 h-1.5 bg-gray-200 rounded-full"></div>
            </div>

            <div class="px-8 py-6 overflow-y-auto">
                <template x-if="addressTab === 'add'">
                    <div>
                        <div class="border-t border-gray-100 my-1"></div>
                            <button @click="showAddressModal = true; addressTab = 'list'; showAddressDropdown = false" 
                                    class="w-full text-left px-4 py-2.5 text-[#e91e63] font-bold hover:bg-pink-50 flex items-center gap-2">
                                <i data-lucide="map" class="w-4 h-4"></i>
                                Ganti / Tambah Alamat
                            </button>
                        
                        <form action="{{ route('address.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="redirect_to" value="cart"> 
                            
                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Label</label>
                                <input type="text" name="label" placeholder="Rumah / Kantor" class="w-full mt-1 p-4 bg-gray-50 rounded-2xl text-sm font-bold outline-none" required>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Penerima</label>
                                <input type="text" name="receiver_name" value="{{ auth()->user()->name }}" class="w-full mt-1 p-4 bg-gray-50 rounded-2xl text-sm font-bold outline-none" required>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">WhatsApp</label>
                                <input type="number" name="phone_number" value="{{ auth()->user()->phone }}" class="w-full mt-1 p-4 bg-gray-50 rounded-2xl text-sm font-bold outline-none" required>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Lengkap</label>
                                <textarea name="full_address" rows="3" class="w-full mt-1 p-4 bg-gray-50 rounded-2xl text-sm font-bold outline-none resize-none" required></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <input type="text" name="district" placeholder="Kecamatan" class="p-4 bg-gray-50 rounded-2xl text-sm font-bold outline-none" required>
                                <input type="text" name="city" placeholder="Kota" class="p-4 bg-gray-50 rounded-2xl text-sm font-bold outline-none" required>
                            </div>
                            <button type="submit" class="w-full mt-4 bg-[#e91e63] text-white py-5 rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-lg active:scale-95 transition-all">
                                Simpan Alamat
                            </button>
                        </form>
                    </div>
                </template>

                <template x-if="addressTab === 'list'">
                    <div>
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex-1">
                                <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight">Pilih Alamat Utama</h2>
                                <p class="text-[10px] text-gray-400 font-medium">Klik alamat untuk menjadikannya utama.</p>
                            </div>
                            <button @click="addressTab = 'add'" 
                                    class="ml-4 w-10 h-10 text-[#e91e63] rounded-2xl flex items-center justify-center bg-pink-50 hover:bg-[#e91e63] hover:text-white transition-all shadow-sm">
                                <i data-lucide="plus" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <div class="flex flex-col gap-3">
                            <template x-for="item in addressLabels" :key="item.id">
                                <div class="relative transition-all duration-150" 
                                    :style="'order: ' + (item.is_default ? 0 : 1)">
                                    
                                    <form :action="'/set-default-address/' + item.id" method="POST">
                                        @csrf
                                        <input type="hidden" name="redirect_to" value="cart">
                                        
                                        <button type="submit" 
                                            class="w-full text-left p-4 rounded-2xl border-2 transition-all flex items-center justify-between"
                                            :class="item.is_default ? 'border-[#e91e63] bg-pink-50/20' : 'border-gray-50 bg-gray-50/50 hover:border-pink-100'">
                                            
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="text-[10px] font-black uppercase tracking-widest"
                                                        :class="item.is_default ? 'text-[#e91e63]' : 'text-gray-400'"
                                                        x-text="item.label">
                                                    </span>
                                                    <template x-if="item.is_default">
                                                        <span class="bg-[#e91e63] text-white text-[8px] px-2 py-0.5 rounded-full font-black uppercase">Utama</span>
                                                    </template>
                                                </div>
                                                <h4 class="text-sm font-bold text-gray-800 truncate" x-text="item.receiver_name"></h4>
                                                <p class="text-[10px] text-gray-500 line-clamp-1" x-text="item.full_address"></p>
                                            </div>

                                            <div @click.stop="addressTab = 'edit'; currentAddress = item" 
                                                class="p-2.5 bg-white/70 backdrop-blur-md shadow-md rounded-xl text-gray-400 hover:text-[#e91e63] border border-white transition-colors cursor-pointer ml-4">
                                                <i data-lucide="pencil" class="w-4 h-4"></i>
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-if="addressTab === 'edit'">
                    <div>
                        <div class="flex items-center gap-2 mb-6">
                            <button @click="addressTab = 'list'" class="text-gray-400"><i data-lucide="arrow-left" class="w-5 h-5"></i></button>
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight">Edit Alamat</h2>
                        </div>

                        <form :action="'/update-address/' + currentAddress.id" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="redirect_to" value="cart"> 

                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Label</label>
                                <input type="text" name="label" :value="currentAddress.label" class="w-full mt-1 p-4 bg-gray-50 rounded-2xl text-sm font-bold outline-none" required>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Penerima</label>
                                <input type="text" name="receiver_name" :value="currentAddress.receiver_name" class="w-full mt-1 p-4 bg-gray-50 rounded-2xl text-sm font-bold outline-none" required>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">WhatsApp</label>
                                <input type="number" name="phone_number" :value="currentAddress.phone_number" class="w-full mt-1 p-4 bg-gray-50 rounded-2xl text-sm font-bold outline-none" required>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Lengkap</label>
                                <textarea name="full_address" rows="3" x-text="currentAddress.full_address" class="w-full mt-1 p-4 bg-gray-50 rounded-2xl text-sm font-bold outline-none resize-none" required></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <input type="text" name="district" :value="currentAddress.district" placeholder="Kecamatan" class="p-4 bg-gray-50 rounded-2xl text-sm font-bold outline-none" required>
                                <input type="text" name="city" :value="currentAddress.city" placeholder="Kota" class="p-4 bg-gray-50 rounded-2xl text-sm font-bold outline-none" required>
                            </div>
                            <button type="submit" class="w-full mt-4 bg-[#e91e63] text-white py-5 rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-lg active:scale-95 transition-all">
                                Update Alamat
                            </button>
                        </form>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <div x-show="showToast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-24 right-4 z-[200] bg-gray-900 text-white text-xs font-semibold px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-2 max-w-[260px]"
        x-cloak>
        <i data-lucide="shopping-cart" class="w-4 h-4 text-pink-400 flex-shrink-0"></i>
        <span x-text="toastMessage"></span>
    </div>

     @include('partials.bottom-nav')
</div>

<script src="{{ asset('js/layout-app.js') }}"></script>

<script>
    function cartData() {
        return {
            addressLabels: @json($allAddresses) || [],
            selectedAddressIndex: 0,
            showAddressDropdown: false,
            showAddressModal: false,
            addressTab: 'list',
            currentAddress: {},
            showVoucherModal: false,
            selectedVouchers: [],
            showNoteModal: false,
            isDropship: false,
            dropshipName: '',
            dropshipPhone: '',
            dropshipReceiverName: '',
            dropshipReceiverPhone: '',
            dropshipReceiverAddress: '',
            dropshipReceiverDistrict: '',
            dropshipReceiverCity: '',
            selectedItem: null,
            selectedPayment: 'shopeepay',
            vouchers: @json($vouchers),
            tempNote: '',
            showToast: false,
            toastMessage: '',
            cartItems: @json($dbItems).map(item => ({
                ...item,
                basePrice: item.basePrice || item.price || 0,
                discountPrice: item.discountPrice ?? null,
                checked: true,
                addons: Array.isArray(item.addons) ? item.addons : (typeof item.addons === 'string' ? JSON.parse(item.addons) : []),
                note: item.notes || ''
            })),

            init() {
                this.$nextTick(() => { 
                    if (window.lucide) lucide.createIcons(); 
                    console.log('item pertama:', this.cartItems[0]);
                });

                this.$watch('selectedVouchers', (value) => {
                    const voucher = this.vouchers.find(v => value.includes(v.id) && v.code === 'TEKWANFREE');

                    if (voucher) {
                        this.handleGiftProduct();
                    } else {
                        this.cartItems = this.cartItems.filter(item => !item.is_gift);
                    }
                });
            },

            get totalSavings() {
                return this.cartItems
                    .filter(item => item.checked && !item.is_gift)
                    .reduce((sum, item) => {
                        const original = item.basePrice || item.base_price || item.price || 0;
                        const discounted = item.discountPrice || item.discount_price || original;
                        return sum + ((original - discounted) * item.quantity);
                    }, 0);
            },

            get paymentLabel() {
                switch(this.selectedPayment) {
                    case 'shopeepay':
                        return 'ShopeePay';
                    case 'cod':
                        return 'Bayar Ditempat';
                    default:
                        return '-';
                }
            },

            get availableVoucherCount() {
                return this.vouchers.length;
            },

            async updateNoteInDatabase(cartId, note) {
                try {
                    const response = await fetch(`/cart/update-note/${cartId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ note: note })
                    });
                } catch (error) { console.error("Error note:", error); }
            },

           async removeItem(cartId, index, productId) {
                if(!confirm('Hapus item ini?')) return;
                this.cartItems.splice(index, 1);
                const response = await fetch(`/cart/remove/${cartId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if(result.success) {
                    this.updateGlobalBadges(result.new_count);

                    let inCart = JSON.parse(localStorage.getItem('inCartProducts') || '[]');
                    inCart = inCart.filter(id => id !== Number(productId));
                    localStorage.setItem('inCartProducts', JSON.stringify(inCart));
                }
                this.$nextTick(() => { lucide.createIcons(); });
            },

            updateGlobalBadges(count) {
                document.querySelectorAll('.global-cart-badge').forEach(badge => {
                    badge.innerText = count;
                    badge.style.display = count > 0 ? 'block' : 'none';
                });
            },

            calculateItemTotal(item) {
                let total = item.discountPrice || item.basePrice || 0;
                if (item.addons) {
                    item.addons.forEach(addon => {
                        if (typeof addon === 'object') total += (addon.price || 0);
                    });
                }
                return total;
            },

            get grandTotal() {
                let total = this.cartItems
                    .filter(item => item.checked && !item.is_gift)
                    .reduce((sum, item) => sum + (this.calculateItemTotal(item) * item.quantity), 0);

                this.selectedVouchers.forEach(id => {
                    const voucher = this.vouchers.find(v => v.id === id);
                    if (voucher && voucher.type === 'discount') {
                        total -= (total * (voucher.discount_value / 100));
                    }
                });

                return total > 0 ? total : 0;
            },

            handleGiftProduct() {
                const hasGift = this.cartItems.find(item => item.is_gift === true);
                if (!hasGift) {
                    this.cartItems.push({
                        id: 'gift_tekwan_' + Date.now(),
                        name: 'Porsi Tekwan (voucher)',
                        image: 'tekwanRL.jpg', 
                        basePrice: 0,
                        quantity: 1,
                        addons: [],
                        checked: true,
                        is_gift: true
                    });
                    this.$nextTick(() => { lucide.createIcons(); });
                }
            },

            async buatPesanan() {
                if (this.checkedCount === 0) return;

                const subtotalSebelumDiskon = this.cartItems
                    .filter(item => item.checked && !item.is_gift)
                    .reduce((sum, item) => sum + (this.calculateItemTotal(item) * item.quantity), 0);

                const payload = {
                    delivery_mode:  'delivery',
                    address_id:     this.isDropship ? null : this.addressLabels[this.selectedAddressIndex]?.id ?? null,
                    payment_method: this.selectedPayment,
                    voucher_ids:    this.selectedVouchers,
                    discount:       subtotalSebelumDiskon - this.grandTotal,
                    is_dropship:    this.isDropship,
                    dropship_name:  this.isDropship ? this.dropshipName : null,
                    dropship_receiver_name:     this.isDropship ? this.dropshipReceiverName : null,
                    dropship_receiver_phone:    this.isDropship ? this.dropshipReceiverPhone : null,
                    dropship_receiver_address:  this.isDropship ? this.dropshipReceiverAddress : null,
                    dropship_receiver_district: this.isDropship ? this.dropshipReceiverDistrict : null,
                    dropship_receiver_city:     this.isDropship ? this.dropshipReceiverCity : null,
                };

                try {
                    const res = await fetch('/order/store', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(payload)
                    });

                    if (!res.ok) {
                        const text = await res.text();
                        console.error('Server error:', text);
                        alert('Terjadi error server: ' + res.status);
                        return;
                    }

                    const result = await res.json();
                    if (result.success) {
                        window.location.href = `/order/${result.order_id}/status`;
                    } else {
                        alert(result.message || 'Gagal membuat pesanan');
                    }
                } catch (err) {
                    console.error('Fetch error:', err);
                    alert('Koneksi gagal, coba lagi.');
                }
            },

            get checkedCount() {
                return this.cartItems.filter(item => item.checked).length;
            },

            showEmptyCartToast() {
                this.toastMessage = 'Tambahkan produk ke keranjang terlebih dahulu';
                this.showToast = true;
                setTimeout(() => { this.showToast = false; }, 3000);
            },
        }
    }

    document.addEventListener('alpine:initialized', () => {
        lucide.createIcons();
    });
</script>

</body>
</html>