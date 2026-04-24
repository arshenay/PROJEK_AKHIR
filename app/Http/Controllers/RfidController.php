<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccessLogin;
use App\Models\RfidUser;

class RfidController extends Controller
{


    public function store(Request $request)
    {
        $uid = $request->rfid_uid;

        // Cek apakah UID ini ada di tabel rfid_users dan statusnya aktif
        $user = RfidUser::where('rfid_uid', $uid)
            ->where('is_active', true)
            ->first();

        if ($user) {
            $status = 'Success';
            $message = "Selamat Datang, " . $user->name;
        } else {
            $status = 'Denied';
            $message = "Akses Ditolak!";
        }

        AccessLogin::create([
            'rfid_uid' => $uid,
            'status' => $status
        ]);

        return response()->json(['status' => $status, 'message' => $message]);
    }
    

    // Tampilkan halaman Manajemen User
    
    public function index() {
        $logs = AccessLogin::latest()->get();
        return view('dashboard', compact('logs'));
        }
    
        
    public function userIndex() {
        $users = RfidUser::latest()->get();
        return view('user-management', compact('users'));
        }
// Simpan User Baru
public function userStore(Request $request) {
    $request->validate([
        'name' => 'required',
        'rfid_uid' => 'required|unique:rfid_users,rfid_uid'
    ]);

    RfidUser::create($request->all());
    return back()->with('success', 'User berhasil ditambahkan!');
}

// Update Status Aktif/Nonaktif (Real-time Toggle)
public function userToggle($id) {
    $user = \App\Models\RfidUser::findOrFail($id);
    $user->is_active = !$user->is_active;
    $user->save();
    return back();
}

// Hapus User
public function userDelete($id) {
    \App\Models\RfidUser::findOrFail($id)->delete();
    return back();
}
}

