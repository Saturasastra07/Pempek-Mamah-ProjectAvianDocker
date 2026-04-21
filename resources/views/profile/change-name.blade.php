<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Name - Pempek Mamah Dhani</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 flex justify-center min-h-screen">
    <div class="w-full max-w-md bg-white min-h-screen shadow-lg">
        <div class="p-4 flex items-center mb-2">
            <a href="{{ route('profile') }}" class="p-2 hover:bg-gray-100 rounded-full">
                <i data-lucide="arrow-left" class="w-6 h-6 text-black"></i>
            </a>
        </div>

        <div class="px-6 pb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Ubah nama</h1>

            <form action="{{ route('profile.review-name') }}" method="POST">
                @csrf
                <div class="border-2 border-[#e91e63] border-opacity-70 rounded-xl p-4 mb-6 shadow-sm">
                    <label class="block text-sm font-medium text-[#e91e63] mb-1">Nama Anda</label>
                    <input type="text" name="new_name" value="{{ old('new_name', auth()->user()->name) }}" 
                           class="w-full text-lg font-semibold outline-none text-gray-800 bg-transparent" autofocus>
                </div>

                <div class="bg-gray-50 rounded-md p-5 mb-8 border border-gray-100">
                    <p class="text-[15px] font-bold text-gray-900 mb-0">Mohon diperhatikan:</p>
                    <p class="text-[13.5px] text-gray-600 leading-relaxed">
                        Jika Anda mengubah nama tampilan pada layanan <span class="font-bold text-gray-600">Pempek Mamah Dhani</span>,
                        Anda tidak akan dapat mengubahnya kembali selama <span class="font-bold text-gray-600">30 hari</span> kedepan demi keamanan akun Anda.
                        <a href="#" class="text-blue-600 font-medium">Lihat Lebih</a>
                    </p>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('profile') }}" class="flex-1 bg-black bg-opacity-70 hover:bg-opacity-30 text-white py-3 rounded-xl font-bold text-center transition-all active:scale-95">
                        Kembali
                    </a>
                    <button type="submit" class="flex-[1.5] bg-[#e91e63] hover:bg-opacity-70 text-white py-3 rounded-xl font-bold transition-all active:scale-95">
                        Tinjau Perubahan
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