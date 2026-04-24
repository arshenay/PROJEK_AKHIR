<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFID Security - Monitoring</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
</head>
<body class="bg-slate-900 text-slate-100 font-sans p-0">
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

    <div class="container mx-auto px-4 py-10">
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-bold text-blue-400">Security Dashboard</h1>
                <p class="text-slate-400 text-sm">Real-time monitoring sistem keamanan RFID</p>
            </div>
            <div id="status-indicator" class="px-4 py-2 rounded-full bg-green-500/20 text-green-400 text-xs font-bold border border-green-500/50 animate-pulse">
                SYSTEM ACTIVE
            </div>
        </header>

        <div class="bg-slate-800 rounded-xl shadow-2xl overflow-hidden border border-slate-700">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-700/50">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-300">WAKTU</th>
                        <th class="p-4 text-sm font-semibold text-slate-300">RFID UID</th>
                        <th class="p-4 text-sm font-semibold text-slate-300">STATUS</th>
                        <th class="p-4 text-sm font-semibold text-slate-300 text-center">VERIFIKASI WAJAH</th>
                    </tr>
                </thead>
                <tbody id="log-table-body">
                    @foreach($logs as $log)
                    <tr class="border-t border-slate-700 hover:bg-slate-700/30 transition-all">
                        <td class="p-4 text-sm">{{ $log->created_at->format('H:i:s | d-m-Y') }}</td>
                        <td class="p-4 font-mono text-blue-300">{{ $log->rfid_uid }}</td>
                        <td class="p-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $log->status == 'Success' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/50' : 'bg-rose-500/20 text-rose-400 border border-rose-500/50' }}">
                                {{ strtoupper($log->status) }}
                            </span>
                        </td>
                        <td class="p-4 flex justify-center">
                            @if($log->image)
                                <img src="{{ asset('storage/'.$log->image) }}" class="w-20 h-20 object-cover rounded-lg border border-slate-600 shadow-lg">
                            @else
                                <div class="w-20 h-20 bg-slate-700 rounded-lg flex items-center justify-center text-xs text-slate-500 italic">No Foto</div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="modal-camera" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-slate-800 w-full max-w-md rounded-2xl p-6 border border-rose-500/50 shadow-[0_0_50px_-12px_rgba(244,63,94,0.5)]">
            <h2 class="text-2xl font-bold text-rose-500 mb-2 text-center uppercase tracking-widest">Akses Ditolak!</h2>
            <p class="text-slate-400 text-sm text-center mb-6">Harap menghadap kamera untuk verifikasi wajah</p>
            
            <div id="my_camera" class="rounded-xl overflow-hidden mx-auto shadow-2xl border-4 border-slate-700"></div>
            
            <div id="countdown" class="text-6xl font-black text-center mt-6 text-white drop-shadow-md"></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let lastLogId = {{ $logs->first()->id ?? 0 }};

        // Konfigurasi Kamera
        Webcam.set({
            width: 400,
            height: 300,
            image_format: 'jpeg',
            jpeg_quality: 90
        });

        // Polling data baru
        setInterval(() => {
            $.get('/api/check-new-log', function(data) {
                if (data && data.id > lastLogId) {
                    lastLogId = data.id;
                    if (data.status === 'Denied') {
                        triggerCamera(data.id);
                    } else {
                        location.reload(); 
                    }
                }
            });
        }, 3000);

        function triggerCamera(logId) {
            const modal = document.getElementById('modal-camera');
            modal.classList.remove('hidden'); // Show modal
            Webcam.attach('#my_camera');

            let timeLeft = 3;
            const timerElement = document.getElementById('countdown');
            
            const timer = setInterval(() => {
                timerElement.innerText = timeLeft;
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    takePicture(logId);
                }
                timeLeft -= 1;
            }, 1000);
        }

        function takePicture(logId) {
            Webcam.snap(function(data_uri) {
                fetch('/upload-webcam', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ id: logId, image: data_uri })
                }).then(() => {
                    location.reload();
                });
            });
        }
    </script>
</body>
</html>