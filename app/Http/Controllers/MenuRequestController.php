<?php

namespace App\Http\Controllers;

use App\Models\MenuRequest;
use App\Models\Beneficiary;
use Illuminate\Http\Request;

class MenuRequestController extends Controller
{
    public function index()
    {
        // Ambil semua request dari yang terbaru
        $requests = MenuRequest::with('beneficiary')->latest()->get();
        // Ambil daftar sekolah untuk form input
        $beneficiaries = Beneficiary::orderBy('school_name', 'asc')->get();
        
        return view('menu-requests.index', compact('requests', 'beneficiaries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'beneficiary_id' => 'required|exists:beneficiaries,id',
            'menu_name' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        MenuRequest::create($request->all());

        return redirect()->back()->with('success', 'Request menu dari siswa berhasil dikirim!');
    }

    public function updateStatus(Request $request, MenuRequest $menuRequest)
    {
        $request->validate(['status' => 'required|in:pending,diterima,ditolak']);
        $menuRequest->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status request menu berhasil diupdate!');
    }

    public function destroy(MenuRequest $menuRequest)
    {
        $menuRequest->delete();
        return redirect()->back()->with('success', 'Request menu berhasil dihapus.');
    }
}