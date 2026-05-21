<?php

namespace App\Http\Controllers;

use App\Models\UsageRecap;
use Illuminate\Http\Request;

class UsageRecapController extends Controller
{
    public function index()
    {
        // Mengambil data rekap terbaru beserta informasi barang dan menunya
        $recaps = UsageRecap::with(['item', 'menu'])
                            ->latest('date')
                            ->paginate(15); // Menggunakan paginasi agar tidak lambat jika data banyak

        return view('usage_recaps.index', compact('recaps'));
    }
}