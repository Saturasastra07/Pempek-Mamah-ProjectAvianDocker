<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Nomor - Pempek Mamah Dhani</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://www.gstatic.com/firebasejs/10.8.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.8.0/firebase-auth-compat.js"></script>
    <script>
    const firebaseConfig = {
        apiKey: "AIzaSyCnpZExUtT5yetpicVoL3MNYNLrbeEcvlQ",
        authDomain: "pempekmamahdhani.firebaseapp.com",
        projectId: "pempekmamahdhani",
        storageBucket: "pempekmamahdhani.firebasestorage.app",
        messagingSenderId: "902398444326",
        appId: "1:902398444326:web:3dadd27f7850b09097f7e7",
        measurementId: "G-YNMP0F9J3X"
    };
    firebase.initializeApp(firebaseConfig);
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/css/intlTelInput.css">
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/intlTelInput.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .iti { width: 100%; }
        .iti__active { background-color: #fce4ec; }
        .iti__country-list { width: 300px; border-radius: 16px; border: none; }
        .iti__search-input { padding: 10px; border-radius: 8px; border: 1px solid #fce4ec; margin-bottom: 5px; }
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
</head>
<body class="bg-white overflow-hidden">
    <div id="recaptcha-container"></div>

    <div x-data="{
        step: 1,
        phoneNumber: '',
        otpValues: ['', '', '', '', '', ''],
        timer: 34,
        loading: false,
        errorMsg: '',

        startTimer() {
            let itv = setInterval(() => {
                if (this.timer > 0) this.timer--;
                else clearInterval(itv);
            }, 1000);
        },

        handleOtpInput(index, event) {
            let cleaned = event.target.value.replace(/[^0-9]/g, '');
            this.otpValues[index] = cleaned.slice(-1);
            event.target.value = this.otpValues[index];
            if (this.otpValues[index] !== '' && index < 5) {
                this.$nextTick(() => {
                    const next = document.getElementById('otp' + (index + 1));
                    if (next) next.focus();
                });
            }
        },

        handleBackspace(index, event) {
            if (event.key === 'Backspace') {
                if (this.otpValues[index] === '' && index > 0) {
                    this.$nextTick(() => {
                        const prev = document.getElementById('otp' + (index - 1));
                        if (prev) {
                            prev.focus();
                            this.otpValues[index - 1] = '';
                            prev.value = '';
                        }
                    });
                } else {
                    this.otpValues[index] = '';
                    event.target.value = '';
                }
            }
        },

        sendOtp() {
            const fullPhone = window.itl.getNumber(); 
            
            if (!fullPhone) {
                this.errorMsg = 'Masukkan nomor telepon yang valid.';
                return;
            }
            this.errorMsg = '';
            this.loading = true;

            try {
                const appVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', { 
                    'size': 'invisible' 
                });

                firebase.auth().signInWithPhoneNumber(fullPhone, appVerifier)
                    .then((confirmationResult) => {
                        window.confirmationResult = confirmationResult;
                        this.phoneNumber = fullPhone;
                        this.loading = false;
                        this.step = 2;
                        this.startTimer();
                    })
                    .catch((error) => {
                        this.loading = false;
                        this.errorMsg = 'Gagal kirim SMS: ' + error.message;
                        console.error(error);
                    });
            } catch (e) {
                this.loading = false;
                this.errorMsg = 'Kesalahan sistem: ' + e.message;
            }
        },

        async verifyCode() {
            const code = this.otpValues.join('');
            if (code.length < 6) return;
            this.loading = true;

            try {
                await window.confirmationResult.confirm(code);
                fetch('/settings/update-phone', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                    },
                    body: JSON.stringify({ phone: this.phoneNumber })
                })
                .then(res => res.json())
                .then(() => {
                    this.loading = false;
                    this.step = 3;
                })
                .catch(() => {
                    this.loading = false;
                    this.errorMsg = 'Gagal menyimpan nomor ke sistem, coba lagi.';
                });

            } catch (error) {
                this.loading = false;
                this.errorMsg = 'Kode OTP salah!';
            }
        }

    }" x-init="lucide.createIcons()" class="max-w-md mx-auto min-h-screen flex flex-col px-8">

        {{-- Progress Bar --}}
        <div class="pt-16 mb-12 px-2">
            <div class="relative flex items-center justify-between w-full h-1 bg-gray-100 rounded-full">
                <div class="absolute h-1 bg-[#e91e63] transition-all duration-1000 ease-in-out rounded-full"
                    :style="'width: ' + (step === 1 ? '0%' : (step === 2 ? '50%' : '100%'))"></div>
                <div class="relative z-10 w-5 h-5 rounded-full border-2 transition-all duration-500 flex items-center justify-center shadow-sm"
                    :class="step >= 1 ? 'bg-[#e91e63] border-[#e91e63] scale-110' : 'bg-white border-gray-200'">
                    <i x-show="step > 1" data-lucide="check" class="w-3.5 h-3.5 text-green-400" stroke-width="4"></i>
                </div>
                <div class="relative z-10 w-5 h-5 rounded-full border-2 transition-all duration-500 flex items-center justify-center shadow-sm"
                    :class="step >= 2 ? 'bg-[#e91e63] border-[#e91e63] scale-110' : 'bg-white border-gray-200'">
                    <i x-show="step > 2" data-lucide="check" class="w-3.5 h-3.5 text-green-400" stroke-width="4"></i>
                </div>
                <div class="relative z-10 w-5 h-5 rounded-full border-2 transition-all duration-500 flex items-center justify-center shadow-sm"
                    :class="step >= 3 ? 'bg-[#e91e63] border-[#e91e63] scale-110' : 'bg-white border-gray-200'">
                    <i x-show="step > 2" data-lucide="check" class="w-3.5 h-3.5 text-green-400" stroke-width="4"></i>
                </div>
            </div>
        </div>

        <div class="relative flex-1">

            {{-- ===== STEP 1 ===== --}}
            <div x-show="step === 1"
                x-transition:enter="transition ease-out duration-500 delay-300"
                x-transition:enter-start="opacity-0 translate-x-8"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-300 absolute w-full"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 -translate-x-8">

                <h2 class="text-2xl font-black text-gray-900 mb-2 leading-tight">Masukkan nomor telepon Anda.</h2>
                <p class="text-[12px] text-gray-400 font-medium mb-10 leading-relaxed">
                    Silakan masukkan nomor telepon Anda untuk menggunakan layanan Pempek Mamah Dhani.
                </p>

                <div class="mb-12">
                    <label class="text-[12px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block">Nomor Telepon</label>
                    <div class="border-b-2 border-gray-100 focus-within:border-black transition-colors pb-2">
                        <input type="tel" id="phoneInput"
                               class="w-full text-lg font-md outline-none placeholder:text-gray-200"
                               placeholder="812 345 678">
                    </div>
                    <p x-show="errorMsg" x-text="errorMsg" class="text-[11px] text-red-500 mt-2 ml-1"></p>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('profile') }}"
                       class="flex-1 bg-black bg-opacity-70 hover:bg-opacity-30 rounded-xl 
                       py-4 text-xs font-black text-white uppercase tracking-widest text-center flex items-center justify-center">
                        Kembali
                    </a>
                    <button type="button"
                            @click="sendOtp()"
                            :disabled="loading"
                            class="flex-[1.5] bg-[#e91e63] hover:bg-opacity-70 text-white py-4 rounded-xl text-xs 
                            font-black uppercase tracking-widest shadow-md active:scale-95 transition-all disabled:opacity-60">
                        <span x-show="!loading">Lanjutkan</span>
                        <span x-show="loading" x-cloak>Mengirim...</span>
                    </button>
                </div>

                <div class="absolute -bottom-40 -right-10 w-80 h-80 pointer-events-none z-0">
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[150%] h-[150%] animate-pulse" 
                        style="background: radial-gradient(circle, rgba(233, 30, 99, 0.4) 0%, rgba(233, 30, 99, 0) 70%); filter: blur(50px); z-index: 1;">
                    </div>

                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full animate-pulse" 
                        style="background: radial-gradient(circle, rgba(255, 255, 255, 0.5) 0%, rgba(255, 255, 255, 0) 60%); filter: blur(30px); animation-delay: 0.5s; z-index: 2;">
                    </div>

                    <img src="{{ asset('assets/images/mamah_tunjukAtas.png') }}" 
                        alt="Mamah Dhani" 
                        class="w-full h-full object-contain object-bottom relative"
                        style="z-index: 10;">
                </div>
            </div>

            <div x-show="step === 2" x-cloak
                x-transition:enter="transition ease-out duration-500 delay-300"
                x-transition:enter-start="opacity-0 translate-x-8"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-300 absolute w-full"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 -translate-x-8">

                <h2 class="text-2xl font-black text-gray-900 mb-2 leading-tight">Masukkan kode verifikasi OTP</h2>
                <p class="text-[11px] text-gray-400 font-medium mb-10 leading-relaxed">
                    Kode verifikasi telah dikirimkan ke nomor <span class="text-black font-bold" x-text="phoneNumber"></span>
                </p>

                <div class="flex justify-center gap-2 mb-10">
                    <input type="text" id="otp0" x-model="otpValues[0]" @input="handleOtpInput(0, $event)" @keydown="handleBackspace(0, $event)" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="w-12 h-14 border-2 border-gray-100 rounded-xl text-center text-xl font-black focus:border-[#e91e63] outline-none transition-all shadow-sm">
                    <input type="text" id="otp1" x-model="otpValues[1]" @input="handleOtpInput(1, $event)" @keydown="handleBackspace(1, $event)" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="w-12 h-14 border-2 border-gray-100 rounded-xl text-center text-xl font-black focus:border-[#e91e63] outline-none transition-all shadow-sm">
                    <input type="text" id="otp2" x-model="otpValues[2]" @input="handleOtpInput(2, $event)" @keydown="handleBackspace(2, $event)" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="w-12 h-14 border-2 border-gray-100 rounded-xl text-center text-xl font-black focus:border-[#e91e63] outline-none transition-all shadow-sm">
                    <input type="text" id="otp3" x-model="otpValues[3]" @input="handleOtpInput(3, $event)" @keydown="handleBackspace(3, $event)" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="w-12 h-14 border-2 border-gray-100 rounded-xl text-center text-xl font-black focus:border-[#e91e63] outline-none transition-all shadow-sm">
                    <input type="text" id="otp4" x-model="otpValues[4]" @input="handleOtpInput(4, $event)" @keydown="handleBackspace(4, $event)" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="w-12 h-14 border-2 border-gray-100 rounded-xl text-center text-xl font-black focus:border-[#e91e63] outline-none transition-all shadow-sm">
                    <input type="text" id="otp5" x-model="otpValues[5]" @input="handleOtpInput(5, $event)" @keydown="handleBackspace(5, $event)" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="w-12 h-14 border-2 border-gray-100 rounded-xl text-center text-xl font-black focus:border-[#e91e63] outline-none transition-all shadow-sm">
                </div>

                <p x-show="errorMsg" x-text="errorMsg" class="text-[11px] text-red-500 text-center mb-4"></p>

                <p class="text-center text-[11px] text-gray-400 font-medium mb-12">
                    Tidak menerima kode?
                    <button @click="timer = 34; startTimer()" :disabled="timer > 0"
                            class="text-black font-black hover:underline disabled:opacity-30">
                        Kirim ulang (<span x-text="timer + 's'"></span>)
                    </button>
                </p>

                <button @click="verifyCode()"
                        :disabled="loading"
                        class="w-full bg-[#e91e63] text-white py-4 rounded-xl text-[15px] font-black uppercase tracking-widest shadow-xl active:scale-95 transition-all disabled:opacity-60">
                    <span x-show="!loading">Verifikasi OTP</span>
                    <span x-show="loading" x-cloak>Memverifikasi...</span>
                </button>
            </div>

            <div x-show="step === 3" x-cloak
                x-transition:enter="transition ease-out duration-700 delay-300"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100">

                <div class="flex flex-col items-center justify-center pt-10 text-center">
                    <div class="mb-10 pt-10">
                        <div class="relative w-48 h-48 mx-auto">
                            <div class="absolute -top-4 -left-6 animate-bounce z-20">
                                <span class="text-5xl inline-block -rotate-12">&#127881;</span>
                            </div>
                            <div class="absolute top-2 -right-4 animate-pulse z-20">
                                <span class="text-4xl inline-block rotate-12">&#10024;</span>
                            </div>
                            <div class="glow-effect w-full h-full relative z-10">
                                <img src="{{ asset('assets/images/mamah_selamat(1).png') }}"
                                    alt="Selamat"
                                    class="w-full h-full object-contain">
                            </div>
                            <div class="absolute -bottom-2 -right-4 animate-bounce z-20" style="animation-delay: 0.5s">
                                <span class="text-3xl">&#129395;</span>
                            </div>
                        </div>
                    </div>

                    <h2 class="text-2xl font-black text-gray-900 mb-3 leading-tight">Verifikasi Berhasil</h2>
                    <p class="text-[11px] text-gray-400 font-medium mb-12 leading-relaxed">
                        Nomor telepon Anda berhasil diperbarui.
                    </p>

                    <a href="{{ route('profile') }}"
                       class="w-full bg-[#e91e63] text-white py-4 rounded-xl text-[13px] font-black uppercase tracking-widest shadow-xl active:scale-95 transition-all text-center block">
                        Kembali ke Profil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const input = document.querySelector("#phoneInput");
        window.itl = window.intlTelInput(input, {
            initialCountry: "id",
            separateDialCode: true,
            countrySearch: true,
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/utils.js",
        });

        document.addEventListener('alpine:initialized', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>