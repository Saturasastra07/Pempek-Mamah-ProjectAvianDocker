<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Pempek Mamah Dhani</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-white min-h-screen flex flex-col justify-center p-6">
    <div class="max-w-md mx-auto w-full">
        <div class="mb-10 text-center">
            <div class="w-20 h-20 bg-pink-50 rounded-3xl flex items-center justify-center mx-auto mb-4">
                <span class="text-3xl">🥣</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tighter leading-none">Selamat Datang</h1>
            <p class="text-gray-400 text-sm mt-2 font-medium">Masuk pakai nomor WhatsApp kamu ya!</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 text-red-500 text-[10px] font-bold p-3 rounded-xl mb-4 border border-red-100 text-center uppercase">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[10px] font-black text-pink-600 uppercase ml-1 tracking-widest">Nomor WhatsApp</label>
                <input type="tel" name="phone" class="w-full p-4 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-pink-300" placeholder="0812xxxx" required>
            </div>

            <div>
                <label class="text-[10px] font-black text-pink-600 uppercase ml-1 tracking-widest">Password</label>
                <input type="password" name="password" class="w-full p-4 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-pink-300" placeholder="••••••••" required>
            </div>

            <button type="submit" class="w-full bg-[#e91e63] text-white py-4 rounded-2xl font-black shadow-xl shadow-pink-100 active:scale-95 transition-all mt-4 uppercase text-sm tracking-widest">
                Masuk Sekarang
            </button>
        </form>

        <p class="text-center mt-10 text-xs text-gray-400 font-medium">
            Belum jajan sebelumnya? <a href="{{ route('register') }}" class="text-pink-600 font-black uppercase">Daftar Akun Baru</a>
        </p>
    </div>
</body>
</html>