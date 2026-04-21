<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Name - Pempek Mamah Dhani</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 flex justify-center min-h-screen">
    <div class="w-full max-w-md bg-white min-h-screen shadow-lg">
        <div class="p-4 flex items-center mb-2">
            <a href="{{ route('profile.change-name') }}" class="p-2 hover:bg-gray-100 rounded-full">
                <i data-lucide="arrow-left" class="w-6 h-6 text-black"></i>
            </a>
        </div>

        <div class="px-6 pb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Pratinjau nama baru Anda</h1>

            <div class="space-y-4 mb-8">
                <div>
                    <p class="text-sm text-gray-700 font-medium">Nama saat ini:</p>
                    <p class="text-xl font-semibold text-[#e91e63]">{{ auth()->user()->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-700 font-medium">Nama baru:</p>
                    <p class="text-xl font-semibold text-[#e91e63]">{{ $newName }}</p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-md p-5 mb-8 border border-gray-100">
                <p class="text-[15px] font-bold text-gray-900 mb-0">Mohon diperhatikan:</p>
                <p class="text-[13.5px] text-gray-600 leading-relaxed">
                    Proses peninjauan kami dapat memakan waktu hingga <span class="font-bold text-gray-600">3 hari</span>. 
                    Jika disetujui, Anda tidak dapat mengubahnya lagi selama <span class="font-bold text-gray-600">30 hari</span> kedepan.
                </p>
            </div>

            <form action="{{ route('profile.update-name') }}" method="POST">
                @csrf
                <input type="hidden" name="new_name" value="{{ $newName }}">

                <p class="text-sm text-gray-700 mb-3">Untuk menyimpan perubahan, masukkan password Anda</p>
                
                <div class="border-2 border-[#e91e63] border-opacity-70 rounded-xl p-4 mb-8">
                    <label class="block text-xs font-medium text-[#e91e63] mb-1">Masukkan password</label>
                    <input type="password" name="password" required placeholder="••••••••"
                           class="w-full text-lg font-bold outline-none text-gray-900 bg-transparent">
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('profile.change-name') }}" class="flex-1 bg-black bg-opacity-70 hover:bg-opacity-30 text-white py-3.5 rounded-xl font-bold text-center transition-all active:scale-95">
                        Kembali
                    </a>
                    <button type="submit" class="flex-[1.5] bg-[#e91e63] hover:bg-opacity-70 text-white py-3.5 rounded-xl font-bold transition-all active:scale-95">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>