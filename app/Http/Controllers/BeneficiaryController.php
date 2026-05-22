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
        $items = \App\Models\Item::orderBy('name', 'asc')->get();
        return view('beneficiaries.create_posyandu', compact('items'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        // 1. Validasi data (Satpam pengecek)
        $request->validate([
            'school_name' => 'required|string|max:255',
            'type'        => 'required|in:sekolah,posyandu',
        ]);

        // 2. Siapkan wadah untuk hitungan alergi
        $totalAlergiKeseluruhan = 0;
        $allergenDetails = null;
        $syncData = [];

        if ($request->has('allergen_items') && count($request->allergen_items) > 0) {
            $detailsArray = [];
            foreach ($request->allergen_items as $itemId) {
                $jmlAnak = $request->allergen_counts[$itemId] ?? 1;
                $syncData[$itemId] = ['anak_count' => $jmlAnak];
                $totalAlergiKeseluruhan += $jmlAnak;

                $itemName = \App\Models\Item::find($itemId)->name;
                $detailsArray[] = "{$itemName} ({$jmlAnak} orang)";
            }
            $allergenDetails = "Alergen: " . implode(', ', $detailsArray);
        }

        // 3. Simpan data sekolah/posyandu ke tabel Master
        $beneficiary = \App\Models\Beneficiary::create([
            'school_name'       => $request->school_name,
            'type'              => $request->type,
            'porsi_besar'       => $request->porsi_besar ?? 0,
            'porsi_kecil'       => $request->porsi_kecil ?? 0,
            'total_balita'      => $request->total_balita ?? 0,
            'total_bumil_busui' => $request->total_bumil_busui ?? 0,
            'allergen_count'    => $totalAlergiKeseluruhan,
            'allergen_details'  => $allergenDetails,
        ]);

        // 4. Masukkan ke tabel pivot alergi
        if (!empty($syncData)) {
            $beneficiary->allergens()->sync($syncData);
        }

        // =========================================================================
        // INI AMUNISI BARU: OTOMATIS SUNTIKKAN KE PERIODE AKTIF DI KALENDER
        // =========================================================================
        $activePeriod = \App\Models\Period::where('is_active', true)->latest()->first();

        if ($activePeriod) {
            $startDate = \Carbon\Carbon::parse($activePeriod->start_date);
            $endDate   = \Carbon\Carbon::parse($activePeriod->end_date);

            // Jalankan mesin waktu khusus untuk PM baru ini saja dari start sampai end_date periode aktif
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                
                $isSunday = $date->isSunday();
                $isHoliday = false;

                // Salin porsi default
                $pBesar  = $beneficiary->porsi_besar;
                $pKecil  = $beneficiary->porsi_kecil;
                $tBalita = $beneficiary->total_balita;
                $tBumil  = $beneficiary->total_bumil_busui;

                if ($isSunday) {
                    $pBesar = $pKecil = $tBalita = $tBumil = 0;
                    $isHoliday = true;
                } else {
                    // Cek aturan Sabtu Libur Sekolah
                    if ($date->isSaturday() && $beneficiary->type === 'sekolah') {
                        $pBesar = $pKecil = 0;
                        $isHoliday = true;
                    }

                    // Cek aturan Rapelan Posyandu
                    if ($pm->type === 'posyandu') {
                        if ($date->isMonday() || $date->isThursday()) {
                            // BIARKAN NORMAL (TIDAK DIKALI 3)
                            // Karena yang dibanyakin itu isi menu/resepnya, bukan jumlah orangnya!
                            // $tBalita dan $tBumil tetap sesuai master data
                        } else {
                            // Selasa, Rabu, Jumat, Sabtu -> Posyandu TIDAK ADA PENGIRIMAN (0)
                            $tBalita = 0;
                            $tBumil  = 0;
                        }
                    }
                }

                // Masukkan rekaman harian PM baru ini ke database target harian
                \App\Models\DailyTarget::create([
                    'period_id'         => $activePeriod->id,
                    'date'              => $date->toDateString(),
                    'beneficiary_id'    => $beneficiary->id,
                    'porsi_besar'       => $pBesar,
                    'porsi_kecil'       => $pKecil,
                    'total_balita'      => $tBalita,
                    'total_bumil_busui' => $tBumil,
                    'is_holiday'        => $isHoliday,
                ]);
            }
        }
        // =========================================================================

        return redirect()->route('beneficiaries.index')->with('success', 'Penerima baru berhasil ditambahkan dan otomatis disinkronisasikan ke kalender periode aktif!');
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