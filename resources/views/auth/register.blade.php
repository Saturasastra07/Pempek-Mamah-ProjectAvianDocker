<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Pempek Mamah Dhani</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-white min-h-screen flex flex-col justify-center p-6">
    <div class="max-w-md mx-auto w-full">
        <div class="mb-8">
            <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tighter">Daftar Akun</h1>
            <p class="text-gray-400 text-sm italic">Lengkapi data pengirimanmu ya, Kak!</p>
        </div>

        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[10px] font-black text-pink-600 uppercase ml-1">Nama Lengkap</label>
                <input type="text" name="name" class="w-full p-3.5 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-pink-300" placeholder="Contoh: Satura Sastra" required>
            </div>

            <div>
                <label class="text-[10px] font-black text-pink-600 uppercase ml-1">Nomor WhatsApp</label>
                <input type="tel" name="phone" class="w-full p-3.5 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-pink-300" placeholder="0812xxxx" required>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-[10px] font-black text-pink-600 uppercase ml-1">Provinsi</label>
                    <select name="province" class="w-full p-3.5 bg-gray-50 border-none rounded-2xl text-[11px] font-bold focus:ring-2 focus:ring-pink-300">
                        <option value="Jawa Tengah">Jawa Tengah</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black text-pink-600 uppercase ml-1">Kota/Kabupaten</label>
                    <select name="city" class="w-full p-3.5 bg-gray-50 border-none rounded-2xl text-[11px] font-bold focus:ring-2 focus:ring-pink-300">
                        <option value="Semarang">Semarang</option>
                        <option value="Kendal">Kendal</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-[10px] font-black text-pink-600 uppercase ml-1">Kecamatan</label>
                <input type="text" name="district" class="w-full p-3.5 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-pink-300" placeholder="Contoh: Boja" required>
            </div>

            <div>
                <label class="text-[10px] font-black text-pink-600 uppercase ml-1">Alamat Lengkap (Jalan/No.Rumah)</label>
                <textarea name="full_address" rows="2" class="w-full p-3.5 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-pink-300" placeholder="Contoh: Jl. Mawar No. 12, RT 01/02"></textarea>
            </div>

            <div>
                <label class="text-[10px] font-black text-pink-600 uppercase ml-1">Buat Password</label>
                <input type="password" name="password" class="w-full p-3.5 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-pink-300" required>
            </div>

            <button type="submit" class="w-full bg-[#e91e63] text-white py-4 rounded-2xl font-black shadow-lg shadow-pink-100 active:scale-95 transition-all mt-4 uppercase text-sm tracking-widest">
                Daftar Sekarang
            </button>
        </form>

        <p class="text-center mt-8 text-xs text-gray-400 font-medium">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-pink-600 font-black uppercase">Masuk di sini</a>
        </p>
    </div>
</body>
</html>