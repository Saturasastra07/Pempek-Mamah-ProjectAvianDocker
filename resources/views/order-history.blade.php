<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Riwayat Pesanan - Pempek Mamah Dhani</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fdfaf9; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="pb-24">
    <div x-data="layoutApp()" x-cloak>
        @include('partials.sidebar')

    <div class="max-w-md mx-auto min-h-screen">
        <nav class="flex items-center justify-between px-6 pt-10 pb-6 sticky top-0 z-50 bg-[#fdfaf9]/90 backdrop-blur-md">
            <button onclick="window.history.back()" class="w-10 h-10 flex items-center justify-center text-gray-800 hover:text-[#e91e63] transition-colors">
                <i data-lucide="arrow-left" class="w-6 h-6"></i>
            </button>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Riwayat Pesanan</h1>
            <div class="w-10"></div>
        </nav>

        <div class="px-5 space-y-4">
            @forelse($orders as $order)
            @php
                $items = $order->items;
                $visibleItems = $items->take(4);
                $remainingCount = max(0, $items->count() - 4);
            @endphp

            <div onclick="window.location.href='/order/{{ $order->id }}/status'" 
                class="bg-white relative rounded-3xl overflow-hidden p-3 pb-[26px] flex gap-5 shadow-[0_4px_20px_-2px_rgba(0,0,0,0.08)] cursor-pointer active:scale-[0.98] transition-transform">
                
                {{-- GRID FOTO --}}
                <div class="w-28 h-28 rounded-2xl overflow-hidden shrink-0 relative">
                    @if($visibleItems->count() === 1)
                        <img src="{{ asset('assets/images/' . ($visibleItems[0]->product?->image ?? 'default.jpg')) }}" 
                             class="w-full h-full object-cover">

                    @elseif($visibleItems->count() === 2)
                        <div class="grid grid-cols-2 gap-0.5 w-full h-full">
                            @foreach($visibleItems as $item)
                            <img src="{{ asset('assets/images/' . ($item->product?->image ?? 'default.jpg')) }}" 
                                 class="w-full h-full object-cover">
                            @endforeach
                        </div>

                    @elseif($visibleItems->count() === 3)
                        <div class="grid grid-cols-2 gap-0.5 w-full h-full">
                            <img src="{{ asset('assets/images/' . ($visibleItems[0]->product?->image ?? 'default.jpg')) }}" 
                                 class="w-full h-full object-cover row-span-2">
                            <img src="{{ asset('assets/images/' . ($visibleItems[1]->product?->image ?? 'default.jpg')) }}" 
                                 class="w-full h-full object-cover">
                            <img src="{{ asset('assets/images/' . ($visibleItems[2]->product?->image ?? 'default.jpg')) }}" 
                                 class="w-full h-full object-cover">
                        </div>

                    @else
                        <div class="grid grid-cols-2 gap-0.5 w-full h-full">
                            @foreach($visibleItems->take(3) as $item)
                            <img src="{{ asset('assets/images/' . ($item->product?->image ?? 'default.jpg')) }}" 
                                 class="w-full h-full object-cover">
                            @endforeach
                            {{-- Slot ke-4: foto + overlay sisa --}}
                            <div class="relative w-full h-full">
                                <img src="{{ asset('assets/images/' . ($visibleItems[3]->product?->image ?? 'default.jpg')) }}" 
                                     class="w-full h-full object-cover">
                                @if($remainingCount > 0)
                                <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                    <span class="text-white font-semibold text-sm -mt-2">+{{ $remainingCount }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- TANGGAL (posisi bawah kiri seperti wishlist) --}}
                <div class="absolute bottom-2 left-3 bg-white rounded-tl-[18px] pt-2 pb-1 pl-2 flex items-center z-10">
                    <div class="absolute bottom-0 -left-3 w-3 h-3 overflow-hidden pointer-events-none">
                        <div class="w-full h-full bg-white rounded-br-full shadow-[4px_4px_0_4px_white]"></div>
                    </div>
                    <div class="bg-[#e91e63] bg-opacity-10 text-[#e91e63] rounded-full px-3 py-1.5 flex items-center gap-1.5 shadow-sm">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        <span class="text-[10px] font-bold tracking-wide">
                            {{ $order->created_at->translatedFormat('d M Y') }}
                        </span>
                    </div>
                </div>

                {{-- DETAIL KANAN --}}
                <div class="flex-1 flex flex-col justify-start relative min-w-0 py-1">
                    
                    {{-- Kode Order --}}
                    <h3 class="text-xs font-black text-gray-700 leading-tight tracking-tight">
                        {{ $order->order_code }}
                    </h3>

                    <div class="mt-1.5 space-y-0.5">
                        {{-- Pembayaran --}}
                        <p class="text-[11px] text-gray-400">
                            Pembayaran: 
                            <span class="font-semibold text-gray-600">
                                @php
                                    $paymentLabels = [
                                        'shopeepay' => 'ShopeePay',
                                        'cod'       => 'COD',
                                        'bank'      => 'COD',
                                    ];
                                @endphp
                                {{ $paymentLabels[$order->payment_method] ?? ucfirst($order->payment_method) }}
                            </span>
                        </p>

                        {{-- Voucher --}}
                        <p class="text-[11px] text-gray-400">
                            Voucher: 
                            @if($order->voucher_ids && count($order->voucher_ids) > 0)
                                @foreach($order->voucher_ids as $vid)
                                    @php $v = \App\Models\Voucher::find($vid); @endphp
                                    @if($v)
                                    <span class="font-semibold text-[#e91e63]">{{ $v->title }}{{ !$loop->last ? ', ' : '' }}</span>
                                    @endif
                                @endforeach
                            @else
                                <span class="font-semibold text-[#e91e63]">-</span>
                            @endif
                        </p>

                        {{-- Pengiriman --}}
                        <p class="text-[11px] text-gray-400">
                            Pengiriman: 
                            <span class="font-semibold text-gray-600">
                                {{ $order->delivery_mode === 'pickup' ? 'Ambil Sendiri' : 'Delivery' }}
                            </span>
                        </p>

                        {{-- Status --}}
                        <p class="text-[11px] text-gray-400">
                            Status: 
                            <span class="font-semibold
                                @php
                                    $statusLabels = [
                                        'pending'     => ['label' => 'Menunggu',    'color' => 'text-yellow-500'],
                                        'confirmed'   => ['label' => 'Dikonfirmasi','color' => 'text-blue-500'],
                                        'preparing'   => ['label' => 'Diproses',     'color' => 'text-orange-500'],
                                        'on_delivery' => ['label' => 'Dikirim',     'color' => 'text-purple-500'],
                                        'delivered'   => ['label' => 'Sukses',      'color' => 'text-green-500'],
                                        'cancelled'   => ['label' => 'Dibatalkan',  'color' => 'text-red-500'],
                                    ];
                                    $s = $statusLabels[$order->status] ?? ['label' => ucfirst($order->status), 'color' => 'text-gray-600'];
                                @endphp
                                {{ $s['color'] }}">
                                {{ $s['label'] }}
                            </span>
                        </p>
                    </div>

                    {{-- 
                    <div class="font-semibold text-md text-gray-700 tracking-tight mt-auto">
                        Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                    </div>
                    --}}
                </div>

                {{-- TOMBOL HAPUS --}}
                <div x-data="{ deleting: false }">
                    <button @click.stop.prevent="
                        if(!confirm('Hapus riwayat pesanan ini?')) return;
                        deleting = true;
                        fetch('/order/{{ $order->id }}/delete', {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json'
                            }
                        }).then(r => r.json()).then(res => {
                            if(res.success) $el.closest('.bg-white').remove();
                        }).finally(() => deleting = false)
                    "
                    :class="deleting ? 'opacity-50 cursor-not-allowed' : ''"
                    class="absolute bottom-3 right-3 bg-gray-100 text-gray-400 hover:bg-red-500 hover:text-white transition-colors rounded-full p-2 z-20">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>

            </div>
            @empty
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-24 h-24 bg-pink-50 rounded-full flex items-center justify-center mb-4">
                        <i data-lucide="receipt-text" class="w-10 h-10 text-pink-300"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg mb-1">Belum ada pesanan</h3>
                    <p class="text-sm text-gray-400">Yuk, pesan pempek kesukaanmu!</p>
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
<script>lucide.createIcons();</script>
</body>
</html>