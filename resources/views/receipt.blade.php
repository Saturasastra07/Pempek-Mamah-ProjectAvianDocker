<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk-{{ $order->order_code }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Mono:wght@400;500&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        :root {
            --pink: #e91e63;
            --pink-light: #f8eff2;
            --pink-dark: #880e4f;
            --dark: #1a0a0f;
            --gray: #6b5057;
            --gray-light: #f9f0f3;
            --border: #f0d6de;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #f8eff2;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            
        }

        .receipt-wrapper {
            width: 100%;
            max-width: 380px;
        }

        .receipt {
            background: white;
            border-radius: 16px;
            overflow: visible;
            box-shadow: none;
        }

        .receipt-header {
            background: linear-gradient(135deg, var(--pink-dark) 0%, var(--pink) 60%, #f06292 100%);
            padding: 36px 28px 50px;
            border-radius: 16px 16px 0 0;
            position: relative;
            overflow: hidden;
            text-align: center;
            box-shadow: inset 0 -10px 20px rgba(0,0,0,0.08);
            z-index: 1;
        }

        .receipt-header::before {
            content: '';
            position: absolute;
            top: -40px; right: -40px;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            animation: glow-float 0.8s ease infinite;
        }

        .receipt-header::after {
            content: '';
            position: absolute;
            bottom: -30px; left: -30px;
            width: 120px; height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            animation: glow-float-2 1.5s ease infinite;
        }

        @keyframes glow-float {
            0%   { opacity: 0.06; transform: scale(1) translate(0, 0); }
            50%  { opacity: 0.14; transform: scale(1.2) translate(-10px, 10px); }
            100% { opacity: 0.06; transform: scale(1) translate(0, 0); }
        }

        @keyframes glow-float-2 {
            0%   { opacity: 0.04; transform: scale(1) translate(0, 0); }
            50%  { opacity: 0.12; transform: scale(1.3) translate(10px, -8px); }
            100% { opacity: 0.04; transform: scale(1) translate(0, 0); }
        }

        .brand-name {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 900;
            color: white;
            letter-spacing: -0.3px;
            position: relative;
            z-index: 1;
            line-height: 1.2;
        }

        .brand-tagline {
            font-size: 10px;
            color: rgba(255,255,255,0.9);
            letter-spacing: 0.2em;
            text-transform: uppercase;
            margin-top: 4px;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        .receipt-body {
            padding: 24px 28px 28px;
            background: white;
        }

        .order-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 16px 0;
            border-bottom: 1.5px dashed var(--border);
            margin-bottom: 20px;
        }

        .order-info-item label {
            font-size: 9px;
            font-weight: 600;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            display: block;
            margin-bottom: 10px;
        }

        .order-info-item span {
            font-family: 'DM Mono', monospace;
            font-size: 11px;
            font-weight: 500;
            color: var(--dark);
        }

        .order-badge {
            background: var(--pink-light);
            color: var(--pink);
            font-family: 'DM Mono', monospace;
            font-size: 10px;
            font-weight: 500;
            padding: 4px 10px;
            border-radius: 20px;
            border: 1px solid rgba(233,30,99,0.15);
        }

        .section-title {
            font-size: 9px;
            font-weight: 700;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-bottom: 12px;
        }

        .items-list {
            margin-bottom: 20px;
        }

        .item-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid var(--gray-light);
        }

        .item-row:last-child { border-bottom: none; }

        .item-emoji {
            width: 32px;
            height: 32px;
            background: var(--gray-light);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .item-info {
            flex: 1;
            min-width: 0;
        }

        .item-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--dark);
            line-height: 1.3;
        }

        .item-note {
            font-size: 10px;
            color: var(--gray);
            margin-top: 2px;
        }

        .item-qty-price {
            text-align: right;
            flex-shrink: 0;
        }

        .item-qty {
            font-size: 10px;
            color: var(--gray);
            margin-bottom: 2px;
        }

        .item-price {
            font-family: 'DM Mono', monospace;
            font-size: 12px;
            font-weight: 500;
            color: var(--dark);
        }

        .summary {
            background: #fff;
            border-radius: 12px;
            padding: 16px;
            border: 1px solid var(--border);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
        }

        .summary-row span:first-child {
            font-size: 12px;
            color: var(--gray);
            font-weight: 400;
        }

        .summary-row span:last-child {
            font-family: 'DM Mono', monospace;
            font-size: 12px;
            color: var(--dark);
            font-weight: 500;
        }

        .summary-row.discount span:last-child {
            color: var(--pink);
        }

        .summary-divider {
            height: 1px;
            background: var(--border);
            margin: 10px 0;
        }

        .summary-row.total span:first-child {
            font-size: 13px;
            font-weight: 700;
            color: var(--dark);
        }

        .summary-row.total span:last-child {
            font-family: 'DM Mono', monospace;
            font-size: 16px;
            font-weight: 500;
            color: var(--pink);
        }

       .wave svg {
            display: block;
            width: 100%;
            height: 40px;
            fill: white;
            margin-top: -1px;
            position: relative;
            z-index: 2;
        }

        .receipt-footer {
            background: white;
            padding: 24px 28px;
            text-align: center;
            margin-top: 6px;
            border-radius: 16px;
        }

        .barcode {
            font-family: 'DM Mono', monospace;
            font-size: 9px;
            color: var(--gray);
            letter-spacing: 0.3em;
            margin-bottom: 12px;
            display: block;
        }

        .barcode-lines {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 2px;
            height: 40px;
            margin-bottom: 8px;
        }

        .barcode-line {
            background: var(--dark);
            width: 2px;
            border-radius: 1px;
        }

        .thank-you {
            font-family: 'Playfair Display', serif;
            font-size: 15px;
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 7px;
        }

        .footer-note {
            font-size: 10px;
            color: var(--gray);
            line-height: 1.5;
        }

        .footer-contact {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px dashed var(--border);
        }

        .footer-contact span {
            margin-top: 8px;
            margin-bottom: -4px;
            font-size: 10px;
            color: var(--gray);
            font-weight: 500;
        }

        .footer-contact span strong {
            color: var(--pink);
        }

        .order-info-item span {
            font-family: 'DM Mono', monospace;
            font-size: 11px;
            font-weight: 500;
            color: var(--dark);
            word-wrap: break-word;
            line-height: 1.4;
        }

        .hole-left, .hole-right {
            position: absolute;
            width: 20px;
            height: 20px;
            background: #f8eff2;
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
        }

        .hole-left { left: -10px; }
        .hole-right { right: -10px; }

       @media print {
            button { display: none !important; }

            body {
                background: white !important;
                padding: 20px !important;
                display: block !important;
            }

            .receipt-wrapper {
                max-width: 100% !important;
                width: 100% !important;
                filter: none !important;
            }

            .receipt {
                border-radius: 0 !important;
                width: 100% !important;
            }

            .receipt-header {
                border-radius: 0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background: linear-gradient(135deg, var(--pink-dark) 0%, var(--pink) 60%, #f06292 100%) !important;
            }

            .receipt-header::before,
            .receipt-header::after {
                animation: none !important;
                opacity: 0.06 !important;
            }

            .wave svg {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .receipt-footer {
                border-radius: 0 !important;
                page-break-inside: avoid;
                margin-top: 20px !important;
            }

            div[style*="border-radius: 50%"] {
                display: none !important;
            }

            @page {
                margin: 20px 0;
            }

            .item-row {
                page-break-inside: avoid;
            }
            .summary {
                page-break-inside: avoid;
                page-break-before: auto;
                margin-top: 20px !important;
            }

        }

        @media (max-width: 400px) {
            .receipt-body { padding: 4px 20px 24px; }
            .receipt-header { padding: 28px 20px 50px; }
        }
    </style>
</head>
<body>
    <div class="receipt-wrapper">

        <button onclick="cetakStruk()" style="
            position: fixed;
            bottom: 28px;
            right: 24px;
            z-index: 9999;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--pink);
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(233,30,99,0.4), 0 2px 6px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            " 
            onmouseover="this.style.transform='scale(1.1)'"
            onmouseout="this.style.transform='scale(1)'"
            onmousedown="this.style.transform='scale(0.95)'"
            onmouseup="this.style.transform='scale(1.1)'">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
        </button>

        <script>
            function cetakStruk() {
                setTimeout(() => {
                    window.print();
                }, 500);
            }
        </script>

        <div class="receipt">

            <!-- HEADER -->
            <div class="receipt-header">
                <div class="brand-name">Pempek Mamah Dhani</div>
                <div class="brand-tagline">Cita Rasa Palembang Asli</div>
            </div>

            <div class="wave">
                <svg viewBox="0 0 500 60" preserveAspectRatio="none">
                    <path d="M0,40 C120,80 380,0 500,40 L500,0 L0,0 Z" fill="#f8d0dc"></path>
                </svg>
            </div>

            <div class="receipt-body">

                <div class="order-info">
                    <div class="order-info-item">
                        <label>No. Pesanan</label>
                        <span class="order-badge">{{ $order->order_code }}</span>
                    </div>
                    <div class="order-info-item" style="text-align:right;">
                        <label>Waktu</label>
                        <span>{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>

                <div class="order-info" style="padding-top:0; border-bottom: none; margin-bottom: 4px;">
                    <div class="order-info-item">
                        <label>Penerima</label>
                        <span>
                            {{ $order->is_dropship 
                                ? $order->dropship_receiver_name 
                                : ($order->address?->receiver_name ?? auth()->user()->name) }}
                        </span>
                    </div>
                    <div class="order-info-item" style="text-align:right;">
                        <label>Pengiriman</label>
                        <span>{{ $order->delivery_mode === 'pickup' ? 'Ambil Sendiri' : 'Delivery' }}</span>
                    </div>
                </div>

                @if($order->is_dropship)
                <div class="order-info" style="padding-top:0; border-bottom: none; margin-bottom: 4px;">
                    <div class="order-info-item" style="width:100%;">
                        <label>Alamat Penerima</label>
                        <span>{{ $order->dropship_receiver_address }}, {{ $order->dropship_receiver_district }}, {{ $order->dropship_receiver_city }}</span>
                    </div>
                </div>
                <div class="order-info" style="padding-top:0; border-bottom: none; margin-bottom: 10px;">
                    <div class="order-info-item" style="width:100%;">
                        <label>No. Telepon Penerima</label>
                        <span>{{ $order->dropship_receiver_phone }}</span>
                    </div>
                </div>
                @else
                @if($order->delivery_mode === 'delivery' && $order->address)
                <div class="order-info" style="padding-top:0; border-bottom: none;">
                    <div class="order-info-item" style="width:100%;">
                        <label>Alamat</label>
                        <span>{{ $order->address->full_address }}, {{ $order->address->district }}, {{ $order->address->city }}</span>
                    </div>
                </div>
                @endif
                @endif

                @if($order->is_dropship)
                <div style="margin-top: -15px; margin-bottom: 15px;">
                    <span style="display:block; font-size:9px; color: #e91e63; font-family:'DM Mono',monospace; font-weight:500; letter-spacing:0.05em;">
                        Dikirim sebagai dropshiper dari {{ $order->dropship_name }}
                    </span>
                    </div>
                    @endif

                <div style="height: 1.5px; border-style: dashed; border-width: 0 0 1.5px 0; margin-bottom: 20px;"></div>

                <div class="section-title">Detail Pesanan</div>
                <div class="items-list">
                    @foreach($order->items as $item)
                    <div class="item-row">
                        <div class="item-emoji">
                            <img src="{{ asset('assets/images/' . ($item->product?->image ?? '')) }}"
                                style="width:100%; height:100%; object-fit:cover; border-radius:10px;"
                                onerror="this.style.display='none'; this.parentElement.innerHTML='🍤'">
                        </div>
                        <div class="item-info">
                            <div class="item-name">{{ $item->product_name }}</div>
                            @if($item->notes)
                            <div class="item-note">{{ $item->notes }}</div>
                            @endif
                            @php
                                $addons = is_array($item->addons) ? $item->addons : json_decode($item->addons, true) ?? [];
                            @endphp
                            @if(count($addons) > 0)
                                <div class="item-note">
                                    + {{ collect($addons)->map(fn($a) => is_array($a) ? $a['name'] : $a)->join(', ') }}
                                </div>
                            @endif
                        </div>
                        <div class="item-qty-price">
                            <div class="item-qty">x{{ $item->quantity }}</div>
                            <div class="item-price">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="summary">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Ongkos Kirim</span>
                        <span>{{ $order->shipping_cost == 0 ? 'Gratis' : 'Rp' . number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="summary-row discount">
                        <span>Total Diskon</span>
                        <span>-Rp{{ number_format($order->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="summary-row">
                        <span>Metode Pembayaran</span>
                        <span>
                            @php
                                $paymentLabels = ['shopeepay'=>'ShopeePay','cod'=>'COD','bank'=>'COD'];
                            @endphp
                            {{ $paymentLabels[$order->payment_method] ?? ucfirst($order->payment_method) }}
                        </span>
                    </div>
                    <div class="summary-divider"></div>
                    <div class="summary-row total">
                        <span>Total Bayar</span>
                        <span>Rp{{ number_format($order->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- ZIGZAG BOTTOM -->
            <div style="position: relative; height: 24px; display: flex; align-items: center; background: white;">
                <div style="position: absolute; left: -12px; width: 24px; height: 24px; border-radius: 50%; background: #f8eff2;"></div>
                <div style="position: absolute; right: -12px; width: 24px; height: 24px; border-radius: 50%; background: #f8eff2;"></div>
                <div style="border-top: 1.5px dashed var(--border); width: 100%; margin: 0 20px;"></div>
            </div>

            <!-- FOOTER -->
            <div class="receipt-footer">
                <div id="qrcode" style="display: flex; justify-content: center; margin-bottom: 8px;"></div>
                    <span class="barcode">{{ $order->order_code }}</span>

                    <script>
                        new QRCode(document.getElementById("qrcode"), {
                            text: "{{ url('/order/' . $order->id . '/status') }}",
                            width: 100,
                            height: 100,
                            colorDark: "#1a0a0f",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });
                    </script>

                <div class="thank-you">Terima kasih sudah memesan!</div>
                <div class="footer-note">
                    Simpan struk ini sebagai bukti pembelian.<br>
                    Struk berlaku untuk penukaran & komplain.
                </div>

                <div class="footer-contact">
                    <span style="display:flex; align-items:center; gap:5px;">
                        <img src="{{ asset('assets/images/maps-mamah.png') }}" 
                            style="width:14px; height:14px; object-fit:contain;">
                        <strong>Cangkiran, Semarang</strong>
                    </span>
                    <span style="display:flex; align-items:center; gap:5px;">
                        <img src="{{ asset('assets/images/wa-mamah.png') }}" 
                            style="width:14px; height:14px; object-fit:contain;">
                        <strong>0812 1579 4223</strong>
                    </span>
                </div>
            </div>
        </div>
    </body>
</html>