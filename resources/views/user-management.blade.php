<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - RFID Security</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-100 p-8">
    <nav class="bg-slate-800 border-b border-slate-700 p-4 mb-6">
    <div class="container mx-auto flex justify-between items-center">
        <div class="text-blue-400 font-bold text-xl uppercase tracking-wider">RFID-Secure v1.0</div>
        <div class="flex gap-6">
            <a href="/dashboard" class="text-slate-300 hover:text-blue-400 font-medium transition {{ request()->is('dashboard') ? 'text-blue-400 border-b-2 border-blue-400' : '' }}">
                🖥️ Monitoring
            </a>
            <a href="/user-management" class="text-slate-300 hover:text-blue-400 font-medium transition {{ request()->is('user-management') ? 'text-blue-400 border-b-2 border-blue-400' : '' }}">
                👥 Kelola User
            </a>
        </div>
    </div>
</nav>

    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-blue-400">User Management</h1>
                <a href="/dashboard" class="text-slate-400 hover:text-white text-sm">← Kembali ke Dashboard</a>
            </div>
            <button onclick="document.getElementById('modal-user').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2 rounded-lg font-semibold transition">
                + Tambah User
            </button>
        </div>

        <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-700/50 text-slate-300 text-sm">
                    <tr>
                        <th class="p-4">NAMA</th>
                        <th class="p-4">RFID UID</th>
                        <th class="p-4">STATUS</th>
                        <th class="p-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr class="border-t border-slate-700">
                        <td class="p-4 font-semibold">{{ $user->name }}</td>
                        <td class="p-4 font-mono text-blue-300">{{ $user->rfid_uid }}</td>
                        <td class="p-4">
                            <a href="{{ route('users.toggle', $user->id) }}" class="px-3 py-1 rounded-full text-xs font-bold {{ $user->is_active ? 'bg-green-500/20 text-green-400 border border-green-500' : 'bg-red-500/20 text-red-400 border border-red-500' }}">
                                {{ $user->is_active ? 'AKTIF' : 'NONAKTIF' }}
                            </a>
                        </td>
                        <td class="p-4 flex justify-center gap-2">
                            <form action="{{ route('users.delete', $user->id) }}" method="POST" onsubmit="return confirm('Yakin hapus user ini?')">
                                @csrf @method('DELETE')
                                <button class="text-rose-500 hover:text-rose-400 text-sm font-bold">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="modal-user" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-slate-800 w-full max-w-md rounded-2xl p-8 border border-slate-700 shadow-2xl">
            <h2 class="text-2xl font-bold mb-6">Tambah Pemilik Akses</h2>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm text-slate-400 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-white focus:outline-none focus:border-blue-500" placeholder="Contoh: Budi Santoso" required>
                </div>
                <div class="mb-6">
                    <label class="block text-sm text-slate-400 mb-2">RFID UID</label>
                    <input type="text" name="rfid_uid" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-white font-mono focus:outline-none focus:border-blue-500" placeholder="Tempel kartu ke alat..." required>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('modal-user').classList.add('hidden')" class="flex-1 bg-slate-700 p-3 rounded-lg font-bold">Batal</button>
                    <button type="submit" class="flex-1 bg-blue-600 p-3 rounded-lg font-bold hover:bg-blue-500 transition">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>