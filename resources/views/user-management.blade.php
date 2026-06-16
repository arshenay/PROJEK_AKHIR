<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Kelola User RFID </title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#9AD872] text-[#5A4545] p-8 font-sans">

    <nav class="bg-[#468432] border-2 border-[#4f8932] p-4 mb-10 rounded-2xl shadow-sm max-w-5xl mx-auto">
        <div class="flex justify-between items-center px-4">
            <div class="text-[#FFEF91] font-serif font-black text-2xl tracking-wide">
             RFID-SECURE <span class="text-[#CBB3B3] font-light text-sm">v1.0</span>
            </div>
            <div class="flex gap-8">
                <a href="/dashboard" class="flex items-center gap-2 font-serif font-medium text-[#FFEF91] hover:text-[#ffffff] transition">
                    <span>🖥️</span> Monitoring
                </a>
                <a href="/user-management" class="flex items-center gap-2 font-serif font-bold text-[#FFEF91] border-b-2 border-[#FFEF91]">
                    <span>👥</span> Kelola User
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto">
        
        @if(session('success'))
        <div class="bg-[#E2F4E9] text-[#2E6B47] border border-[#A3D9B7] p-4 rounded-2xl mb-6 font-semibold font-serif">
            ✨ {{ session('success') }} ✨
        </div>
        @endif

        <div class="flex justify-between items-center mb-8 px-2">
            <div>
                <h1 class="text-3xl font-serif font-black text-[#1F6F5F]">User Management 👥</h1>
                <a href="/dashboard" class="text-[#ffffff] hover:text-[#ffffff] text-sm italic transition">← Kembali ke Dashboard</a>
            </div>
            <button onclick="openModal()" class="bg-[#268223] hover:bg-[#239f0e] text-white px-5 py-3 rounded-2xl font-serif font-bold transition shadow-sm">
             Tambah User
            </button>
        </div>

        <div class="bg-white rounded-2xl border-2 border-[#9AD872] overflow-hidden shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead class="bg-[#468432] text-[#FFEF91] font-serif font-bold text-sm">
                    <tr>
                        <th class="p-4">NAMA</th>
                        <th class="p-4">RFID UID</th>
                        <th class="p-4">STATUS</th>
                        <th class="p-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="border-t border-[#FFF0F0] hover:bg-[#FFF5F5] transition-all">
                        <td class="p-4 font-semibold text-[#5A4545]">🌸 {{ $user->name }}</td>
                        <td class="p-4 font-mono text-[#A06A6A] font-bold">{{ $user->rfid_uid }}</td>
                        <td class="p-4">
                            <a href="{{ route('users.toggle', $user->id) }}" class="px-3 py-1 rounded-full text-xs font-bold transition-all {{ $user->is_active ? 'bg-[#E2F4E9] text-[#2E6B47] border border-[#A3D9B7]' : 'bg-[#FCE8E8] text-[#A34343] border border-[#EAAFAF]' }}">
                                {{ $user->is_active ? 'AKTIF' : 'NONAKTIF' }}
                            </a>
                        </td>
                        <td class="p-4 flex justify-center">
                            <form action="{{ route('users.delete', $user->id) }}" method="POST" onsubmit="return confirm('Hapus akses manis untuk {{ $user->name }}?')">
                                @csrf @method('DELETE')
                                <button class="text-[#A34343] hover:text-red-600 text-xs font-serif font-bold bg-[#FCE8E8] px-3 py-1 rounded-xl transition border border-[#EAAFAF]">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-[#CBB3B3] italic">Belum ada user terdaftar disini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="modal-user" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
        <div class="bg-white w-full max-w-md rounded-4xl p-8 border-4 border-[#FFD1D1] shadow-2xl text-[#5A4545]">
            <h2 class="text-2xl font-serif font-bold mb-1 text-[#8C6262]">Tambah Pemilik Akses</h2>
            <p class="text-xs text-[#BA9A9A] mb-6 italic">Silakan isi data nama, atau tap kartu di sensor...</p>
            
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm text-[#8C6262] mb-2 font-serif font-bold">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full bg-[#FFF9F9] border-2 border-[#FFE1E1] rounded-xl p-3 text-[#5A4545] focus:outline-none focus:border-[#FCA5A5] transition" placeholder="Contoh: Budi Santoso" required>
                </div>
                <div class="mb-6">
                    <label class="block text-sm text-[#8C6262] mb-2 font-serif font-bold">
                        RFID UID 
                        <span id="scan-status" class="text-xs text-blue-400 ml-2 animate-pulse">(🔄 Mendengarkan sensor...)</span>
                    </label>
                    <input type="text" id="input-rfid" name="rfid_uid" class="w-full bg-[#FFF9F9] border-2 border-[#FFE1E1] rounded-xl p-3 text-[#2E6B47] font-mono focus:outline-none text-md font-bold" placeholder="Menunggu kartu manis terdeteksi..." readonly required>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeModal()" class="flex-1 bg-[#F3E8E8] hover:bg-[#EAE0E0] text-[#8C6262] p-3 rounded-xl font-serif font-bold transition">Batal</button>
                    <button type="submit" class="flex-1 bg-[#FCA5A5] p-3 rounded-xl font-serif font-bold text-white hover:bg-[#F87171] transition">Simpan 🌸</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let lastLogId = 0;
        let pollingInterval = null;

        function openModal() {
            document.getElementById('modal-user').classList.remove('hidden');
            document.getElementById('modal-user').classList.add('flex');
            $.get('/api/check-new-log', function(data) {
                if (data) lastLogId = data.id;
                startPolling();
            });
        }

        /* Fungsi closeModal dan startPolling persis sama dengan sebelumnya */
        function closeModal() {
            document.getElementById('modal-user').classList.add('hidden');
            document.getElementById('modal-user').classList.remove('flex');
            document.getElementById('input-rfid').value = "";
            clearInterval(pollingInterval);
        }

        function startPolling() {
            pollingInterval = setInterval(() => {
                $.get('/api/check-new-log', function(data) {
                    if (data && data.id > lastLogId) {
                        lastLogId = data.id;
                        document.getElementById('input-rfid').value = data.rfid_uid;
                        const statusIndicator = document.getElementById('scan-status');
                        statusIndicator.innerText = "(✅ Kartu RFID Terdeteksi!)";
                        statusIndicator.className = "text-xs text-emerald-500 ml-2 font-bold font-serif";
                    }
                });
            }, 1500);
        }
    </script>
</body>
</html>