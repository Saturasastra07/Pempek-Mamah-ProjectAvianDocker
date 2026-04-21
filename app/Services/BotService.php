<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Order;
use App\Events\MessageSent;

class BotService
{
    protected array $intents = [
        'cek_pesanan'   => ['posisi', 'tracking', 'status', 'pesanan saya', 'orderan', 'pesanan ku', 'order saya'],
        'batalkan'      => ['batal', 'cancel', 'tidak jadi', 'ga jadi', 'gak jadi'],
        'pembayaran'    => ['bayar', 'payment', 'transfer', 'lunas', 'belum dibayar'],
        'komplain'      => ['rusak', 'salah', 'tidak sesuai', 'komplain', 'protes', 'kecewa'],
        'pengiriman'    => ['kirim', 'kurir', 'ongkir', 'sampai', 'tiba', 'delivery'],
        'jam_buka'      => ['jam', 'buka', 'tutup', 'operasional', 'libur'],
        'lokasi'        => ['alamat', 'lokasi', 'toko', 'dimana toko', 'dimana', 'mana', 'letak', 'cabang'],
        'voucher'       => ['voucher', 'diskon', 'promo', 'kode'],
    ];

    protected array $allQuestions = [
        'Bagaimana cara cek status pesanan saya?',
        'Apakah bisa bayar di tempat (COD)?',
        'Berapa biaya ongkos kirim ke lokasi saya?',
        'Dimana lokasi toko fisik Pempek Mamah Dhani?',
        'Sampai jam berapa toko buka?',
        'Bagaimana cara menggunakan voucher promo?',
        'Kenapa pesanan saya belum sampai?',
        'Apakah bisa membatalkan pesanan yang sudah dibayar?',
        'Menu apa yang paling favorit di sini?',
        'Apakah ada paket hampers untuk oleh-oleh?',
    ];

    public function detectIntent(string $text): string
    {
        $text = strtolower($text);
        $scores = [];

        foreach ($this->intents as $intent => $keywords) {
            $scores[$intent] = 0;
            foreach ($keywords as $keyword) {
                if (str_contains($text, $keyword)) {
                    $scores[$intent]++;
                }
            }
        }

        arsort($scores);
        $topIntent = array_key_first($scores);

        return $scores[$topIntent] > 0 ? $topIntent : 'unknown';
    }

