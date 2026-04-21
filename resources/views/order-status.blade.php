<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Status Pesanan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif;
            cursor: pointer;
        }
        #map {
            height: 280px;
            width: 100%;
            border-radius: 20px;
            z-index: 0;
        }
        [x-cloak] {
            display: none !important;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }
        .glow-effect { position: relative; overflow: hidden; }
        .glow-effect::after {
            content: "";
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: linear-gradient(to right, rgba(255,255,255,0) 40%, rgba(255,255,255,0.4) 50%, rgba(255,255,255,0) 60%);
            animation: shimmer 3s infinite;
            transform: rotate(45deg);
            z-index: 15;
        }
        
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 pb-10 cursor-pointer">

    <div class="sticky top-0 bg-white z-50 px-4 py-4 flex items-center justify-between border-b border-gray-100 shadow-sm">
        <a href="{{ route('order.history') }}" class="text-gray-800">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </a>
        <h1 class="text-base font-black text-gray-900 uppercase tracking-tight">Status Pesanan</h1>
        <a href="{{ route('pusat-bantuan')}}" class="text-gray-500 hover:text-[#e91e63] transition-colors">
            <i data-lucide="headphones" class="w-6 h-6"></i>
        </a>
    </div>

    <div class="bg-white px-4 py-4 mt-3 mx-4 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-1">
            <span class="text-xs text-gray-400">Kode Pesanan</span>
            <span class="text-xs font-black text-gray-800">{{ $order->order_code }}</span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-xs text-gray-400">Waktu Pesan</span>
            <span class="text-xs text-gray-600">{{ $order->created_at->format('d M Y, H:i') }}</span>
        </div>
    </div>

    {{-- PETA TRACKING --}}
    @if($order->status === 'on_delivery' && $order->delivery_mode === 'delivery')
    <div class="mt-3">
        <div class="bg-white shadow-sm border border-gray-100 overflow-hidden relative">

            <div class="px-4 py-4">
                <h3 class="text-md font-black text-gray-700 tracking-tight">Lagi Otw ke Tempat Kamu</h3>
            </div>

            <div id="map" style="height: 520px; width: 100%; position: relative; border-radius: 0;">
                
            <div id="eta-badge" 
                style="position: absolute; top: 12px; left: 12px; z-index: 1000;"
                class="bg-black/50 backdrop-blur-sm text-white rounded-2xl flex items-center gap-1.5 px-3 py-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span id="eta-text" class="text-[11px] font-bold">Menghitung...</span>
            </div>

            <button onclick="focusCourier()" 
                style="position: absolute; bottom: 30px; right: 18px; z-index: 1000;"
                class="w-9 h-9 bg-white shadow-md rounded-xl flex items-center justify-center text-[#e91e63] active:scale-90 transition-all">
                <i data-lucide="navigation" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="px-4 py-3 border-t border-gray-50">
            <p class="text-[10px] text-gray-400">Tujuan pengiriman</p>
            <p class="text-xs font-bold text-gray-700 line-clamp-2">
                @if($order->is_dropship)
                    {{ $order->dropship_receiver_address }}, {{ $order->dropship_receiver_district }}, {{ $order->dropship_receiver_city }}
                @else
                    {{ $order->address?->full_address ?? 'Alamat tidak tersedia' }}
                @endif
            </p>
        </div>

        <p class="text-[10px] text-gray-400 text-center pb-3">Peta diperbarui otomatis</p>
    </div>
    @endif

    {{-- STATUS TRACKER --}}
    @if($order->status === 'cancelled')
    <div class="bg-white px-4 py-8 mt-3 mx-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center text-center">
        <div class="glow-effect w-48 h-48">
            <img src="{{ asset('assets/images/mamah_selamat(1).png') }}" 
                alt="Pesanan Dibatalkan" 
                class="w-full h-full object-contain mb-4">
        </div>
        <h3 class="text-base font-black text-gray-800 uppercase tracking-tight">Pesanan Dibatalkan</h3>
        <p class="text-xs text-gray-400 mt-2 leading-relaxed max-w-xs">
            Pesanan kamu dengan kode <span class="font-bold text-gray-600">{{ $order->order_code }}</span> 
            telah dibatalkan. Jika ada pertanyaan, Anda dapat menghubungi kami 
            <a href="#" class="text-blue-400 underline">disini</a>.
        </p>
        <a href="/" class="mt-6 bg-[#e91e63] text-white text-xs font-black uppercase tracking-widest px-8 py-3 rounded-xl shadow-md active:scale-95 transition-all">
            Kembali Belanja
        </a>
    </div>

    @elseif($order->status === 'delivered')
    <div class="bg-white px-4 py-8 mt-3 mx-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center text-center">
        <div class="glow-effect w-48 h-48">
            <img src="{{ asset('assets/images/mamah_melambai.png') }}" 
                alt="Pesanan Sampai" 
                class="w-full h-full object-contain mb-4">
        </div>
        <h3 class="text-base font-black text-gray-800 uppercase tracking-tight">Pesanan Sudah Sampai!</h3>
        <p class="text-xs text-gray-400 mt-2 leading-relaxed max-w-xs">
            Pesanan kamu dengan kode <span class="font-bold text-gray-600">{{ $order->order_code }}</span> 
            telah sampai. Selamat menikmati, terima kasih sudah pesan di Pempek Mamah Dhani!
        </p>
        <a href="{{ route('order.receipt', $order->id) }}" class="mt-6 bg-[#e91e63] text-white text-xs font-black uppercase tracking-widest px-8 py-3 rounded-xl shadow-md active:scale-95 transition-all">
            Lihat Struk
        </a>
    </div>

    {{-- tracking pesanan --}}

    @else
    <div x-data="{ 
        isOnDelivery: {{ $order->status === 'on_delivery' ? 'true' : 'false' }},
        expanded: false
    }" 
    x-init="$nextTick(() => lucide.createIcons())"
    class="bg-white px-4 py-5 mt-3 mx-4 rounded-2xl shadow-sm border border-gray-100">

        <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-4">Tracking Pesanan</h3>

        @php
            $steps = [
                ['key' => 'pending',     'label' => 'Pesanan Masuk',    'icon' => 'shopping-bag'],
                ['key' => 'confirmed',   'label' => 'Dikonfirmasi',     'icon' => 'check-circle'],
                ['key' => 'preparing',   'label' => 'Sedang Dimasak',   'icon' => 'flame'],
                ['key' => 'on_delivery', 'label' => 'Dalam Pengiriman', 'icon' => 'bike'],
                ['key' => 'delivered',   'label' => 'Sampai!',          'icon' => 'home'],
            ];
            $statusOrder = ['pending','confirmed','preparing','on_delivery','delivered'];
            $currentIndex = array_search($order->status, $statusOrder);
        @endphp

        <template x-if="isOnDelivery">
            <div>
                <div class="flex items-start gap-3" x-show="!expanded">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center bg-[#e91e63] shadow-lg shadow-pink-200">
                            <i data-lucide="bike" class="w-4 h-4 text-white"></i>
                        </div>
                    </div>
                    <div class="pb-2">
                        <p class="text-sm font-black text-[#e91e63]">Dalam Pengiriman</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">Sedang dalam proses...</p>
                    </div>
                </div>

                <div x-show="expanded"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-3"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-3"
                    class="mt-1">

                    @foreach($steps as $i => $step)
                    @php $done = $i <= $currentIndex; $active = $step['key'] === 'on_delivery'; @endphp

                    <div class="flex items-start gap-3">

                        <div class="flex flex-col items-center">

                            @if($i > 0)
                                <div class="w-0.5 h-3 {{ $i <= $currentIndex ? 'bg-pink-200' : 'bg-gray-100' }}"></div>
                            @endif

                            <div class="w-8 h-8 rounded-full flex items-center justify-center
                                {{ $active ? 'bg-[#e91e63] shadow-lg shadow-pink-200' : ($done ? 'bg-pink-100' : 'bg-gray-100') }}">
                                <i data-lucide="{{ $step['icon'] }}" 
                                    class="w-4 h-4 {{ $active ? 'text-white' : ($done ? 'text-[#e91e63]' : 'text-gray-300') }}"></i>
                            </div>
                            @if(!$loop->last)
                            <div class="w-0.5 h-6 {{ $done ? 'bg-pink-200' : 'bg-gray-100' }} my-1"></div>
                            @endif
                        </div>
                        <div class="pb-3 pt-3">
                            <p class="text-sm {{ $active ? 'font-black text-[#e91e63]' : ($done ? 'font-medium text-gray-700' : 'font-medium text-gray-300') }}">
                                {{ $step['label'] }}
                            </p>
                            @if($active)
                            <p class="text-[10px] text-gray-400 mt-0.5">Sedang dalam proses...</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="flex justify-center mt-2">
                    <button @click="expanded = !expanded; $nextTick(() => lucide.createIcons())"
                        class="flex items-center gap-1 text-gray-400 hover:text-[#e91e63] transition-colors py-1 px-3">
                        <i data-lucide="chevron-down" 
                            class="w-4 h-4 transition-transform duration-300"
                            :class="expanded ? 'rotate-180' : ''"></i>
                    </button>
                </div>
            </div>
        </template>

        <template x-if="!isOnDelivery">
            <div class="flex flex-col gap-0">
                @foreach($steps as $i => $step)
                @php $done = $i <= $currentIndex; $active = $i === $currentIndex; @endphp
                <div class="flex items-start gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center transition-all
                            {{ $active ? 'bg-[#e91e63] shadow-lg shadow-pink-200' : ($done ? 'bg-pink-100' : 'bg-gray-100') }}">
                            <i data-lucide="{{ $step['icon'] }}" 
                                class="w-4 h-4 {{ $active ? 'text-white' : ($done ? 'text-[#e91e63]' : 'text-gray-300') }}"></i>
                        </div>
                        @if(!$loop->last)
                        <div class="w-0.5 h-6 {{ $done ? 'bg-pink-200' : 'bg-gray-100' }} my-1"></div>
                        @endif
                    </div>
                    <div class="pb-4">
                        <p class="text-sm font-{{ $active ? 'black' : 'medium' }} 
                            {{ $active ? 'text-[#e91e63]' : ($done ? 'text-gray-700' : 'text-gray-300') }}">
                            {{ $step['label'] }}
                        </p>
                        @if($active)
                        <p class="text-[10px] text-gray-400 mt-0.5">Sedang dalam proses...</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </template>
    </div>
    @endif

@if($order->status === 'on_delivery' && $order->delivery_mode === 'delivery')
<script>
    const adminLat = -7.089195;
    const adminLng = 110.304251;

    const courierLat = {{ $order->courier_lat ?? '-7.089195' }};
    const courierLng = {{ $order->courier_lng ?? '110.304251' }};

    @if($order->is_dropship)
        const userLat = {{ $order->dropship_receiver_lat ?? '-7.0700' }};
        const userLng = {{ $order->dropship_receiver_lng ?? '110.3200' }};
    @else
        const userLat = {{ $order->address?->lat ?? '-7.0700' }};
        const userLng = {{ $order->address?->lng ?? '110.3200' }};
    @endif

    const map = L.map('map', {
        zoomControl: false,
        attributionControl: true
    }).setView([courierLat, courierLng], 14);

    setTimeout(() => { map.invalidateSize(); }, 300);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    const homeIcon = L.divIcon({
        html: `<div style="
            width:38px;
            height:38px;
            border-radius:50%;
            overflow:hidden;
            box-shadow:0 4px 12px rgba(16,185,129,0.4);
        ">
            <img src="/assets/images/iconLocation.png" 
                style="width:100%;height:100%;object-fit:contain;">
        </div>`,
        className: '',
        iconAnchor: [18, 18]
    });
    
    L.marker([userLat, userLng], {icon: homeIcon})
        .addTo(map).bindPopup('Lokasi kamu');

    // Fokus ke kurir
    function focusCourier() {
        map.setView(courierMarker.getLatLng(), 15, { animate: true });
    }

    async function drawRoute() {
        try {
            const res = await fetch(
                `https://router.project-osrm.org/route/v1/driving/${courierLng},${courierLat};${userLng},${userLat}?overview=full&geometries=geojson`
            );
            const data = await res.json();

            if (data.code === 'Ok') {
                const route = data.routes[0];
                const coords = route.geometry.coordinates.map(c => [c[1], c[0]]);
                
                const routeLine = L.polyline(coords, {
                    color: '#e91e63',
                    weight: 4,
                    opacity: 0.8
                }).addTo(map);

                map.fitBounds(routeLine.getBounds(), { padding: [40, 40] });

                const durasiMenit = Math.ceil(route.duration / 60);
                const jarakKm = (route.distance / 1000).toFixed(1);
                
                let etaText = '';
                if (durasiMenit < 60) {
                    etaText = `~${durasiMenit} menit • ${jarakKm} km`;
                } else {
                    const jam = Math.floor(durasiMenit / 60);
                    const menit = durasiMenit % 60;
                    etaText = `~${jam}j ${menit}m • ${jarakKm} km`;
                }

                document.getElementById('eta-text').innerText = durasiMenit < 60 
                    ? `${durasiMenit} menit` 
                    : `${Math.floor(durasiMenit/60)}j ${durasiMenit%60}m`;
            }
        } catch(e) {
            const routeLine = L.polyline([
                [courierLat, courierLng],
                [userLat, userLng]
            ], { color: '#e91e63', weight: 4, opacity: 0.7 }).addTo(map);
            map.fitBounds(routeLine.getBounds(), { padding: [40, 40] });
            document.getElementById('eta-badge').innerText = 'Dalam perjalanan';
        }
    }

    drawRoute();

    setInterval(async () => {
        const res = await fetch('/order/{{ $order->id }}/courier-location');
        const data = await res.json();
        if (data.lat && data.lng) {
            const prevLatLng = courierMarker.getLatLng();
            const bearing = getBearing(prevLatLng.lat, prevLatLng.lng, data.lat, data.lng);
            
            courierMarker.setLatLng([data.lat, data.lng]);
            updateCourierBearing(bearing);
            drawRoute();
        }
    }, 5000);

    function getBearing(lat1, lng1, lat2, lng2) {
        const toRad = d => d * Math.PI / 180;
        const toDeg = r => r * 180 / Math.PI;
        const dLng = toRad(lng2 - lng1);
        const y = Math.sin(dLng) * Math.cos(toRad(lat2));
        const x = Math.cos(toRad(lat1)) * Math.sin(toRad(lat2)) -
                Math.sin(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.cos(dLng);
        return (toDeg(Math.atan2(y, x)) + 360) % 360;
    }

    function createCourierIcon(bearing = 0) {
        const needsMirror = bearing > 90 && bearing < 270;
        return L.divIcon({
            html: `<div id="courier-icon-wrapper" style="
                width:38px;
                height:38px;
                display:flex;
                align-items:center;
                justify-content:center;
                transform: rotate(${bearing}deg);
                transition: transform 0.5s ease;
                filter: drop-shadow(0 4px 8px rgba(233,30,99,0.4));
            ">
                <img src="{{ asset('assets/images/iconMotor.png') }}" 
                    id="courier-icon-img"
                    style="width:38px;height:38px;object-fit:contain;
                    transform: scaleX(${needsMirror ? -1 : 1});
                    transition: transform 0.3s ease;">
            </div>`,
            className: '',
            iconSize: [38, 38],
            iconAnchor: [19, 19]
        });
    }

    function updateCourierBearing(bearing) {
        const wrapper = document.getElementById('courier-icon-wrapper');
        const img = document.getElementById('courier-icon-img');
        if (wrapper) wrapper.style.transform = `rotate(${bearing}deg)`;
        if (img) img.style.transform = `scaleX(${bearing > 90 && bearing < 270 ? -1 : 1})`;
    }

    let courierMarker = L.marker([courierLat, courierLng], { icon: createCourierIcon(0) })
        .addTo(map).bindPopup('Kurir dalam perjalanan'); 

</script>
@endif

{{-- ITEM PESANAN --}}
<div class="bg-white px-4 py-4 mt-3 mx-4 rounded-2xl shadow-sm border border-gray-100">
    <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-3">Pesanan Kamu</h3>
    @foreach($order->items as $item)
    <div class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
        <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
            <img src="{{ asset('assets/images/' . $item->product?->image) }}" class="w-full h-full object-cover"
                onerror="this.src='https://ui-avatars.com/api/?name=P&background=fce4ec&color=e91e63'">
        </div>
        <div class="flex-1">
            <p class="text-sm font-bold text-gray-800">{{ $item->product_name }}</p>
            <p class="text-xs text-gray-400">x{{ $item->quantity }}</p>
        </div>
        <p class="text-sm font-semibold text-[#e91e63]">
            Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}
        </p>
    </div>
    @endforeach
</div>

{{-- RINCIAN PEMESANAN --}}
<div class="bg-white px-4 py-4 mt-3 mx-4 rounded-2xl shadow-sm border border-gray-100">
    <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-3">Rincian Pemesanan</h3>
    
    <div class="space-y-2">
        <div class="flex justify-between items-center">
            <span class="text-xs text-gray-500">Subtotal Pesanan</span>
            <span class="text-xs text-gray-700">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
        </div>

        <div class="flex justify-between items-center">
            <span class="text-xs text-gray-500">Subtotal Pengiriman</span>
            <span class="text-xs text-gray-700">
                {{ $order->shipping_cost == 0 ? 'Gratis' : 'Rp' . number_format($order->shipping_cost, 0, ',', '.') }}
            </span>
        </div>

        @if($order->discount > 0)
        <div class="flex justify-between items-center">
            <span class="text-xs text-gray-500">Total Diskon</span>
            <span class="text-xs text-[#e91e63]">-Rp{{ number_format($order->discount, 0, ',', '.') }}</span>
        </div>
        @endif

        <div class="flex justify-between items-center">
            <span class="text-xs text-gray-500">Metode Pembayaran</span>
            <span class="text-xs text-gray-700">
                @php
                    $paymentLabels = [
                        'shopeepay' => 'ShopeePay',
                        'bank'      => 'Bayar Ditempat',
                        'cod'       => 'Bayar Ditempat',
                        'transfer'  => 'Transfer Bank',
                    ];
                @endphp
                {{ $paymentLabels[$order->payment_method] ?? ucfirst($order->payment_method) }}
            </span>
        </div>

        @if($order->voucher_ids && count($order->voucher_ids) > 0)
            @foreach($order->voucher_ids as $voucherId)
                @php $voucher = \App\Models\Voucher::find($voucherId); @endphp
                @if($voucher)
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Voucher Digunakan</span>
                    <span class="text-xs font-bold text-[#e91e63]">{{ $voucher->title }}</span>
                </div>
                @endif
            @endforeach
        @endif

        <div class="border-t border-gray-100 pt-2 flex justify-between items-center">
            <span class="text-sm font-bold text-gray-900">Total Pembayaran</span>
            <span class="text-sm font-black text-[#e91e63]">Rp{{ number_format($order->grand_total, 0, ',', '.') }}</span>
        </div>
    </div>
</div>

<script>lucide.createIcons();</script>
</body>
</html>