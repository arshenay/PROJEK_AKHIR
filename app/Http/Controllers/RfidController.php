<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccessLogin;
use App\Models\RfidUser;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RfidController extends Controller
{


    public function store(Request $request)
{
    // Kita buat fleksibel: bisa menerima 'rfid_uid' atau 'uid' (dari Postman/ESP32)
    $uid = $request->rfid_uid ?? $request->uid;

    if (!$uid) {
        return response()->json(['status' => 'Error', 'message' => 'UID tidak terbaca!'], 400);
    }

    // Cek apakah UID ini ada di tabel rfid_users dan statusnya aktif
    $user = RfidUser::where('rfid_uid', $uid)
        ->where('is_active', true)
        ->first();

    if ($user) {
        $status = 'Success';
        $message = "Selamat Datang, " . $user->name;
        $openDoor = true;
    } else {
        $status = 'Denied';
        $message = "Akses Ditolak!";
        $openDoor = false;
    }

    // Simpan log ke database
    AccessLogin::create([
        'rfid_uid' => $uid,
        'status' => $status
    ]);

    // Kembalikan respon ke ESP32 atau Postman
    return response()->json([
        'status' => $status, 
        'message' => $message,
        'open_door' => $openDoor // PENTING: Ini yang dibaca ESP32 untuk gerakin relay
    ]);
}

    // Tampilkan halaman Manajemen User

    public function index()
    {
        $logs = AccessLogin::latest()->get();
        return view('dashboard', compact('logs'));
    }


    public function userIndex()
    {
        $users = RfidUser::latest()->get();
        return view('user-management', compact('users'));
    }
    // Simpan User Baru
    public function userStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'rfid_uid' => 'required|unique:rfid_users,rfid_uid'
        ]);

        RfidUser::create($request->all());
        return back()->with('success', 'User berhasil ditambahkan!');
    }

    // Update Status Aktif/Nonaktif (Real-time Toggle)
    public function userToggle(string $id)
    {
        $user = RfidUser::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();
        return back();
    }

    // Hapus User
    public function userDelete(string $id)
    {
        RfidUser::findOrFail($id)->delete();
        return back();
    }

    public function standby()
    {
        // Kita ambil log terakhir cuma buat pemicu di JavaScript nanti
        $lastLog = AccessLogin::latest()->first();
        return view('standby', compact('lastLog'));
    }

    public function uploadWebcam(Request $request)
{
    // 1. Ambil data ID log dan string gambar (Base64) dari JavaScript
    $logId = $request->id;
    $img = $request->image; // Ini isinya data:image/jpeg;base64,xxxx

    if (!$img) {
        return response()->json(['success' => false, 'message' => 'Tidak ada gambar yang dikirim'], 400);
    }

    // 2. Bersihkan string Base64 agar bisa diubah jadi file gambar asli
    $image_parts = explode(";base64,", $img);
    $image_base64 = base64_decode($image_parts[1]);

    // 3. Buat nama file unik untuk foto pelakunya
    $fileName = 'pelaku_' . $logId . '_' . Str::random(5) . '.jpeg';

    // 4. Simpan gambar ke folder internal Laravel (storage/app/public/pelaku/)
    // Agar bisa diakses publik, pastikan nanti ketik "php artisan storage:link" di terminal
    Storage::disk('public')->put('pelaku/' . $fileName, $image_base64);

    // 5. Update data AccessLogin terakhir, masukkan nama file foto ke databasenya
    $log = AccessLogin::find($logId);
    if ($log) {
        // Pastikan di tabel access_log kamu sudah ada kolom 'photo' ya.
        // Jika belum ada, sementara baris di bawah ini bisa kamu komentari dulu pakai //
        $log->update(['image' => $fileName]); 
    }

    return response()->json(['success' => true, 'message' => 'Foto pelaku berhasil disimpan!']);
}

}