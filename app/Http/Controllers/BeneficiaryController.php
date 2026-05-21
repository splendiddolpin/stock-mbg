<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    public function index()
    {
        $beneficiaries = Beneficiary::orderBy('school_name', 'asc')->get();
        return view('beneficiaries.index', compact('beneficiaries'));
    }

    public function create()
    {
        // Panggil semua data bahan baku dari database (urut abjad)
        $items = \App\Models\Item::orderBy('name', 'asc')->get();
        
        return view('beneficiaries.create', compact('items'));
    }

    public function createPosyandu()
    {
        return view('beneficiaries.create_posyandu');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        // 1. Hitung jumlah alergen dan buat teks detail otomatis
        $allergenCount = 0;
        $allergenDetails = null;

        // Jika admin mencentang kotak alergi di form
        if ($request->has('allergen_items') && count($request->allergen_items) > 0) {
            $allergenCount = count($request->allergen_items);
            
            // Ambil nama-nama bahan yang dipilih untuk mengisi kolom teks otomatis
            $namaBahan = \App\Models\Item::whereIn('id', $request->allergen_items)->pluck('name')->implode(', ');
            $allergenDetails = "Alergi bahan: " . $namaBahan;
        }

        // 2. Simpan data sekolah/posyandu (Sesuaikan dengan field validasimu)
        $beneficiary = \App\Models\Beneficiary::create([
            'school_name'       => $request->school_name,
            'type'              => $request->type,
            'porsi_besar'       => $request->porsi_besar ?? 0,
            'porsi_kecil'       => $request->porsi_kecil ?? 0,
            'total_balita'      => $request->total_balita ?? 0,
            'total_bumil_busui' => $request->total_bumil_busui ?? 0,
            
            // INI YANG BARU: Masukkan hasil hitungan alergi
            'allergen_count'    => $allergenCount,
            'allergen_details'  => $allergenDetails,
        ]);

        // 3. JURUS SAKTI: Simpan relasinya ke tabel jembatan (allergen_item)
        if ($request->has('allergen_items')) {
            $beneficiary->allergens()->sync($request->allergen_items);
        }

        return redirect()->route('beneficiaries.index')->with('success', 'Penerima berhasil ditambahkan beserta data alerginya!');
    }

    // Menampilkan form edit
    public function edit(Beneficiary $beneficiary)
    {
        return view('beneficiaries.edit', compact('beneficiary'));
    }

    // Menyimpan perubahan data
    public function update(Request $request, Beneficiary $beneficiary)
    {
        $request->validate([
            'school_name' => 'required|string',
            'porsi_besar' => 'required|integer|min:0',
            'porsi_kecil' => 'required|integer|min:0',
            'allergen_count' => 'required|integer|min:0',
            'allergen_details' => 'nullable|string',
        ]);

        $beneficiary->update($request->all());
        return redirect()->route('beneficiaries.index')->with('success', 'Data sekolah berhasil diperbarui!');
    }

    // Menghapus data
    public function destroy(Beneficiary $beneficiary)
    {
        $beneficiary->delete();
        return redirect()->route('beneficiaries.index')->with('success', 'Data sekolah berhasil dihapus!');
    }
}