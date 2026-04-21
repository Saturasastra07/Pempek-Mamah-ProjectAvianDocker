<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pusat Bantuan - Pempek Mamah Dhani</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { 
            font-family: 'Inter', sans-serif;
            overflow: hidden;
        }

        .chat-container {
            display: flex;
            flex-direction: column;
            height: calc(var(--vh, 1vh) * 100);
            width: 100%;
            overflow: hidden;
            top: 0;
            left: 0;
        }

        [x-cloak] {
            display: none !important;
        }

        .chat-bg {
            background-color: #f0f2f5;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23e91e63' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .messages-area {
            flex: 1 1 0%;
            overflow-y: auto;
        }

        .bubble-bot {
            position: relative;
        }
        .bubble-bot::before {
            content: "";
            position: absolute;
            z-index: 0;
            bottom: 0;
            left: -8px;
            height: 18px;
            width: 18px;
            background-color: white;
            border-bottom-right-radius: 12px;
        }
        .bubble-bot::after {
            content: "";
            position: absolute;
            z-index: 1;
            bottom: 0;
            left: -11px;
            height: 18px;
            width: 11px;
            background-color: #f0f2f5;
            border-bottom-right-radius: 10px;
        }

        .bubble-user {
            position: relative;
        }
        .bubble-user::before {
            content: "";
            position: absolute;
            z-index: 0;
            bottom: 0;
            right: -8px;
            height: 18px;
            width: 18px;
            background-color: #e91e63;
            border-bottom-left-radius: 12px;
        }
        .bubble-user::after {
            content: "";
            position: absolute;
            z-index: 1;
            bottom: 0;
            right: -11px;
            height: 18px;
            width: 11px;
            background-color: #f0f2f5;
            border-bottom-left-radius: 10px;
        }

        @keyframes pop {
            0% {
                transform: scale(0.9);
                opacity: 0;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-pop {
            animation: pop 0.18s ease-out;
        }

        @keyframes reaction-pop {
            0%   { transform: scale(0); opacity: 0; }
            60%  { transform: scale(1.3); opacity: 1; }
            80%  { transform: scale(0.9); }
            100% { transform: scale(1); }
        }

        .reaction-pop {
            animation: reaction-pop 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }
    </style>
</head>
<body class="bg-gray-50 h-screen flex flex-col overflow-hidden">

<div x-data="chatPage()" x-init="init()" x-cloak class="chat-container">

    <!-- Overlay blur -->
    <div x-show="showActionMenu" 
        class="fixed inset-0 z-40"
        style="backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); background: rgba(0,0,0,0.2);"
        @click="showActionMenu = false">
    </div>

    <div x-show="showActionMenu" class="fixed z-50 animate-pop"
        style="bottom: max(80px, calc(env(safe-area-inset-bottom) + 80px)); left: 16px;">
        
        <!-- Emoji bar - lebar 220px -->
        <div class="bg-white border border-gray-200 rounded-full shadow-lg flex items-center mb-2" style="width: 250px;">
            <div class="flex-1 overflow-x-auto no-scrollbar" style="-webkit-overflow-scrolling: touch;">
                <div class="flex items-center gap-1 px-3 py-2" style="width: max-content;">
                    <span class="text-2xl cursor-pointer flex-shrink-0" @click="reactToMessage('👍')">👍</span>
                    <span class="text-2xl cursor-pointer flex-shrink-0" @click="reactToMessage('❤️')">❤️</span>
                    <span class="text-2xl cursor-pointer flex-shrink-0" @click="reactToMessage('😂')">😂</span>
                    <span class="text-2xl cursor-pointer flex-shrink-0" @click="reactToMessage('😮')">😮</span>
                    <span class="text-2xl cursor-pointer flex-shrink-0" @click="reactToMessage('😢')">😢</span>
                    <span class="text-2xl cursor-pointer flex-shrink-0" @click="reactToMessage('🙏')">🙏</span>
                    <span class="text-2xl cursor-pointer flex-shrink-0" @click="reactToMessage('🔥')">🔥</span>
                    <span class="text-2xl cursor-pointer flex-shrink-0" @click="reactToMessage('😍')">😍</span>
                    <span class="text-2xl cursor-pointer flex-shrink-0" @click="reactToMessage('😭')">😭</span>
                    <span class="text-2xl cursor-pointer flex-shrink-0" @click="reactToMessage('🎉')">🎉</span>
                </div>
            </div>
            <div class="flex-shrink-0 pr-2">
                <button class="w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center text-gray-500 text-sm font-bold border-4 border-gray-100"
                    @click="showActionMenu = false; $nextTick(() => $refs.messageInput.focus())">+</button>
            </div>
        </div>

        <!-- Preview teks -->
        <div class="bg-white border border-gray-100 rounded-2xl px-4 py-3 shadow-lg mb-2"
            style="min-width: 220px; max-width: 75vw; width: fit-content;">
            <p class="text-[13px] text-gray-800 leading-relaxed" x-text="selectedMessage?.content"></p>
            <p class="text-[10px] text-gray-400 mt-1 text-right" x-text="selectedMessage ? formatTime(selectedMessage.created_at) : ''"></p>
        </div>

        <div class="space-y-2" style="width: 220px;">
            <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-lg">
                <button @click="setReply(selectedMessage); showActionMenu=false"
                    class="w-full flex justify-between items-center px-4 py-2 text-[14px] text-gray-800 border-b border-gray-200 active:bg-gray-50">
                    <span>Balas Pesan</span>
                    <img src="{{ asset('assets/images/iconReply.png') }}" alt="Reply" class="w-5 h-5 object-contain">
                </button>

                <button @click="copyText(selectedMessage.content)"
                    class="w-full flex justify-between items-center px-4 py-2 text-[14px] text-gray-800 border-b border-gray-200 active:bg-gray-50">
                    <span>Salin Teks</span>
                    <img src="{{ asset('assets/images/iconCopy.png') }}" alt="Copy" class="w-5 h-5 object-contain">
                </button>

                <button class="w-full flex justify-between items-center px-4 py-2 text-[14px] active:bg-gray-50"
                    :class="selectedMessage?.sender_type === 'user' ? 'text-orange-500' : 'text-red-500'"
                    @click="selectedMessage?.sender_type === 'user' ? unsendMessage(selectedMessage) : deleteMessage(selectedMessage)">
                    <span x-text="selectedMessage?.sender_type === 'user' ? 'Batal Kirim' : 'Hapus'"></span>
                    <img src="{{ asset('assets/images/iconTrash.png') }}" alt="Trash" class="w-5 h-5 object-contain">
                </button>
            </div>
            <button @click="showActionMenu = false"
                class="w-full bg-white border border-gray-100 rounded-2xl py-2 text-[14px] font-semibold text-gray-700 active:bg-gray-50 shadow-lg">
                Batalkan
            </button>
        </div>
    </div>

    <div class="bg-[#e91e63] px-4 pt-4 pb-4 flex items-center gap-3 flex-shrink-0 ">
        <a href="{{ route('home') }}?sidebar=open" class="text-white p-1">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>

        <div class="w-10 h-10  flex items-center justify-center flex-shrink-0">
            <img src="{{ asset('assets/images/MamahProfil.png') }}" 
                alt="Profile" 
                class="w-full h-full object-contain">
        </div>

        <div class="flex-1">
            <p class="text-white font-bold text-sm">Pempek Mamah Dhani</p>
            <p class="text-white/70 text-[11px]" x-text="statusLabel"></p>
        </div>

        <button class="text-white/80 p-1">
            <i data-lucide="more-vertical" class="w-5 h-5"></i>
        </button>
    </div>

    <div x-show="showFloatingDate" x-transition.opacity.duration.300ms class="fixed top-[64px] left-0 right-0 z-40 flex justify-center py-2 mt-2">
        <span class="text-[10px] text-gray-500 px-3 py-1 bg-white rounded-full shadow-md"
            x-text="currentDateLabel">
        </span>
    </div>

        <div 
            class="flex-1 overflow-y-auto no-scrollbar chat-bg py-4 space-y-2"
            :style="replyingTo ? 'padding-bottom: 130px;' : 'padding-bottom: 80px;'"
            x-ref="messagesContainer"
        >
            <template x-for="(message, index) in messages" :key="message.id">
                <div class="px-4" :data-message="true" :data-date="message.created_at">

                <template x-if="message.sender_type !== 'user' && message.message_type !== 'system'">
                    <div class="flex-1 flex flex-col gap-2 min-w-0 ml-1 mr-auto relative group">
                        <div class="bg-white bubble-bot rounded-2xl px-4 pt-2.5 pb-1.5 shadow-sm max-w-[92%] transition-transform duration-200"
                            @touchstart="gestureStart($event, message)" 
                            @touchmove="gestureMove($event, message.id)" 
                            @touchend="gestureEnd(message)" 
                            @mousedown="gestureStart($event, message)" 
                            @mousemove="gestureMove($event, message.id)" 
                            @mouseup="gestureEnd(message)" 
                            @mouseleave="cancelLongPress"

                            :style="swipe.activeId === message.id 
                                ? `transform: translateX(${swipe.currentX}px)` 
                                : ''"
                        >
                            <p class="text-[13px] text-gray-800 leading-tight" x-text="message.content"></p>
                            <p class="text-[10px] text-gray-400 mt-1 text-right leading-none" x-text="formatTime(message.created_at)"></p>
                            <template x-if="reactions[message.id]">
                                <div class="absolute -bottom-4 right-2 z-10">
                                    <div class="bg-white rounded-full shadow-md border border-gray-100 px-1.5 py-0.5 flex items-center gap-0.5 reaction-pop">
                                        <span class="text-base leading-none" x-text="reactions[message.id]"></span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <template x-if="message.message_type === 'welcome_card'">
                            <div class="bg-white rounded-xl shadow-md border border-gray-100 w-full overflow-hidden mt-1">
                                <div class="p-4 border-b border-gray-50 bg-gray-50/50">
                                    <p class="text-[13px] font-bold text-gray-800" x-text="message.metadata.title"></p>
                                </div>
                                    
                                <div class="flex flex-col">
                                    <template x-for="q in message.metadata.questions" :key="q">
                                        <button @click.prevent="sendQuickReply(q)" 
                                            class="text-left px-4 py-3.5 text-[13px] text-blue-600 border-b border-gray-50 hover:bg-blue-50 transition-colors">
                                            <span x-text="q"></span>
                                        </button>
                                    </template>
                                </div>

                                <button @click.stop.prevent="refreshQuestions(message.id)" 
                                    class="w-full py-3.5 flex items-center justify-center gap-2 text-[12px] text-orange-500 font-semibold hover:bg-orange-50">
                                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                    Ganti Pertanyaan
                                </button>
                            </div>
                        </template>

                        <template x-if="message.message_type === 'location_card'">
                            <div class="mt-2 bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 max-w-[92%]">
                                
                                <div class="relative">
                                    <iframe
                                        :src="`https://www.openstreetmap.org/export/embed.html?bbox=${message.metadata.lng-0.005},${message.metadata.lat-0.005},${message.metadata.lng+0.005},${message.metadata.lat+0.005}&layer=mapnik&marker=${message.metadata.lat},${message.metadata.lng}`"
                                        class="w-full h-36 border-0"
                                        loading="lazy">
                                    </iframe>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="bg-white rounded-full p-2 shadow-lg">
                                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div class="px-3 py-2 border-b border-gray-50">
                                    <p class="text-[13px] font-bold text-gray-800" x-text="message.metadata.name"></p>
                                    <!-- <p class="text-[11px] text-gray-500 mt-0.5 leading-relaxed" x-text="message.metadata.address"></p> -->
                                </div>

                                <div class="flex divide-x divide-gray-100">
                                    <a :href="`https://www.google.com/maps?q=${message.metadata.lat},${message.metadata.lng}`"
                                    target="_blank"
                                    class="flex-1 py-3 flex items-center justify-center gap-1.5 text-[12px] text-blue-600 font-semibold hover:bg-blue-50 transition-colors">

                                        <img src="{{ asset('assets/images/iconMaps.png') }}"
                                            alt="Google Maps"
                                            class="w-4 h-4 object-contain">

                                        Google Maps
                                    </a>
                                    <a :href="`https://maps.google.com/?q=${message.metadata.lat},${message.metadata.lng}`"
                                    target="_blank"
                                    class="flex-1 py-3 flex items-center justify-center gap-1.5 text-[12px] text-green-500 font-semibold hover:bg-green-50 transition-colors">
                                        <img src="{{ asset('assets/images/iconBrowser.png') }}"
                                            alt="Browser"
                                            class="w-4 h-4 object-contain">

                                        Buka Browser
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="message.message_type === 'info_card'">
                    <div class="flex flex-col gap-3 w-full mt-2">
                        <template x-for="order in message.metadata.orders" :key="order.order_code">
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="p-3 flex gap-3 border-b border-gray-50">
                                    <img :src="order.image" class="w-16 h-16 rounded-lg object-cover bg-gray-50 flex-shrink-0">
                                    <div class="flex-1 min-w-0 flex flex-col h-16">
                                        <p class="text-[14px] font-bold text-gray-800 truncate" x-text="order.order_code"></p>
                                        <div class="flex justify-between items-center mt-1">
                                            <span class="text-[11px] text-gray-700 font-medium" x-text="order.date"></span>
                                        </div>
                                        <div class="mt-auto w-full h-[1.2px] bg-[repeating-linear-gradient(to_right,#d1d5db_0px,#d1d5db_6px,transparent_6px,transparent_12px)]"></div>
                                    </div>
                                </div>

                                <div class="p-3 space-y-2">
                                    <div class="flex gap-2">
                                        <div class="flex justify-between w-24 flex-shrink-0 text-[11px] text-gray-500">
                                            <span>Alamat</span><span>:</span>
                                        </div>
                                        <span class="text-[11px] text-gray-700 leading-relaxed" x-text="order.address"></span>
                                    </div>

                                    <div class="flex gap-2 items-center">
                                        <div class="flex justify-between w-24 flex-shrink-0 text-[11px] text-gray-500">
                                            <span>Status</span><span>:</span>
                                        </div>
                                        <div :style="`background-color: ${order.status_color || '#9CA3AF'}`"
                                            class="px-2 py-1 rounded-lg flex items-center shadow-sm border border-black/10">
                                            <span class="text-[10px] text-white font-semibold uppercase tracking-wider"
                                                x-text="order.status_label"></span>
                                        </div>
                                    </div>

                                    <div class="flex gap-2">
                                        <div class="flex justify-between w-24 flex-shrink-0 text-[11px] text-gray-500">
                                            <span>Penerima</span><span>:</span>
                                        </div>
                                        <span class="text-[11px] text-gray-700 font-medium" x-text="order.receiver"></span>
                                    </div>

                                    <div class="flex gap-2 items-center">
                                        <div class="flex justify-between w-24 flex-shrink-0 text-[11px] text-gray-500">
                                            <span>Pembayaran</span><span>:</span>
                                        </div>
                                        <span class="text-[11px] text-gray-700" x-text="order.payment"></span>
                                    </div>

                                    <template x-if="order.is_dropship">
                                        <div class="flex gap-2">
                                            <div class="flex justify-between w-24 flex-shrink-0 text-[11px] text-gray-500">
                                                <span>No. Telp (DS)</span><span>:</span>
                                            </div>
                                            <span class="text-[11px] text-gray-700" x-text="order.receiver_phone"></span>
                                        </div>
                                    </template>

                                    <div class="flex gap-2 items-center pt-1">
                                        <div class="flex justify-between w-24 flex-shrink-0 text-[11px] text-gray-500 font-medium">
                                            <span>Total Bayar</span><span>:</span>
                                        </div>
                                        <span class="text-[13px] font-bold text-[#e91e63]" x-text="order.grand_total"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                    <template x-if="message.sender_type === 'user'">
                        <div class="flex flex-1 justify-end gap-2 min-w-0 ml-1 mr-auto">

                            <div class="max-w-[78%] bg-[#e91e63] bubble-user rounded-2xl shadow-sm relative mr-2 transition-transform duration-200"
                                :class="message.image_path ? 'p-1' : 'px-4 pt-2.5 pb-1.5'"
                                @touchstart="gestureStart($event, message)" 
                                    @touchmove="gestureMove($event, message.id)" 
                                    @touchend="gestureEnd(message)" 
                                    @mousedown="gestureStart($event, message)" 
                                    @mousemove="gestureMove($event, message.id)" 
                                    @mouseup="gestureEnd(message)" 
                                    @mouseleave="cancelLongPress"
                                    :style="swipe.activeId === message.id 
                                        ? `transform: translateX(${swipe.currentX}px)` 
                                        : ''"
                                >

                                <template x-if="message.image_path">
                                    <img :src="message.image_path.startsWith('blob:') 
                                            ? message.image_path 
                                            : '/storage/' + message.image_path" 
                                        class="w-full max-w-[200px] rounded-xl object-cover cursor-pointer"
                                        @click="openImageFull(message.image_path)">
                                </template>

                                <template x-if="message.content">
                                    <p class="text-[13px] text-white leading-tight break-words"
                                    :class="message.image_path ? 'px-2 pt-1' : ''"
                                    x-text="message.content"></p>
                                </template>

                                <div class="flex items-center justify-end gap-1 mt-1"
                                    :class="message.image_path ? 'px-2 pb-1' : ''">
                                    <p class="text-[10px] text-white/70 leading-none" x-text="formatTime(message.created_at)"></p>
                                </div>

                                <template x-if="reactions[message.id]">
                                    <div class="absolute -bottom-4 right-2 z-10">
                                        <div class="bg-white rounded-full shadow-md border border-gray-100 px-1.5 py-0.5 flex items-center gap-0.5 reaction-pop">
                                            <span class="text-base leading-none" x-text="reactions[message.id]"></span>
                                        </div>
                                    </div>
                                </template>

                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <div x-show="isTyping" class="flex items-center px-4 py-2">
                <div class="flex gap-1 items-center bg-transparent">
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0ms"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:150ms"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:300ms"></div>
                </div>
            </div>

        </div>

        <template x-if="conversationStatus === 'waiting'">
            <div class="flex justify-center my-3">
                <div class="bg-yellow-50 border border-yellow-200 rounded-2xl px-4 py-3 text-center max-w-[80%]">
                    <p class="text-[11px] text-yellow-700 font-semibold">⏳ Menunggu Admin</p>
                    <p class="text-[10px] text-yellow-600 mt-0.5">Tim kami akan segera membalas</p>
                </div>
            </div>
        </template>

        <div class="bg-white/65 backdrop-blur-md fixed bottom-0 left-0 right-0 py-1 gap-2 border-t border-gray-200 shadow-[0_-2px_10px_rgba(0,0,0,0.05)] z-50">

            <template x-if="selectedImage">
                <div class="px-3 pt-2">
                    <div class="relative inline-block">
                        <img :src="selectedImagePreview" class="h-20 w-20 object-cover rounded-xl border border-gray-200">
                        <button @click="removeImage()"
                            class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-gray-800 rounded-full flex items-center justify-center">
                            <span class="text-white text-[10px] leading-none font-bold">✕</span>
                        </button>
                    </div>
                </div>
            </template>

            <template x-if="replyingTo">
                <div class="px-0">
                    <div class="flex items-stretch gap-2 px-2 py-2">
                        <div class="w-[3px] bg-[#e91e63]"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] text-[#e91e63]">Membalas</p>

                            <p class="text-[12px] text-gray-800 truncate"
                            x-text="replyingTo.content">
                            </p>
                        </div>

                        <button @click="cancelReply()" class="text-gray-400 hover:text-gray-600 text-sm">
                            ✕
                        </button>
                    </div>
                </div>
            </template>

            <div class="px-3 pt-1 pb-2 flex items-end gap-2">

                <button @click="$refs.fileInput.click()"
                    class="w-[36px] h-[36px] flex items-center justify-center flex-shrink-0 text-gray-400">
                    <i data-lucide="plus-circle" class="w-6 h-6"></i>
                </button>

                <input type="file" x-ref="fileInput" accept="image/*"
                    class="hidden"
                    @change="handleFileSelect($event)">

                <input type="file" x-ref="cameraInput" accept="image/*" capture="environment"
                    class="hidden"
                    @change="handleFileSelect($event)">
            
                <div class="flex-1 bg-gray-100 rounded-2xl px-4 flex items-center shadow-sm">
                    <textarea
                        x-ref="messageInput"
                        x-model="inputMessage"
                        @keydown.enter.prevent="handleEnter"
                        @input="autoResize($el)"
                        rows="1"
                        placeholder="Ketik pesan..."
                        class="flex-1 resize-none text-[15px] text-gray-800 placeholder:text-gray-400 outline-none leading-relaxed max-h-32 py-2.5 bg-transparent no-scrollbar"
                        style="scrollbar-width: none; border:none;"></textarea>
                </div>

                <button x-show="!inputMessage.trim() && !selectedImage"
                    @click="$refs.cameraInput.click()"
                    class="w-[36px] h-[36px] bg-[#e91e63] rounded-full flex items-center justify-center flex-shrink-0 active:scale-90 transition-all shadow-md">
                    <i data-lucide="camera" class="w-5 h-5 text-white"></i>
                </button>

                <!-- Tombol send (muncul kalau ada teks atau ada foto) -->
                <button x-show="inputMessage.trim() || selectedImage"
                    @click="sendMessage()"
                    :disabled="sending"
                    class="w-[36px] h-[36px] bg-[#e91e63] rounded-full flex items-center justify-center flex-shrink-0 active:scale-90 transition-all shadow-md disabled:opacity-30">
                    <i data-lucide="send" class="w-5 h-5 text-white"></i>
                </button>
            </div>
        </div>
    </div>

<script>
    function chatPage() {
        return {
        messages: @json($messages ?? []),
        conversationId: {{ $conversation->id ?? 0 }},
        conversationStatus: @json($conversation->status ?? 'bot'),
        inputMessage: '',
        sending: false,
        isTyping: false,
        replyingTo: null,
        userScrolling: false,
        currentDateLabel: '',
        showFloatingDate: false,
        scrollTimeout: null,
        swipe: { startX: 0, activeId: null, currentX: 0 },
        isSwiping: false,
        longPressTimeout: null,
        selectedMessage: null,
        showActionMenu: false,
        reactions: {},
        selectedImage: null,
        selectedImagePreview: null,
                
        updateFloatingDate() {
            const container = this.$refs.messagesContainer;
            if (!container) return;

            const messagesEl = container.querySelectorAll('[data-message]');
            
            let closest = null;
            let minOffset = Infinity;

            messagesEl.forEach(el => {
                const rect = el.getBoundingClientRect();
                const offset = Math.abs(rect.top - 100); // 100px dari atas
                
                if (offset < minOffset) {
                    minOffset = offset;
                    closest = el;
                }
            });

            if (closest) {
                const date = closest.getAttribute('data-date');
                this.currentDateLabel = this.formatDateLabel(date);
            }
        },

        formatDateLabel(dateString) {
            const date = new Date(dateString);
            const today = new Date();
            const yesterday = new Date();
            yesterday.setDate(today.getDate() - 1);

            const isSameDay = (d1, d2) =>
                d1.getFullYear() === d2.getFullYear() &&
                d1.getMonth() === d2.getMonth() &&
                d1.getDate() === d2.getDate();

            if (isSameDay(date, today)) {
                return 'Hari ini';
            }

            if (isSameDay(date, yesterday)) {
                return 'Kemarin';
            }

            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
        },

        shouldShowDate(index) {
            if (index === 0) return true;

            const current = new Date(this.messages[index].created_at);
            const prev = new Date(this.messages[index - 1].created_at);

            return current.toDateString() !== prev.toDateString();
        },

        setReply(message) {
            console.log('CLICKED', message);
            this.replyingTo = message;

            this.$nextTick(() => {
                this.$refs.messageInput.focus();
            });
        },

        cancelReply() {
            this.replyingTo = null;
        },

        handleFileSelect(e) {
            const file = e.target.files[0];
            if (!file) return;

            this.selectedImage = file;
            this.selectedImagePreview = URL.createObjectURL(file);
            
            e.target.value = '';
        },

        removeImage() {
            this.selectedImage = null;
            this.selectedImagePreview = null;
        },

        openImageFull(path) {
            const url = path.startsWith('blob:') ? path : '/storage/' + path;
            window.open(url, '_blank');
        },

        get statusLabel() {
            const labels = {
                'bot':      'Asisten Virtual • Online',
                'waiting':  '⏳ Menunggu Admin',
                'active':   '👩 Admin • Online',
                'resolved': '✅ Percakapan Selesai',
            };
            return labels[this.conversationStatus] || 'Online';
        },

        get lastBotMessageId() {
            const botMessages = this.messages.filter(m => m.sender_type !== 'user');
            return botMessages.length > 0 ? botMessages[botMessages.length - 1].id : null;
        },

        isNearBottom() {
            const el = this.$refs.messagesContainer;
            if (!el) return true;

            return el.scrollHeight - el.scrollTop - el.clientHeight < 10;
        },

        // untuk geser kanan biar reply ya
        gestureStart(e, message) {
            this.isSwiping = false;
            this.swipe.startX = e.touches ? e.touches[0].clientX : e.clientX;
            this.swipe.activeId = message.id;
            this.swipe.currentX = 0;

            this.longPressTimeout = setTimeout(() => {
                if (this.isSwiping) return;
                this.selectedMessage = message;
                this.showActionMenu = true;
                this.cancelReply();
                if (navigator.vibrate) navigator.vibrate(10);
            }, 400);
        },

        gestureMove(e, id) {
            if (this.swipe.activeId !== id) return;

            const currentX = e.touches ? e.touches[0].clientX : e.clientX;
            let diff = currentX - this.swipe.startX;

            if (Math.abs(diff) > 10) {
                this.isSwiping = true;
                clearTimeout(this.longPressTimeout);
            }

            if (diff < 0) diff = 0;

            const msg = this.messages.find(m => m.id === id);
            const maxSwipe = msg?.sender_type === 'user' ? 40 : 100;
            if (diff > maxSwipe) diff = maxSwipe;

            this.swipe.currentX = diff;
        },

        gestureEnd(message) {
            clearTimeout(this.longPressTimeout);

            const threshold = message.sender_type === 'user' ? 20 : 60;
            if (this.swipe.currentX > threshold) this.setReply(message);

            this.swipe.currentX = 0;
            this.swipe.activeId = null;
            this.isSwiping = false;
        },

        cancelLongPress() {
            clearTimeout(this.longPressTimeout);
        },

        copyText(text) {
            navigator.clipboard.writeText(text);
            this.showActionMenu = false;
        },

        async deleteMessage(message) {
            this.messages = this.messages.filter(m => m.id !== message.id);
            this.showActionMenu = false;

            try {
                await fetch(`/message/delete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ id: message.id })
                });
            } catch(e) {
                console.error(e);
            }
        },

        async unsendMessage(message) {
            this.showActionMenu = false;

            try {
                const res = await fetch(`/chat/${this.conversationId}/unsend/${message.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                const data = await res.json();

                if (data.success) {
                    this.messages = this.messages.filter(m => m.id !== message.id);
                }
            } catch(e) {
                console.error(e);
            }
        },

        async loadMessages() {
            const res = await fetch(`/chat/open`, {
                method: 'POST'
            });

            const data = await res.json();
            this.messages = data.messages;
        },

        async reactToMessage(emoji) {
            if (!this.selectedMessage) return;
            const id = this.selectedMessage.id;

            if (this.reactions[id] === emoji) {
                delete this.reactions[id];
            } else {
                this.reactions[id] = emoji;
            }
            this.reactions = { ...this.reactions };
            this.showActionMenu = false;

            try {
                await fetch(`/chat/${this.conversationId}/react/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ emoji })
                });
            } catch(e) {
                console.error(e);
            }
        },

        init() {
            const setVH = () => {
                let vh = window.innerHeight * 0.01;
                document.documentElement.style.setProperty('--vh', `${window.innerHeight * 0.01}px`);
                // this.scrollToBottom(true);
            };

            window.addEventListener('resize', setVH);

            setVH();

            this.$nextTick(() => {
                lucide.createIcons();
                this.updateFloatingDate();
                this.scrollToBottom(true);
            });

            if (window.Echo) {
                window.Echo.private(`conversation.${this.conversationId}`)
                    .listen('MessageSent', (e) => {
                        this.isTyping = false;
                        this.messages.push(e);
                        this.$nextTick(() => this.scrollToBottom());
                    });
            }

            this.$nextTick(() => {
                if (this.$refs.messageInput) {
                    this.$refs.messageInput.addEventListener('focus', () => {
                        setTimeout(() => {
                            if (!this.replyingTo) {
                                this.scrollToBottom(true);
                            }
                        }, 300);
                    });
                }
            });

            this.messages.forEach(m => {
                if (m.metadata && m.metadata.reaction) {
                    this.reactions[m.id] = m.metadata.reaction;
                }
            });

            this.messages = this.messages.map(m => {
                if (m.is_unsent) {
                    return { ...m, content: 'Pesan dibatalkan' };
                }
                return m;
            });

            this.$refs.messagesContainer.addEventListener('scroll', () => {
                const el = this.$refs.messagesContainer;
                this.userScrolling = el.scrollTop + el.clientHeight < el.scrollHeight - 50;

                this.showFloatingDate = true;
                this.updateFloatingDate();
                
                clearTimeout(this.scrollTimeout);

                this.scrollTimeout = setTimeout(() => {
                    this.showFloatingDate = false;
                }, 1000);
            });
        },

        updateViewportHeight() {
            let vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
            if(this.$refs.messagesContainer) this.scrollToBottom(true);
        },

        scrollToBottom(force = false) {
            const el = this.$refs.messagesContainer;
            if (!el) return;

            if (this.replyingTo && !force) return;

            if (force || (!this.userScrolling && this.isNearBottom())) {
                el.scrollTo({
                    top: el.scrollHeight,
                    behavior: 'smooth'
                });
            }
        },

        handleEnter(e) {
            if (!this.inputMessage.trim()) return;
            this.sendMessage();
        },

        async refreshQuestions(messageId) {
            try {
                const res = await fetch(`/chat/${this.conversationId}/refresh-welcome`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                const data = await res.json();

                const index = this.messages.findIndex(m => m.id === messageId);
                if (index !== -1) {
                    this.messages[index].metadata.questions = data.bot_reply.metadata.questions;
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.$nextTick(() => {
                    lucide.createIcons();
                    // this.$refs.messageInput.focus(); 
                });
            }
        },

        async sendMessage() {
            if (!this.inputMessage.trim() && !this.selectedImage || this.sending) return;

            const content = this.inputMessage.trim();
            this.inputMessage = '';
            this.sending = true;
            this.$refs.messageInput.style.height ='auto';

            const tempMsg = {
                id: 'temp_' + Date.now(),
                content: content,
                sender_type: 'user',
                message_type: this.selectedImage ? 'image' : 'text',
                image_path: this.selectedImage ? this.selectedImagePreview : null,
                created_at: new Date().toISOString(),
            };

            this.messages.push(tempMsg);

            const imageToSend = this.selectedImage;
            const previewUrl = this.selectedImagePreview;
            this.selectedImage = null;
            this.selectedImagePreview = null;

            this.$nextTick(() => {
                if (!this.replyingTo) {
                    this.scrollToBottom(true);
                }
                this.$refs.messageInput.focus();
            });

            try {
                const formData = new FormData();
                if (content) formData.append('content', content);
                if (imageToSend) formData.append('image', imageToSend);
                if (this.replyingTo) formData.append('reply_to', this.replyingTo.id);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                const res = await fetch(`/chat/${this.conversationId}/send`, {
                    method: 'POST',
                    body: formData,
                });

                const data = await res.json();

                const idx = this.messages.findIndex(m => m.id === tempMsg.id);
                if (idx !== -1) this.messages[idx] = data.user_message;

                if (data.bot_reply) {
                    this.isTyping = true;
                    setTimeout(() => {
                        this.isTyping = false;
                        this.messages.push(data.bot_reply);
                        if (data.bot_reply.metadata?.escalate || this.conversationStatus === 'waiting') {
                            this.conversationStatus = 'waiting';
                        }
                        this.$nextTick(() => this.scrollToBottom());
                    }, 900);
                }

            } catch(e) {
                console.error(e);
                this.isTyping = false;
            } finally {
                this.sending = false;
                this.$nextTick(() => this.$refs.messageInput.focus());
            }

            this.replyingTo = null;
        },

        async sendQuickReply(option) {
            if (this.sending) return;

            this.sending = true;

            const tempMsg = {
                id: 'temp_' + Date.now(),
                content: option,
                sender_type: 'user',
                message_type: 'text',
                created_at: new Date().toISOString(),
                reply_to: this.replyingTo
            };

            this.messages.push(tempMsg);
            this.scrollToBottom(true);

            try {
                const res = await fetch(`/chat/${this.conversationId}/quick-reply`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ option })
                });

                const data = await res.json();

                const idx = this.messages.findIndex(m => m.id === tempMsg.id);
                if (idx !== -1 && data.user_message) {
                    this.messages[idx] = data.user_message;
                }

                if (data.bot_reply) {
                    this.isTyping = true;

                    setTimeout(() => {
                        this.isTyping = false;
                        this.messages.push(data.bot_reply);

                        if (data.bot_reply.metadata?.escalate) {
                            this.conversationStatus = 'waiting';
                        }

                        this.scrollToBottom(true);
                    }, 700);
                }

                if (data.escalated) {
                    this.conversationStatus = 'waiting';
                }

            } catch (e) {
                console.error(e);
                this.isTyping = false;
            } finally {
                this.sending = false;
            }
        },

        autoResize(el) {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 112) + 'px';
        },

        formatTime(dateString) {
            return new Date(dateString).toLocaleTimeString('id-ID', {
                hour: '2-digit', minute: '2-digit'
            });
        },
    }
}
</script>

<script>
    lucide.createIcons();
</script>
</body>
</html>