<?php

namespace App\Http\Controllers;

use App\Models\MenuRequest;
use App\Models\Beneficiary;
use App\Models\MenuCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuRequestController extends Controller
{
    // =========================================================================
    // 1. HALAMAN FORM PUBLIK (UNTUK SISWA - TANPA LOGIN)
    // =========================================================================
    
    public function createPublic()
    {
        $schools = Beneficiary::orderBy('school_name', 'asc')->get();
        
        // Ambil data dari tabel baru khusus katalog UI siswa
        $items = MenuCatalog::orderBy('category', 'asc')->orderBy('name', 'asc')->get();
        
        return view('menu-requests.public-form', compact('schools', 'items'));
    }

    public function storePublic(Request $request)
    {
        // A. CEK COOLDOWN SESSION (Anti-Spam Anak Iseng) dengan Timestamp Detik
        if (session()->has('last_request_time')) {
            $lastRequest = session('last_request_time');
            
            // Cek apakah data di session berupa angka (mencegah error dari data lama)
            if (is_numeric($lastRequest)) {
                // 300 detik = 5 menit. Jika selisihnya kurang dari 300 detik, tolak!
                if (now()->timestamp - $lastRequest < 300) {
                    return redirect()->back()->with('error', 'Sabar ya! Kamu baru saja mengirim menu. Beri kesempatan temanmu yang lain, coba lagi dalam 5 menit! ⏳');
                }
            }
        }

        // B. VALIDASI DATA
        $request->validate([
            'beneficiary_id' => 'required|exists:beneficiaries,id',
            'student_name'   => 'required|string|max:255',
            'menu_name'      => 'required|string',
            'reason'         => 'nullable|string',
        ]);

        // C. SIMPAN KE DATABASE (Pecah string menu_name menjadi array)
        $menuArray = explode(', ', $request->menu_name);

        foreach ($menuArray as $singleMenu) {
            if (!empty($singleMenu)) {
                MenuRequest::create([
                    'beneficiary_id' => $request->beneficiary_id,
                    'student_name'   => $request->student_name,
                    'menu_name'      => $singleMenu,
                    'reason'         => $request->reason,
                    'status'         => 'pending'
                ]);
            }
        }

        // D. SET WAKTU TERAKHIR MENGIRIM KE SESSION (Simpan sebagai angka Detik)
        session(['last_request_time' => now()->timestamp]);

        return redirect()->back()->with('success', 'Hore! Usulan kombinasi menumu sudah dikirim ke Ahli Gizi! 🚀🍱');
    }

    // =========================================================================
    // 2. HALAMAN BACKEND ADMIN (UNTUK AHLI GIZI - WAJIB LOGIN)
    // =========================================================================

    public function adminIndex()
    {
        // Query Agregasi: Menghitung total request per nama menu dan diurutkan dari yang paling banyak
        $rankedRequests = MenuRequest::select('menu_name', DB::raw('count(*) as total_request'))
            ->groupBy('menu_name')
            ->orderBy('total_request', 'desc')
            ->get();

        // Hitung total suara masuk
        $totalVotes = MenuRequest::count();

        // Ambil log detail request terbaru untuk history bawah
        $latestRequests = MenuRequest::with('beneficiary')->latest()->take(10)->get();

        return view('menu-requests.admin-index', compact('rankedRequests', 'latestRequests', 'totalVotes'));
    }
}