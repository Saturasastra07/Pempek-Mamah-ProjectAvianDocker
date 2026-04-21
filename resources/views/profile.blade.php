<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Pengguna - Pempek Mamah Dhani</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif;
        }

        [x-cloak] { 
            display: none !important;
        }

        .flex-col-instan {
            display: flex; 
            flex-direction: column;
        }

        [style*="order"] {
            transition: transform 0.15s ease-out;
        }

        .icon-maps-gradient {
            stroke: url(#maps-gradient);
            filter: drop-shadow(0 1px 1px rgba(0,0,0,0.1));
        }
    </style>
</head>
<body class="bg-gray-50 pb-20" 
    x-data="{ 
        showAddressModal: false, 
        addressTab: 'list',
        currentAddress: {},
        addresses: {{ json_encode($allAddresses ?? []) }},
        showPhotoModal: false,
        showCropModal: false,
        selectedImage: null,
        cropper: null,

        initCropper() {
            const image = document.getElementById('image-to-crop');
            this.cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 2,
                background: false,
                autoCropArea: 1,
            });
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.selectedImage = e.target.result;
                    this.showPhotoModal = false;
                    this.showCropModal = true;
                    this.$nextTick(() => this.initCropper());
                };
                reader.readAsDataURL(file);
            }
        },

        async saveCroppedImage() {
            if (!this.cropper) return;
            this.loading = true;

            try {
                const canvas = this.cropper.getCroppedCanvas({ width: 400, height: 400 });
                const base64Image = canvas.toDataURL('image/jpeg', 0.8);

                const response = await fetch('{{ route("profile.update-photo") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ photo: base64Image })
                });

                const data = await response.json();

                if (data.success) {
                    this.showCropModal = false;
                    location.reload();
                } else {
                    alert('Gagal simpan: ' + (data.message || 'Error server'));
                }
            } catch (error) {
                console.error('Error Detail:', error);
                alert('Gagal mengunggah foto. Cek koneksi atau server kamu.');
            } finally {
                this.loading = false;
            }
        },

        get activeAddress() {
            return this.addresses.find(item => item.is_default == 1) || null;
        },
        
        async setMainAddress(id) {
            try {
                let newActiveItem = null;
                this.addresses.forEach(item => {
                    if(item.id === id) {
                        item.is_default = 1; // Jadikan utama
                        newActiveItem = item;
                    } else {
                        item.is_default = 0; // Matikan yang lain
                    }
                });

                if(newActiveItem) {
                    localStorage.setItem('globalFullAddress', newActiveItem.full_address);
                    localStorage.setItem('globalUserAddress', newActiveItem.district + ' ' + newActiveItem.city);
                    
                    if(Alpine.store('userStore')) {
                        Alpine.store('userStore').updateAddress(newActiveItem.full_address, newActiveItem.district + ' ' + newActiveItem.city);
                    }
                }
                    
                fetch('/set-default-address/' + id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });
                
            } catch(e) { console.error(e); }
        }
    }">

    <nav class="bg-white flex items-center border-b border-gray-100 shadow-sm sticky top-0 z-50 h-16">
        <a href="{{ route('home') }}?sidebar=open" class="w-16 h-full flex items-center justify-center text-gray-800 hover:text-[#e91e63] transition-colors">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </a>
        <div class="flex-1 flex justify-center">
            <h1 class="text-base font-black text-gray-900 uppercase tracking-tight">Pusat Pengguna</h1>
        </div>
        <div class="w-16"></div>
    </nav>

    <div class="bg-black/85 px-6 py-8 flex items-center gap-5">
        <div class="flex-shrink-0">
            <div class="w-[85px] h-[85px] rounded-full overflow-hidden ">
                <img src="{{ auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=fce4ec&color=e91e63' }}" 
                 class="w-full h-full object-cover"
                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=fce4ec&color=e91e63'">
            </div>
        </div>

        <div class="flex-1 min-w-0">
            <h2 class="font-black text-white text-xl leading-tight truncate">{{ auth()->user()->name }}</h2>
            <div class="mt-1 flex items-start gap-1.5 text-gray-300">
                <p class="text-[11px] font-medium leading-relaxed line-clamp-2">
                    
                    <template x-if="activeAddress">
                        <span>
                            <span x-text="activeAddress.full_address"></span><br>
                            <span x-text="activeAddress.district + ', ' + activeAddress.city"></span>
                        </span>
                    </template>

                    <template x-if="!activeAddress">
                        <span>
                            {{ auth()->user()->full_address ?? 'Alamat belum diatur' }}<br>
                            {{ auth()->user()->district ?? '-' }}, {{ auth()->user()->city ?? '-' }}
                        </span>
                    </template>
                    
                </p>
            </div>
        </div>
    </div>

    <div class="h-2 bg-gray-100 border-y border-gray-200/50"></div>
    <div class="bg-white">
        <div class="px-6 py-6 flex items-center justify-center">
            <div class="h-[1.5px] bg-gray-200 flex-1"></div>
            <span class="px-4 text-[12px] font-black text-gray-400 uppercase tracking-[0.2em]">Informasi Pribadi</span>
            <div class="h-[1.5px] bg-gray-200 flex-1"></div>
        </div>

        <div class="px-4 pb-10 space-y-1">
            <a href="{{ route('profile.change-name') }}" class="flex items-center gap-2 p-3 hover:bg-pink-100/60 transition-all group">
                <div class="w-10 h-10 flex items-center justify-center">
                    <img src="{{ asset('assets/images/iconProfile.png') }}" 
                        alt="Profile" 
                        class="w-8 h-8 object-contain">
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-gray-800 tracking-tight">Nama Profile</h3>
                    <p class="text-[10px] font-medium text-gray-400 mt-0.5">Ubah nama tampilan akun kamu</p>
                </div>
                <i data-lucide="chevron-right" class="w-5 h-5 text-gray-300 group-hover:text-[#e91e63] transition-colors"></i>
            </a>

            <a href="{{ route('settings.phone')}}" class="flex items-center gap-2 p-3 hover:bg-pink-100/60 transition-all group">
                <div class="w-10 h-10 flex items-center justify-center">
                    <img src="{{ asset('assets/images/iconTelepon.png') }}" 
                        alt="telepon" 
                        class="w-7 h-7 object-contain">
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-gray-800 tracking-tight">Nomor Telepon</h3>
                    <p class="text-[10px] font-medium text-gray-400 mt-0.5">Ganti atau verifikasi nomor baru kamu.</p>
                </div>
                <i data-lucide="chevron-right" class="w-5 h-5 text-gray-300 group-hover:text-[#e91e63] transition-colors"></i>
            </a>

            <a href="{{ route('profile.change-password') }}" class="flex items-center gap-2 p-3 hover:bg-pink-100/60 transition-all group">
                <div class="w-10 h-10 flex items-center justify-center">
                    <img src="{{ asset('assets/images/iconPassword.png') }}" 
                        alt="Password" 
                        class="w-8 h-8 object-contain">
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-gray-800 tracking-tight">Password Akun</h3>
                    <p class="text-[10px] font-medium text-gray-400 mt-0.5">Demi keamanan, ganti berkala</p>
                </div>
                <i data-lucide="chevron-right" class="w-5 h-5 text-gray-300 group-hover:text-[#e91e63] transition-colors"></i>
            </a>

            <button @click="showPhotoModal = true" class="w-full flex items-center gap-2 p-3 hover:bg-pink-100/60 transition-all group text-left">
                <div class="w-10 h-10 flex items-center justify-center">
                    <img src="{{ asset('assets/images/iconGallery.png') }}" alt="img" class="w-8 h-8 object-contain">
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-gray-800 tracking-tight">Foto Profil</h3>
                    <p class="text-[10px] font-medium text-gray-400 mt-0.5">Ganti foto profil terbaru</p>
                </div>
                <i data-lucide="chevron-right" class="w-5 h-5 text-gray-300 group-hover:text-[#e91e63] transition-colors"></i>
            </button>

            <button @click="showAddressModal = true" class="w-full flex items-center gap-2 p-3 hover:bg-pink-50/50 rounded-2xl transition-all group border border-gray-100 hover:border-pink-200 text-left">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center group-hover:text-white transition-all">
                    <img src="{{ asset('assets/images/iconMaps.png') }}" 
                        alt="Maps" 
                        class="w-8 h-8 object-contain">
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-black text-gray-800 group-hover:text-[#e91e63] tracking-tight transition-colors">Pengaturan Alamat</h3>
                    <p class="text-[10px] font-medium text-gray-400 mt-0.5">Setup alamat pengiriman kamu</p>
                </div>
                <i data-lucide="chevron-right" class="w-5 h-5 text-gray-200 group-hover:text-[#e91e63] transition-colors"></i>
            </button>
        </div>
    </div>

    <div x-show="showAddressModal" 
        class="fixed inset-0 z-[100] flex items-end justify-center bg-black/60 backdrop-blur-[2px]"
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
                <template x-if="addressTab === 'list'">
                    <div>
                        <div class="flex items-start justify-between mb-6 bg-white">
                            <div class="flex-1">
                                <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight">Pilih Alamat Utama</h2>
                                <p class="text-[12px] text-gray-400 font-medium">Klik alamat untuk menjadikannya utama.</p>
                            </div>
                            
                            <button @click="addressTab = 'add'" 
                                    type="button"
                                    x-init="lucide.createIcons()"
                                    class="ml-4 w-12 h-12 text-[#e91e63] rounded-3xl flex items-center justify-center hover:bg-[#e91e63] hover:text-white transition-all shadow-sm active:scale-90">
                                <i data-lucide="plus" class="w-6 h-6 icon-maps"></i>
                            </button>
                        </div>

                        <div class="flex flex-col gap-3">
                            <template x-for="item in addresses" :key="item.id">
                                <div class="relative transition-transform duration-150 ease-out transform" 
                                     :style="'order: ' + (item.is_default ? 0 : 1)">
                                     
                                    <button type="button" @click="setMainAddress(item.id)" 
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
                                            
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                                                <path d="m15 5 4 4"/>
                                            </svg>

                                        </div>
                                    </button>
                                </div>
                            </template>
                            
                            <template x-if="addresses.length === 0">
                                <div class="text-center py-10">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Belum ada alamat</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-if="addressTab === 'add'">
                    <div>
                        <button @click="addressTab = 'list'" class="flex items-center gap-2 text-gray-400 hover:text-gray-600 mb-4 transition-colors">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest">Kembali</span>
                        </button>
                        
                        <form action="{{ route('address.store') }}" method="POST" class="space-y-4">
                            @csrf
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

                <template x-if="addressTab === 'edit'">
                    <div>
                        <button @click="addressTab = 'list'" class="flex items-center gap-2 text-gray-400 hover:text-gray-600 mb-4 transition-colors">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest">Batal Edit</span>
                        </button>

                        <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight mb-6">Edit Alamat</h2>
                        
                        <form :action="'/update-address/' + currentAddress.id" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
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

    <div x-show="showPhotoModal" class="fixed inset-0 z-[110] flex items-end justify-center bg-black/60 backdrop-blur-[2px]" x-cloak>
        <div @click.away="showPhotoModal = false" class="bg-white w-full max-w-md p-8 shadow-2xl"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0">
            <div class="flex justify-center mb-6"><div class="w-12 h-1.5 bg-gray-200 rounded-full"></div></div>
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight">Ganti Foto Profil</h2>
            <p class="text-[12px] text-gray-400 font-medium">Ambil foto baru atau pilih dari galeri.</p>
            <div class="space-y-4">

                <label class="flex items-center gap-4 py-2 px-4 -mb-2 mt-6 bg-gray-50 rounded-2xl cursor-pointer active:scale-95 transition-transform">
                    <img src="{{ asset('assets/images/iconGallery.png') }}"
                    alt="img" class="w-8 h-8 object-contain">

                    <span class="font-bold text-gray-800">Ambil dari Galeri</span>
                    <input id="input-galeri" type="file" accept="image/*" class="hidden" @change="handleFileUpload">
                </label>

                <label class="flex items-center gap-4 py-2 px-4 bg-gray-50 rounded-2xl cursor-pointer active:scale-95 transition-transform">
                    <img src="{{ asset('assets/images/iconKamera.png') }}"
                    alt="img" class="w-8 h-8 object-contain">
                    <span class="font-bold text-gray-800">Gunakan Kamera</span>
                    <input id="input-kamera" type="file" accept="image/*" capture="environment" class="hidden" @change="handleFileUpload">
                </label>
            </div>
        </div>
    </div>

    <div x-show="showCropModal" class="fixed inset-0 z-[120] bg-black flex flex-col" x-cloak>
        <div class="flex items-center justify-between p-6 text-white">
            <button @click="showCropModal = false" class="p-2"><i data-lucide="x" class="w-6 h-6"></i></button>
            <h2 class="font-black uppercase tracking-widest text-sm">Sesuaikan Foto</h2>
            <button @click="saveCroppedImage()" class="p-2 text-green-400"><i data-lucide="check" class="w-7 h-7"></i></button>
        </div>
        <div class="flex-1 overflow-hidden flex items-center justify-center">
            <img :src="selectedImage" id="image-to-crop" class="max-w-full">
        </div>
        <div class="p-8 flex justify-center gap-10 text-white/50 bg-black/50">
            <button @click="cropper.rotate(-90)"><i data-lucide="rotate-ccw" class="w-6 h-6"></i></button>
            <button @click="cropper.setDragMode('move')"><i data-lucide="move" class="w-6 h-6"></i></button>
            <button @click="cropper.zoom(0.1)"><i data-lucide="zoom-in" class="w-6 h-6"></i></button>
            <button @click="cropper.rotate(90)"><i data-lucide="rotate-cw" class="w-6 h-6"></i></button>
        </div>
    </div>

    <script>
        lucide.createIcons();

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
</body>
</html>