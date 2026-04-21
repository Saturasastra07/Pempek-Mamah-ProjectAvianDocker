<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Kata Sandi - Pempek Mamah Dhani</title>
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
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
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
        newPassword: '',
        showPassword: false,
        otpValues: ['', '', '', '', '', ''],
        timer: 34,
        loading: false,
        submittedStep1: false,
        errorMsg: '',
        userPhone: '{{ auth()->user()->phone }}',

        get hasMinLength() { return this.newPassword.length >= 6; },
        get hasNumber() { return /\d/.test(this.newPassword); },
        get hasUpper() { return /[A-Z]/.test(this.newPassword); },
        get isAllValid() { return this.hasMinLength && this.hasNumber && this.hasUpper; },

        startTimer() {
            this.timer = 34;
            let itv = setInterval(() => {
                if (this.timer > 0) this.timer--;
                else clearInterval(itv);
            }, 1000);
        },

        handleStep1() {
            this.submittedStep1 = true;
            if (this.isAllValid) {
                this.loading = true;
                const appVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', { 'size': 'invisible' });
                
                firebase.auth().signInWithPhoneNumber(this.userPhone, appVerifier)
                    .then((confirmationResult) => {
                        window.confirmationResult = confirmationResult;
                        this.loading = false;
                        this.step = 2;
                        this.startTimer();
                    })
                    .catch((error) => {
                        this.loading = false;
                        alert('Gagal kirim OTP: ' + error.message);
                    });
            }
        },

        handleOtpInput(index, event) {
            let cleaned = event.target.value.replace(/[^0-9]/g, '');
            this.otpValues[index] = cleaned.slice(-1);
            event.target.value = this.otpValues[index];
            if (this.otpValues[index] !== '' && index < 5) {
                document.getElementById('otp' + (index + 1)).focus();
            }
        },

        handleBackspace(index, event) {
            if (event.key === 'Backspace' && this.otpValues[index] === '' && index > 0) {
                const prev = document.getElementById('otp' + (index - 1));
                prev.focus();
                this.otpValues[index - 1] = '';
            }
        },

        verifyOtp() {
        const code = this.otpValues.join('');
        if (code.length < 6) return;
        this.loading = true;

        window.confirmationResult.confirm(code)
            .then(() => {
                // Jika OTP Firebase tembus, baru lari ke Controller Laravel untuk update password
                fetch('{{ route("profile.update-password-final") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ new_password: this.newPassword })
                })
                .then(res => res.json())
                .then(data => {
                    this.loading = false;
                    if(data.success) this.step = 3;
                });
            })
            .catch(() => {
                this.loading = false;
                alert('Kode OTP salah!');
            });
    }

    }" x-init="lucide.createIcons()" class="max-w-md mx-auto min-h-screen flex flex-col px-8">

        {{-- Progress Bar (3 Step) --}}
        <div class="pt-16 mb-12 px-2">
            <div class="relative flex items-center justify-between w-full h-1 bg-gray-100 rounded-full">
                <div class="absolute h-1 bg-[#e91e63] transition-all duration-1000 ease-in-out rounded-full"
                    :style="'width: ' + (step === 1 ? '0%' : (step === 2 ? '50%' : '100%'))"></div>
                
                <template x-for="i in [1, 2, 3]">
                    <div class="relative z-10 w-5 h-5 rounded-full border-2 transition-all duration-500 flex items-center justify-center shadow-sm"
                        :class="step >= i ? 'bg-[#e91e63] border-[#e91e63] scale-110' : 'bg-white border-gray-200'">
                        <i x-show="step > i" data-lucide="check" class="w-3.5 h-3.5 text-white" stroke-width="4"></i>
                    </div>
                </template>
            </div>
        </div>

        <div class="relative flex-1">

            {{-- ===== STEP 1: MASUKKAN PASSWORD ===== --}}
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-500" class="w-full">
                <h2 class="text-2xl font-black text-gray-900 mb-2 leading-tight">Ubah kata sandi Anda</h2>
                <p class="text-[12px] text-gray-400 font-medium mb-10 leading-relaxed">
                    Silakan masukkan kata sandi baru Anda untuk mengamankan akun Pempek Mamah Dhani.
                </p>

                <div class="mb-8">
                    <div class="relative border-2 rounded-2xl p-4 transition-all"
                         :class="submittedStep1 && !isAllValid ? 'border-red-500' : 'border-[#e91e63]'">
                        <div class="flex items-center gap-3">
                            <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                            <input :type="showPassword ? 'text' : 'password'" 
                                   x-model="newPassword"
                                   class="flex-1 bg-transparent outline-none font-semibold text-gray-800"
                                   placeholder="Kata sandi baru">
                            <button @click="showPassword = !showPassword" type="button">
                                <i :data-lucide="showPassword ? 'eye-off' : 'eye'" class="w-5 h-5 text-gray-400"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Checklist Validasi --}}
                <div class="space-y-3 mb-10">
                    <p class="text-[12px] font-bold transition-colors duration-300 flex items-center gap-2"
                    :class="isAllValid ? 'text-green-600' : 'text-gray-400'">
                        <i data-lucide="check-circle" class="w-4 h-4 transition-all duration-500" 
                        :class="isAllValid ? 'text-green-500 scale-110' : 'text-gray-200'"></i>
                        Kata sandi Anda harus berisi:
                    </p>
                    <div class="ml-6 space-y-2">
                        <div class="flex items-center gap-2 text-[12px] font-medium transition-colors"
                             :class="hasMinLength ? 'text-gray-800' : (submittedStep1 ? 'text-red-500' : 'text-gray-400')">
                            <i data-lucide="check" x-show="hasMinLength" class="w-3 h-3 text-[#e91e63]"></i>
                            Minimal 6 karakter
                        </div>
                        <div class="flex items-center gap-2 text-[12px] font-medium transition-colors"
                             :class="hasNumber ? 'text-gray-800' : (submittedStep1 ? 'text-red-500' : 'text-gray-400')">
                            <i data-lucide="check" x-show="hasNumber" class="w-3 h-3 text-[#e91e63]"></i>
                            Mengandung angka
                        </div>
                        <div class="flex items-center gap-2 text-[12px] font-medium transition-colors"
                             :class="hasUpper ? 'text-gray-800' : (submittedStep1 ? 'text-red-500' : 'text-gray-400')">
                            <i data-lucide="check" x-show="hasUpper" class="w-3 h-3 text-[#e91e63]"></i>
                            Satu huruf besar (Kapital)
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('profile') }}"
                       class="flex-1 bg-black bg-opacity-70 hover:bg-opacity-30 rounded-xl 
                       py-4 text-xs font-black text-white uppercase tracking-widest text-center flex items-center justify-center">
                        Kembali
                    </a>
                    <button type="button"
                            @click="handleStep1()"
                            :disabled="loading"
                            class="flex-[1.5] bg-[#e91e63] hover:bg-opacity-70 text-white py-4 rounded-xl text-xs 
                            font-black uppercase tracking-widest shadow-md active:scale-95 transition-all disabled:opacity-60">
                        <span x-show="!loading">Lanjutkan</span>
                        <span x-show="loading" x-cloak>Mengirim...</span>
                    </button>
                </div>
            </div>

            {{-- ===== STEP 2: VERIFIKASI (OTP) ===== --}}
            <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-500" class="w-full">
                <h2 class="text-2xl font-black text-gray-900 mb-2 leading-tight">Verifikasi kata sandi</h2>
                <p class="text-[11px] text-gray-400 font-medium mb-6 leading-relaxed">
                    Kode verifikasi telah dikirimkan untuk mengonfirmasi perubahan sandi Anda.
                </p>

                {{-- Box Info Nomor (Sesuai Permintaan) --}}
                <div class="bg-pink-50 border border-pink-100 rounded-xl p-4 mb-10 flex items-center gap-4">
                    <div class="w-10 h-10 bg-[#e91e63] rounded-full flex items-center justify-center shadow-sm">
                        <i data-lucide="smartphone" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-[#e91e63] font-bold uppercase tracking-wider">Nomor Terdaftar</p>
                        <p class="text-sm font-black text-gray-800" x-text="userPhone"></p>
                    </div>
                </div>

                <div class="flex justify-center gap-2 mb-10">
                    <template x-for="(v, i) in otpValues">
                        <input type="text" :id="'otp'+i" x-model="otpValues[i]" 
                               @input="handleOtpInput(i, $event)" @keydown="handleBackspace(i, $event)"
                               inputmode="numeric" maxlength="1" 
                               class="w-12 h-14 border-2 border-gray-100 rounded-xl text-center text-xl font-black focus:border-[#e91e63] outline-none transition-all shadow-sm">
                    </template>
                </div>

                <p class="text-center text-[11px] text-gray-400 font-medium mb-12">
                    Tidak menerima kode? 
                    <button @click="startTimer()" :disabled="timer > 0" class="text-black font-black hover:underline disabled:opacity-30">
                        Kirim ulang (<span x-text="timer + 's'"></span>)
                    </button>
                </p>

                <button @click="verifyOtp()" :disabled="loading"
                        class="w-full bg-[#e91e63] text-white py-4 rounded-xl text-sm font-black uppercase tracking-widest shadow-xl active:scale-95 transition-all">
                    <span x-show="!loading">Verifikasi OTP</span>
                    <span x-show="loading">Memverifikasi...</span>
                </button>
            </div>

            {{-- ===== STEP 3: BERHASIL ===== --}}
            <div x-show="step === 3" x-cloak x-transition:enter="transition ease-out duration-700" class="flex flex-col items-center text-center pt-10">
                <div class="mb-10 relative">
                    <div class="glow-effect w-48 h-48 mx-auto relative z-10">
                        <img src="{{ asset('assets/images/mamah_selamat(1).png') }}" class="w-full h-full object-contain">
                    </div>
                    <div class="absolute -top-4 -right-4 animate-bounce text-4xl">🎉</div>
                </div>
                <h2 class="text-2xl font-black text-gray-900 mb-3 leading-tight">Kata Sandi Diperbarui</h2>
                <p class="text-[11px] text-gray-400 font-medium mb-12">Keamanan akun Anda kini sudah ditingkatkan.</p>
                <a href="{{ route('profile') }}" class="w-full bg-[#e91e63] text-white py-4 rounded-xl font-black text-center shadow-lg active:scale-95">KEMBALI KE PROFIL</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>