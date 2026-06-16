<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎀 Standby - Soft RFID Security 🎀</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
</head>
<body class="bg-[#a7e77c] text-[#634848] overflow-hidden font-sans">

    <div class="flex flex-col items-center justify-center h-screen space-y-6">
        <div class="relative">
            <div class="w-56 h-56 border-4 border-[#FFEF91]/60 rounded-full flex items-center justify-center animate-[ping_3s_linear_infinite]"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-7xl drop-shadow-sm animate-pulse">☘️</span>
            </div>
        </div>

        <div class="text-center">
            <h1 class="text-3xl font-serif font-bold tracking-wide text-[#34af19] uppercase">Silahkan Scan Kartu sayang</h1>
            <p class="text-[#000000] mt-2 font-medium italic text-md"> Tempelkan kartu RFID pada sensor</p>
        </div>

        <div id="status-msg" class="hidden px-8 py-4 rounded-3xl text-xl font-serif font-bold uppercase tracking-wider border-2 shadow-sm"></div>
    </div>

    <div id="modal-camera" class="hidden fixed inset-0 bg-[#a7e77c]/90 backdrop-blur-md z-50 flex-col items-center justify-center p-6 text-center">
        <span class="text-5xl mb-2 animate-bounce">☘️</span>
        <h2 class="text-4xl font-serif font-black text-[#34af19] mb-2 uppercase tracking-wide">Akses Ditolak!</h2>
        <p class="text-md text-[#1F6F5F] mb-6 font-medium">Verifikasi wajah dulu yuk, mohon menghadap kamera ya... 🤍</p>
        
        <div id="my_camera" class="rounded-4xl overflow-hidden mx-auto shadow-xl border-8 border-[#0db12e] bg-[#a7e77c] w-[320px] h-60"></div>
        
        <div id="countdown" class="text-7xl font-serif font-black mt-6 text-[#8C6262]">3</div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let lastLogId = 0; 
    let isProcessing = false;

    Webcam.set({ width: 320, height: 240, image_format: 'jpeg', jpeg_quality: 90 });

    $.get('/api/check-new-log', function(data) {
        if (data) lastLogId = data.id;
    });

    setInterval(() => {
        if (isProcessing) return;
        $.get('/api/check-new-log', function(data) {
            if (data && data.id > lastLogId) {
                lastLogId = data.id;
                handleAccess(data);
            }
        });
    }, 2000);

    function handleAccess(data) {
        const statusBox = document.getElementById('status-msg');
        statusBox.classList.remove('hidden');

        if (data.status === 'Success') {
            statusBox.innerHTML = " Akses Diberikan ";
            statusBox.className = "px-8 py-4 rounded-3xl text-xl font-serif font-bold uppercase bg-[#E2F4E9] text-[#2E6B47] border-[#A3D9B7]";
            setTimeout(() => { statusBox.classList.add('hidden'); }, 3000);
        } else {
            statusBox.innerHTML = " Akses Ditolak ";
            statusBox.className = "px-8 py-4 rounded-3xl text-xl font-serif font-bold uppercase bg-[#a7e77c] text-[#A34343] border-[#EAAFAF]";
            triggerCamera(data.id);
        }
    }

function triggerCamera(logId) {
        isProcessing = true;
        const modal = document.getElementById('modal-camera');
        const countdownEl = document.getElementById('countdown');
        const titleEl = modal.querySelector('h2');
        const descEl = modal.querySelector('p');
        
        // 1. Tampilan awal pas nunggu izin/kamera loading (Biar ga langsung ngitung)
        countdownEl.innerText = "⏳"; 
        titleEl.innerText = "Menyiapkan Kamera... ";
        descEl.innerText = "Mohon klik 'Allow/Izinkan' pada browser untuk mengaktifkan kamera Logitech ya... ";
        
        modal.classList.remove('hidden');
        
        // Perintahkan browser menyalakan webcam
        Webcam.attach('#my_camera'); 

        // ====================================================================
        // SAKRAL: Kunci Utama! Timer HANYA jalan kalau kamera sudah BENAR-BENAR LIVE
        // ====================================================================
        Webcam.on('live', function() {
            // Ketika gambar kamera Logitech sudah muncul, baru set teks dan mulai hitung mundur!
            titleEl.innerText = "Akses Ditolak, Sayang!";
            descEl.innerText = "Verifikasi wajah dulu yuk, mohon menghadap kamera ya... ";
            
            let timeLeft = 3;
            countdownEl.innerText = timeLeft;

            const timer = setInterval(() => {
                timeLeft -= 1;
                countdownEl.innerText = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(timer);
                    
                    Webcam.snap(function(data_uri) {
                        // Matikan listener 'live' dan 'error' agar bersih untuk scan berikutnya
                        Webcam.off('live');
                        Webcam.off('error');

                        modal.classList.remove('bg-[#a7e77c]/90');
                        modal.classList.add('bg-[#a7e77c]/90');

                        // Ubah status sukses menjepret
                        countdownEl.innerText = "📸";
                        titleEl.innerText = "Wajah Terekam!";
                        titleEl.classList.replace('text-[#a7e77c]', 'text-[#a7e77c]'); // Merah jadi Hijau
                        descEl.innerText = "Data kamu sudah diambil, tunggu verifikasi dari admin ya...";

                        // Kirim gambar ke Laravel
                        fetch('/upload-webcam', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ id: logId, image: data_uri })
                        })
                        .then(() => {
                            Webcam.reset(); // Matikan lampu webcam Logitech
                            
                            // Beri jeda 3 detik agar pesan sempat dibaca pelaku sebelum modal menutup
                            setTimeout(() => {
                                modal.classList.add('hidden');
                                document.getElementById('status-msg').classList.add('hidden');

                                modal.classList.remove('bg-[#a7e77c]/90');
                                modal.classList.add('bg-[#a7e77c]/90');
                                
                                // Kembalikan setelan teks ke default
                                titleEl.innerText = "Akses Ditolak, Sayang!";
                                titleEl.classList.replace('text-[#a7e77c]', 'text-[#a7e77c]');
                                descEl.innerText = "Verifikasi wajah dulu yuk, mohon menghadap kamera ya... 🤍";
                                
                                isProcessing = false;
                            }, 3000);
                        })
                        .catch(err => {
                            console.error(err);
                            Webcam.reset();
                            modal.classList.add('hidden');
                            isProcessing = false;
                        });
                    });
                }
            }, 1000);
        });

        // Jaga-jaga kalau kamera Logitech dicabut atau user klik 'Block/Tolak Izin'
        Webcam.on('error', function(err) {
            console.error("Webcam Error:", err);
            countdownEl.innerText = "❌";
            titleEl.innerText = "Kamera Tidak Terdeteksi 🤍";
            descEl.innerText = "Gagal mengakses kamera Logitech. Pastikan kabel USB sudah tertancap dengan manis ya!";
            
            setTimeout(() => {
                Webcam.reset();
                Webcam.off('live');
                Webcam.off('error');
                modal.classList.add('hidden');
                document.getElementById('status-msg').classList.add('hidden');
                isProcessing = false;
            }, 4000);
        });
    }
</script>
</body>
</html>