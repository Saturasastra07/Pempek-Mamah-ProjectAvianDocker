@auth
<div x-data="chatWidget()" x-init="init()" x-cloak>

    {{-- FAB Button --}}
    <button @click="toggleChat()"
        class="fixed bottom-24 right-4 z-[200] w-14 h-14 bg-[#e91e63] rounded-full shadow-lg shadow-pink-300 flex items-center justify-center active:scale-90 transition-all"
        :class="isOpen ? 'rotate-45' : ''">
        <template x-if="!isOpen">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
        </template>
        <template x-if="isOpen">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </template>

        {{-- Unread badge --}}
        <span x-show="unreadCount > 0"
            x-text="unreadCount"
            class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-black rounded-full flex items-center justify-center">
        </span>
    </button>

    {{-- Chat Window --}}
    <div x-show="isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="fixed bottom-40 right-4 z-[199] w-[340px] max-w-[calc(100vw-2rem)] bg-white rounded-3xl shadow-2xl shadow-black/20 flex flex-col overflow-hidden"
        style="height: 520px;">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-[#880e4f] via-[#e91e63] to-[#f06292] px-4 py-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-white font-black text-sm">Customer Service</p>
                <p class="text-white/70 text-[10px]" x-text="statusLabel"></p>
            </div>
            <button @click="toggleChat()" class="text-white/70 hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto px-4 py-3 space-y-3 bg-gray-50" 
             id="chat-messages"
             x-ref="messagesContainer">

            <template x-if="loading">
                <div class="flex justify-center py-8">
                    <div class="w-6 h-6 border-2 border-[#e91e63] border-t-transparent rounded-full animate-spin"></div>
                </div>
            </template>

            <template x-for="message in messages" :key="message.id">
                <div>
                    {{-- Bot / Admin message --}}
                    <template x-if="message.sender_type !== 'user'">
                        <div class="flex items-end gap-2">
                            <div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center mb-1"
                                :class="message.sender_type === 'bot' ? 'bg-[#e91e63]' : 'bg-blue-500'">
                                <template x-if="message.sender_type === 'bot'">
                                    <span class="text-white text-[10px] font-black">🤖</span>
                                </template>
                                <template x-if="message.sender_type === 'admin'">
                                    <span class="text-white text-[10px] font-black">👩</span>
                                </template>
                            </div>

                            <div class="max-w-[75%]">
                                {{-- System message --}}
                                <template x-if="message.message_type === 'system'">
                                    <div class="text-center w-full">
                                        <span class="text-[10px] text-gray-400 bg-gray-100 px-3 py-1 rounded-full" x-text="message.content"></span>
                                    </div>
                                </template>

                                {{-- Text message --}}
                                <template x-if="message.message_type === 'text' || message.message_type === 'info_card'">
                                    <div class="bg-white rounded-2xl rounded-bl-sm px-3 py-2.5 shadow-sm">
                                        <p class="text-xs text-gray-800 leading-relaxed whitespace-pre-line" x-text="message.content"></p>
                                        <p class="text-[9px] text-gray-400 mt-1 text-right" x-text="formatTime(message.created_at)"></p>
                                    </div>
                                </template>

                                {{-- Quick reply message --}}
                                <template x-if="message.message_type === 'quick_reply'">
                                    <div>
                                        <div class="bg-white rounded-2xl rounded-bl-sm px-3 py-2.5 shadow-sm mb-2">
                                            <p class="text-xs text-gray-800 leading-relaxed whitespace-pre-line" x-text="message.content"></p>
                                        </div>
                                        {{-- Quick reply options --}}
                                        <template x-if="message.id === lastBotMessageId && message.metadata?.options">
                                            <div class="flex flex-wrap gap-1.5 mt-1">
                                                <template x-for="option in message.metadata.options" :key="option">
                                                    <button @click="sendQuickReply(option)"
                                                        class="text-[11px] font-semibold text-[#e91e63] border border-[#e91e63] rounded-full px-3 py-1 hover:bg-[#e91e63] hover:text-white transition-all active:scale-95">
                                                        <span x-text="option"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- User message --}}
                    <template x-if="message.sender_type === 'user'">
                        <div class="flex justify-end">
                            <div class="max-w-[75%] bg-[#e91e63] rounded-2xl rounded-br-sm px-3 py-2.5">
                                <p class="text-xs text-white leading-relaxed" x-text="message.content"></p>
                                <p class="text-[9px] text-white/70 mt-1 text-right" x-text="formatTime(message.created_at)"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Typing indicator --}}
            <div x-show="isTyping" class="flex items-end gap-2">
                <div class="w-7 h-7 bg-[#e91e63] rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-[10px]">🤖</span>
                </div>
                <div class="bg-white rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm">
                    <div class="flex gap-1">
                        <div class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                        <div class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                        <div class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input area --}}
        <div class="bg-white border-t border-gray-100 px-3 py-3">
            <div class="flex items-end gap-2">
                <textarea 
                    x-model="inputMessage"
                    @keydown.enter.prevent="inputMessage.trim() && sendMessage()"
                    rows="1"
                    placeholder="Ketik pesan..."
                    class="flex-1 resize-none text-xs text-gray-800 placeholder:text-gray-300 outline-none bg-gray-50 rounded-2xl px-4 py-2.5 max-h-24 leading-relaxed"
                    style="scrollbar-width: none;">
                </textarea>
                <button @click="sendMessage()"
                    :disabled="!inputMessage.trim() || sending"
                    class="w-9 h-9 bg-[#e91e63] rounded-full flex items-center justify-center flex-shrink-0 active:scale-90 transition-all disabled:opacity-40">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function chatWidget() {
    return {
        isOpen: false,
        loading: false,
        sending: false,
        isTyping: false,
        messages: [],
        inputMessage: '',
        conversationId: null,
        unreadCount: 0,
        conversationStatus: 'bot',

        get statusLabel() {
            const labels = {
                'bot':      '🤖 Asisten Virtual • Online',
                'waiting':  '⏳ Menunggu Admin',
                'active':   '👩 Admin • Online',
                'resolved': '✅ Selesai',
            };
            return labels[this.conversationStatus] || 'Online';
        },

        get lastBotMessageId() {
            const botMessages = this.messages.filter(m => m.sender_type !== 'user');
            return botMessages.length > 0 ? botMessages[botMessages.length - 1].id : null;
        },

        async init() {
            // Listen for real-time messages via Echo
            if (window.Echo && this.conversationId) {
                this.listenForMessages();
            }
        },

        async toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen && this.messages.length === 0) {
                await this.openConversation();
            }
            if (this.isOpen) {
                this.unreadCount = 0;
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        async openConversation() {
            this.loading = true;
            try {
                const res = await fetch('/chat/open', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ order_id: null })
                });
                const data = await res.json();
                this.conversationId = data.conversation.id;
                this.conversationStatus = data.conversation.status;
                this.messages = data.messages;
                this.listenForMessages();
            } catch(e) {
                console.error('Chat error:', e);
            } finally {
                this.loading = false;
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        listenForMessages() {
            if (!window.Echo || !this.conversationId) return;

            window.Echo.private(`conversation.${this.conversationId}`)
                .listen('MessageSent', (e) => {
                    this.messages.push(e);
                    this.isTyping = false;

                    if (!this.isOpen) {
                        this.unreadCount++;
                    }

                    this.$nextTick(() => this.scrollToBottom());
                });
        },

        async sendMessage() {
            if (!this.inputMessage.trim() || this.sending) return;

            const content = this.inputMessage;
            this.inputMessage = '';
            this.sending = true;
            this.isTyping = true;

            // Optimistic update
            this.messages.push({
                id: Date.now(),
                content,
                sender_type: 'user',
                message_type: 'text',
                created_at: new Date().toISOString(),
            });
            this.$nextTick(() => this.scrollToBottom());

            try {
                const res = await fetch(`/chat/${this.conversationId}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ content })
                });
                const data = await res.json();

                if (data.bot_reply) {
                    // Simulasi delay bot mengetik
                    setTimeout(() => {
                        this.isTyping = false;
                        this.messages.push(data.bot_reply);
                        this.$nextTick(() => this.scrollToBottom());
                    }, 800);
                } else {
                    this.isTyping = false;
                }

                if (data.conversation_status) {
                    this.conversationStatus = data.conversation_status;
                }

            } catch(e) {
                this.isTyping = false;
                console.error(e);
            } finally {
                this.sending = false;
            }
        },

        async sendQuickReply(option) {
            this.inputMessage = option;
            await this.sendMessage();
        },

        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },

        formatTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        },
    }
}
</script>
@endauth