    public function reply(Conversation $conversation, string $userMessage): Message
    {
        $intent = $this->detectIntent($userMessage);
        $user = $conversation->user;

        $response = match($intent) {
            'cek_pesanan'  => $this->handleCekPesanan($user),
            'batalkan'     => $this->handleBatalkan($user),
            'pembayaran'   => $this->handlePembayaran($user),
            'komplain'     => $this->handleKomplain($conversation),
            'pengiriman'   => $this->handlePengiriman($user),
            'jam_buka'     => $this->handleJamBuka(),
            'lokasi'       => $this->handleLokasi(),
            'voucher'      => $this->handleVoucher(),
            default        => $this->handleUnknown($conversation),
        };

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => null,
            'sender_type'     => 'bot',
            'content'         => $response['content'],
            'message_type'    => $response['type'] ?? 'text',
            'metadata'        => $response['metadata'] ?? null,
        ]);

        $conversation->update(['last_message_at' => now()]);

        broadcast(new MessageSent($message))->toOthers();

        return $message;
    }

    protected function handleCekPesanan($user): array
    {
        $orders = Order::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'on_delivery'])
            ->with(['items.product', 'address'])
            ->latest()
            ->take(2)
            ->get();

        if ($orders->isEmpty()) {
            return [
                'content' => 'Saat ini kamu tidak memiliki pesanan aktif. Yuk pesan pempek sekarang!',
                'type'    => 'text',
            ];
        }

        $user->load('addresses');

        $orderData = $orders->map(function($order) use ($user) {
            $firstItem = $order->items->first();
            $productImage = $firstItem && $firstItem->product ? $firstItem->product->image : 'default.png';

            // Alamat & penerima
            if ($order->is_dropship) {
                $address  = $order->dropship_receiver_address ?? 'Alamat tidak tersedia';
                $receiver = $order->dropship_receiver_name ?? '-';
            } elseif ($order->delivery_mode === 'pickup') {
                $address  = 'Ambil di Toko';
                $receiver = $user->name;
            } elseif ($order->address) {
                $address  = $order->address->full_address . ', ' . $order->address->district . ', ' . $order->address->city;
                $receiver = $user->name;
            } else {
                $defaultAddress = $user->addresses->where('is_default', 1)->first()
                            ?? $user->addresses->first();
                $address  = $defaultAddress 
                    ? $defaultAddress->full_address . ', ' . $defaultAddress->district . ', ' . $defaultAddress->city
                    : 'Alamat tidak tersedia';
                $receiver = $user->name;
            }

            $statusMap = [
                'pending'     => ['label' => 'Menunggu',     'color' => '#EAB308'],
                'confirmed'   => ['label' => 'Dikonfirmasi', 'color' => '#3B82F6'],
                'preparing'   => ['label' => 'Diproses',     'color' => '#F97316'],
                'on_delivery' => ['label' => 'Dikirim',      'color' => '#A855F7'],
                'delivered'   => ['label' => 'Sukses',       'color' => '#22C55E'],
                'cancelled'   => ['label' => 'Dibatalkan',   'color' => '#EF4444'],
            ];

            $s = $statusMap[$order->status] ?? ['label' => ucfirst($order->status), 'color' => '#9CA3AF'];

            return [
                'order_code'     => $order->order_code,
                'image'          => asset('storage/' . $productImage),
                'date'           => $order->created_at->translatedFormat('d M Y, H:i'),
                'address'        => $address,
                'receiver'       => $receiver,
                'status_label'   => $s['label'],
                'status_color'   => $s['color'],
                'payment'        => match($order->payment_method ?? 'cod') {
                    'cod'       => 'Bayar di Tempat',
                    'shopeepay' => 'ShopeePay',
                    default     => ucfirst($order->payment_method),
                },
                'grand_total'    => 'Rp' . number_format($order->grand_total, 0, ',', '.'),
                'is_dropship'    => $order->is_dropship,
                'receiver_phone' => $order->dropship_receiver_phone, // hanya dipakai kalau is_dropship
            ];
        });

        return [
            'content'  => "Berikut pesanan aktifmu:",
            'type'     => 'info_card',
            'metadata' => ['orders' => $orderData]
        ];
    }

    protected function handleBatalkan($user): array
    {
        $order = Order::where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$order) {
            return [
                'content' => 'Tidak ada pesanan yang bisa dibatalkan. Pesanan hanya bisa dibatalkan saat masih berstatus "Menunggu konfirmasi".',
                'type'    => 'text',
            ];
        }

        return [
            'content'  => "Pesanan *{$order->order_code}* bisa dibatalkan. Hubungi admin kami untuk memproses pembatalan ya.",
            'type'     => 'text',
            'metadata' => ['escalate' => true],
        ];
    }

    protected function handlePembayaran($user): array
    {
        return [
            'content' => "Kami menerima pembayaran via:\n💳 ShopeePay\n💵 Bayar di Tempat (COD)\n\nAda yang ingin ditanyakan lebih lanjut soal pembayaran?",
            'type'    => 'text',
        ];
    }

    protected function handleKomplain(Conversation $conversation): array
    {
        $conversation->update(['status' => 'waiting']);

        return [
            'content' => "Mohon maaf atas ketidaknyamanannya 🙏 Kami akan segera menghubungkan kamu dengan tim kami. Mohon tunggu sebentar ya.",
            'type'    => 'system',
        ];
    }

    protected function handlePengiriman($user): array
    {
        $order = Order::where('user_id', $user->id)
            ->where('status', 'on_delivery')
            ->latest()
            ->first();

        if ($order) {
            return [
                'content'  => "Pesanan *{$order->order_code}* sedang dalam perjalanan ke lokasimu Kamu bisa pantau posisi kurir di halaman status pesanan.",
                'type'     => 'text',
                'metadata' => ['order_id' => $order->id],
            ];
        }

        return [
            'content' => "Ongkos kirim kami flat Rp10.000 untuk semua area pengiriman. Ada pertanyaan lain soal pengiriman?",
            'type'    => 'text',
        ];
    }

    protected function handleJamBuka(): array
    {
        return [
            'content' => "🕐 Jam operasional kami:\nSenin - Sabtu: 08.00 - 21.00 WIB\nMinggu: 09.00 - 20.00 WIB",
            'type'    => 'text',
        ];
    }

    protected function handleLokasi(): array
    {
        return [
            'content'  => "Toko kami berlokasi di \nPerumahan Taman Kayangan blok A3a Swargaloka, Jl. Rm. Hadisoebeno Sosro Wardoyo, Mijen, Kota Semarang, Jawa Tengah.",
            'type'     => 'location_card',
            'metadata' => [
                'name'    => 'Pempek Mamah Dhani',
                'address' => 'Perumahan Taman Kayangan blok A3a Swargaloka, Jl. Rm. Hadisoebeno Sosro Wardoyo, Mijen, Kota Semarang, Jawa Tengah',
                'lat'     => -7.089195,
                'lng'     => 110.304251,
            ],
        ];
    }

    protected function handleVoucher(): array
    {
        return [
            'content' => "🎟️ Voucher bisa dipakai saat checkout di halaman keranjang. Maksimal 2 voucher per transaksi. Cek voucher yang tersedia di halaman keranjang ya!",
            'type'    => 'text',
        ];
    }

    protected function handleUnknown(Conversation $conversation): array
    {
        $shuffledQuestions = $this->allQuestions;
        shuffle($shuffledQuestions);
        $displayQuestions = array_slice($shuffledQuestions, 0, 4);

        return [
            'content' => "Maaf, saya kurang memahami pertanyaanmu 😅 Silakan pilih topik di bawah ini:",
            'type'    => 'welcome_card',
            'metadata' => [
                'title' => 'Silahkan pilih topik yang ingin kamu tanyakan:',
                'questions' => $displayQuestions,
                'options' => [
                    'Status Pesanan',
                    'Pengiriman',
                    'Pembayaran',
                    'Voucher & Promo',
                    'Jam Buka',
                    'Hubungi Admin',
                ]
            ],
        ];
    }
    public function getWelcomeMessage(Conversation $conversation): Message
    {
        $user = $conversation->user;
        
        $shuffledQuestions = $this->allQuestions;
        shuffle($shuffledQuestions);
        $displayQuestions = array_slice($shuffledQuestions, 0, 4);

        $content = "Halo {$user->name}, aku Bot Mamah Dhani!";
        
        $metadata = [
            'type' => 'shopee_style',
            'title' => 'Silahkan pilih topik yang ingin kamu tanyakan:',
            'questions' => $displayQuestions,
            'options' => ['Hubungi Admin', 'Lainnya'] 
        ];

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => null,
            'sender_type'     => 'bot',
            'content'         => $content,
            'message_type'    => 'welcome_card',
            'metadata'        => $metadata,
        ]);

        $conversation->update(['last_message_at' => now()]);

        broadcast(new MessageSent($message))->toOthers();

        return $message;
    }

    public function getRandomQuestions(): array
    {
        $shuffled = $this->allQuestions;
        shuffle($shuffled);
        return array_slice($shuffled, 0, 4);
    }
}