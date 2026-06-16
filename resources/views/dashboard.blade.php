<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>☘️ Security Dashboard ☘️</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#9AD872] text-[#5A4545] p-8 font-sans"> 
    <div class="max-w-6xl mx-auto"> 
        
        <nav class="bg-[#468432] border-2 border-[#4f8932] p-4 mb-10 rounded-2xl shadow-sm">
            <div class="flex justify-between items-center px-4">
                <div class="text-[#FFEF91] font-serif font-black text-2xl tracking-wide">
                    RFID-SECURE <span class="text-[#CBB3B3] font-light text-sm">v1.0</span>
                </div>
                <div class="flex gap-8">
                    <a href="/dashboard" class="flex items-center gap-2 font-serif font-bold text-[#FFEF91] border-b-2 border-[#FFA02E]">
                        <span>🖥️</span> Monitoring
                    </a>
                    <a href="/user-management" class="flex items-center gap-2 font-serif font-medium text-[#FFEF91] hover:text-[#ffffff] transition">
                        <span>👥</span> Kelola User
                    </a>
                </div>
            </div>
        </nav>

        <header class="flex justify-between items-center mb-10 px-2">
            <div>
                <h1 class="text-3xl font-serif font-black text-[#1F6F5F] tracking-wide">Security Dashboard </h1>
                <p class="text-[#ffffff] text-sm mt-1 italic">Real-time monitoring sistem keamanan</p>
            </div>
            <div id="status-indicator" class="px-4 py-2 rounded-full bg-[#E2F4E9] text-[#7b6c16] text-xs font-bold border border-[#A3D9B7] animate-pulse">
                ☘️ SYSTEM ACTIVE
            </div>
        </header>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border-2 border-[#9AD872]">
            <table class="w-full text-left border-collapse">
                <thead class="bg-[#468432] text-[#FFEF91] font-serif">
                    <tr>
                        <th class="p-4 text-sm font-bold">WAKTU</th>
                        <th class="p-4 text-sm font-bold">RFID UID</th>
                        <th class="p-4 text-sm font-bold">STATUS</th>
                        <th class="p-4 text-sm font-bold text-center">VERIFIKASI WAJAH</th>
                    </tr>
                </thead>
                <tbody id="log-table-body">
                    @forelse($logs as $log)
                    <tr class="border-t border-[#FFF0F0] hover:bg-[#FFF5F5] transition-all">
                        <td class="p-4 text-sm text-[#7D6464]">{{ $log->created_at->format('H:i:s | d-m-Y') }}</td>
                        <td class="p-4 font-mono text-[#A06A6A] font-bold">{{ $log->rfid_uid }}</td>
                        <td class="p-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $log->status == 'Success' ? 'bg-[#E2F4E9] text-[#FFEF91] border border-[#A3D9B7]' : 'bg-[#ff63637e] text-[#89791b] border border-[#EAAFAF]' }}">
                                {{ $log->status == '✅ Success' ? ' SUCCESS' : '❌ DENIED' }}
                            </span>
                        </td>
                        <td class="p-4 flex justify-center">
                            @if($log->image)
                                <img src="{{ asset('storage/pelaku/'.$log->image) }}" onclick="openImageModal('{{ asset('storage/pelaku/'.$log->image) }}')" class="w-20 h-20 object-cover rounded-2xl border-2 border-[#1c8b00] shadow-sm cursor-pointer hover:scale-110 transition-transform duration-300">
                            @else
                                <div class="w-20 h-20 bg-[#FFF5F5] rounded-2xl flex items-center justify-center text-xs text-[#CBB3B3] italic border border-dashed ✅">No Foto 🤍</div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr id="empty-row">
                        <td colspan="4" class="p-8 text-center text-[#CBB3B3] italic">Belum ada data scan masuk... ⏳</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="image-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 cursor-pointer transition-opacity" onclick="closeImageModal()">
        <div class="relative bg-[#FFF9F9] p-5 rounded-4-xl border-4 border-[#FFD1D1] shadow-2xl max-w-sm w-full cursor-default" onclick="event.stopPropagation()">
            <button onclick="closeImageModal()" class="absolute -top-4 -right-4 bg-[#FCE8E8] text-[#A34343] w-12 h-12 rounded-full font-black text-2xl border-4 border-[#FFF9F9] shadow-lg hover:bg-[#F87171] hover:text-white transition-all transform hover:scale-110 flex items-center justify-center pb-1">×</button>
            
            <img id="modal-image-src" src="" class="w-full h-auto rounded-2xl object-cover border-2 border-[#FFE1E1]">
            <p class="text-center text-[#8C6262] font-serif font-bold mt-4 text-lg">📸 Tersangka Tertangkap Kamera</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let lastLogId = {{ $logs->first()->id ?? 0 }};

        // Fungsi membuka modal gambar
        function openImageModal(imgUrl) {
            document.getElementById('modal-image-src').src = imgUrl;
            document.getElementById('image-modal').classList.remove('hidden');
        }

        // Fungsi menutup modal gambar
        function closeImageModal() {
            document.getElementById('image-modal').classList.add('hidden');
        }

        function formatTanggal() {
            const d = new Date();
            const pad = (n) => n < 10 ? '0' + n : n;
            return `${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())} | ${pad(d.getDate())}-${pad(d.getMonth()+1)}-${d.getFullYear()}`;
        }

        setInterval(() => {
            $.get('/api/check-new-log', function(data) {
                if (data && data.id > lastLogId) {
                    lastLogId = data.id;
                    $('#empty-row').remove();

                    const isSuccess = data.status === 'Success';
                    const badgeClass = isSuccess 
                        ? 'bg-[#E2F4E9] text-[#2E6B47] border border-[#A3D9B7]' 
                        : 'bg-[#FCE8E8] text-[#A34343] border border-[#EAAFAF]';
                    const textStatus = isSuccess ? '🌸 SUCCESS' : '🎀 DENIED';

                    let fotoHtml = '<div class="w-20 h-20 bg-[#FFF5F5] rounded-2xl flex items-center justify-center text-xs text-[#CBB3B3] italic border border-dashed border-[#FFD1D1]">No Foto 🤍</div>';
                    
                    const newRow = `
                        <tr class="border-t border-[#FFF0F0] bg-[#FFF0F0]/40 hover:bg-[#FFF5F5] transition-all id-log-${data.id}">
                            <td class="p-4 text-sm text-[#7D6464]">${formatTanggal()}</td>
                            <td class="p-4 font-mono text-[#A06A6A] font-bold">${data.rfid_uid}</td>
                            <td class="p-4"><span class="px-3 py-1 rounded-full text-xs font-bold ${badgeClass}">${textStatus}</span></td>
                            <td class="p-4 flex justify-center img-container">${fotoHtml}</td>
                        </tr>
                    `;

                    $('#log-table-body').prepend(newRow);

                    setTimeout(() => { $(`.id-log-${data.id}`).removeClass('bg-[#FFF0F0]/40'); }, 3000);
                }

                // Cek berkala apakah foto pelaku sudah terupload ke database
                if (data && data.status === 'Denied' && data.image) {
                    const cellFoto = $(`.id-log-${data.id} .img-container`);
                    // Perbarui menjadi gambar yang BISA DIKLIK untuk membesarkan
                    if (cellFoto.find('div').length > 0) {
                        cellFoto.html(`<img src="/storage/pelaku/${data.image}" onclick="openImageModal('/storage/pelaku/${data.image}')" class="w-20 h-20 object-cover rounded-2xl border-2 border-[#FFD1D1] shadow-sm cursor-pointer hover:scale-110 transition-transform duration-300">`);
                    }
                }
            });
        }, 2000);
    </script>
</body>
</html>