@php
    // 1. FORMAT TANGGAL AMAN
    $startStr = isset($period->start_date) ? \Carbon\Carbon::parse($period->start_date)->format('Y-m-d') : null;
    $endStr = isset($period->end_date) ? \Carbon\Carbon::parse($period->end_date)->format('Y-m-d') : null;

    // 2. AMBIL DATA TARGET HARIAN (Gunakan blok try-catch agar jika tabel kosong tidak crash)
    $beneficiaries = \App\Models\Beneficiary::all();
    
    try {
        $dataTargetHarian = \App\Models\DailyTarget::where('period_id', $period->id)->get();
        $groupedTargets = $dataTargetHarian->isNotEmpty() ? $dataTargetHarian->groupBy(function($item) {
            return \Carbon\Carbon::parse($item->date)->format('Y-m-d');
        }) : collect();
    } catch (\Exception $e) {
        $dataTargetHarian = collect();
        $groupedTargets = collect();
    }

    // 3. GENERATE KALENDER 14 HARI OTOMATIS
    $semuaTanggal = [];
    if ($startStr && $endStr) {
        $d = \Carbon\Carbon::parse($period->start_date);
        $dEnd = \Carbon\Carbon::parse($period->end_date);
        for($date = $d->copy(); $date->lte($dEnd); $date->addDay()) {
            $semuaTanggal[] = $date->format('Y-m-d');
        }
    }

    // 4. AMBIL BARANG MASUK & KELUAR (AMBIL DATA BERDASARKAN ID PERIODE DULU, JIKA KOSONG BARU PAKAI TANGGAL)
    $gabunganKeluar = collect();
    $barangMasuk = collect();

    if (isset($period->id)) {
        // Ambil Masuk & Keluar Manual Berdasarkan period_id
        $manualOut = \App\Models\Transaction::with('item')->where('type', 'out')->where('period_id', $period->id)->get();
        $barangMasuk = \App\Models\Transaction::with('item')->where('type', 'in')->where('period_id', $period->id)->orderBy('date')->get();
        
        // Ambil Keluar Dapur Berdasarkan Rentang Tanggal
        $autoOut = \App\Models\UsageRecap::with(['item', 'menu'])->whereDate('date', '>=', $startStr)->whereDate('date', '<=', $endStr)->get();

        // Jika DB ternyata sudah disapu bersih (0 hasil), coba fallback gunakan data historis rentang tanggal (jika ada backup)
        if ($barangMasuk->isEmpty() && $manualOut->isEmpty()) {
            $manualOut = \App\Models\Transaction::with('item')->where('type', 'out')->whereDate('date', '>=', $startStr)->whereDate('date', '<=', $endStr)->get();
            $barangMasuk = \App\Models\Transaction::with('item')->where('type', 'in')->whereDate('date', '>=', $startStr)->whereDate('date', '<=', $endStr)->orderBy('date')->get();
        }

        // Mapping data Keluar Dapur
        foreach($autoOut as $a) {
            $gabunganKeluar->push((object)[
                'date' => $a->date,
                'item_name' => $a->item->name ?? 'Bahan Dihapus',
                'keterangan' => 'Menu: ' . ($a->menu->name ?? '-'),
                'qty' => $a->quantity_out,
                'unit' => $a->unit,
                'hpp' => $a->total_cost
            ]);
        }

        // Mapping data Keluar Manual/Darurat
        foreach($manualOut as $m) {
            $gabunganKeluar->push((object)[
                'date' => $m->date,
                'item_name' => $m->item->name ?? 'Bahan Dihapus',
                'keterangan' => 'Manual (Darurat): ' . $m->description,
                'qty' => $m->quantity,
                'unit' => $m->item->unit ?? '-',
                'hpp' => $m->quantity * ($m->item->hpp ?? 0)
            ]);
        }
        $gabunganKeluar = $gabunganKeluar->sortBy('date');
    }
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <table>
        <tr>
            <td colspan="8" style="text-align: center; font-weight: bold; font-size: 16px;">LAPORAN REKAPITULASI KOMPREHENSIF MBG</td>
        </tr>
        <tr>
            <td colspan="8" style="text-align: center; font-weight: bold; color: blue; font-size: 14px;">
                PERIODE: {{ strtoupper($period->name ?? 'ARSIP PERIODE SISTEM') }}
            </td>
        </tr>
        <tr>
            <td colspan="8" style="text-align: center;">
                Tanggal Pelaksanaan: {{ $startStr ? \Carbon\Carbon::parse($startStr)->translatedFormat('d F Y') : '-' }} s/d {{ $endStr ? \Carbon\Carbon::parse($endStr)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
        <tr><td colspan="8"></td></tr>

        <tr>
            <td colspan="8" style="background-color: #4f46e5; color: white; font-weight: bold;">1. REKAP TARGET PORSI HARIAN PENERIMA MANFAAT</td>
        </tr>
        <tr>
            <td style="font-weight: bold; border: 1px solid black; background-color:#e0e7ff;">Tanggal</td>
            <td style="font-weight: bold; border: 1px solid black; background-color:#e0e7ff;">Nama Sekolah/Posyandu</td>
            <td style="font-weight: bold; border: 1px solid black; background-color:#e0e7ff;">Tipe</td>
            <td style="font-weight: bold; border: 1px solid black; background-color:#e0e7ff;">Porsi Besar</td>
            <td style="font-weight: bold; border: 1px solid black; background-color:#e0e7ff;">Porsi Kecil</td>
            <td style="font-weight: bold; border: 1px solid black; background-color:#e0e7ff;">Total Balita</td>
            <td style="font-weight: bold; border: 1px solid black; background-color:#e0e7ff;">Total Bumil/Busui</td>
            <td style="font-weight: bold; border: 1px solid black; background-color:#e0e7ff;">Status Hari</td>
        </tr>
        @foreach($semuaTanggal as $tgl)
            @php
                $isSunday = \Carbon\Carbon::parse($tgl)->isSunday();
                $targetsForDate = $groupedTargets->has($tgl) ? $groupedTargets->get($tgl)->keyBy('beneficiary_id') : collect();
            @endphp
            @foreach($beneficiaries as $ben)
                @php
                    $target = $targetsForDate->get($ben->id);
                    $isHoliday = $target ? $target->is_holiday : $isSunday; 
                    
                    $pBesar = $target ? $target->porsi_besar : $ben->porsi_besar;
                    $pKecil = $target ? $target->porsi_kecil : $ben->porsi_kecil;
                    $tBalita = $target ? $target->total_balita : $ben->total_balita;
                    $tBumil = $target ? $target->total_bumil_busui : $ben->total_bumil_busui;

                    if($isHoliday) {
                        $pBesar = 0; $pKecil = 0; $tBalita = 0; $tBumil = 0;
                    }
                @endphp
                <tr>
                    <td style="border: 1px solid black;">{{ \Carbon\Carbon::parse($tgl)->translatedFormat('d M Y') }}</td>
                    <td style="border: 1px solid black;">{{ $ben->school_name }}</td>
                    <td style="border: 1px solid black; text-transform: capitalize;">{{ $ben->type }}</td>
                    <td style="border: 1px solid black;">{{ $pBesar }}</td>
                    <td style="border: 1px solid black;">{{ $pKecil }}</td>
                    <td style="border: 1px solid black;">{{ $tBalita }}</td>
                    <td style="border: 1px solid black;">{{ $tBumil }}</td>
                    <td style="border: 1px solid black; font-weight:bold; color:{{ $isHoliday ? 'red' : 'green' }}">{{ $isHoliday ? 'LIBUR' : 'AKTIF' }}</td>
                </tr>
            @endforeach
        @endforeach
        
        <tr><td colspan="8"></td></tr>

        <tr>
            <td colspan="8" style="background-color: #10b981; color: white; font-weight: bold;">2. REKAP LOGISTIK: BARANG MASUK KE GUDANG</td>
        </tr>
        <tr>
            <td style="font-weight: bold; border: 1px solid black; background-color:#d1fae5;">Tanggal</td>
            <td style="font-weight: bold; border: 1px solid black; background-color:#d1fae5;">Nama Bahan Baku</td>
            <td style="font-weight: bold; border: 1px solid black; background-color:#d1fae5;">Masuk</td>
            <td colspan="5" style="font-weight: bold; border: 1px solid black; background-color:#d1fae5;">Keterangan / Supplier</td>
        </tr>
        @forelse($barangMasuk as $in)
            <tr>
                <td style="border: 1px solid black;">{{ \Carbon\Carbon::parse($in->date)->translatedFormat('d M Y') }}</td>
                <td style="border: 1px solid black;">{{ $in->item->name ?? '-' }}</td>
                <td style="border: 1px solid black;">{{ floatval($in->quantity) }} {{ $in->item->unit ?? '-' }}</td>
                <td colspan="5" style="border: 1px solid black;">{{ $in->description }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align: center; border: 1px solid black; color: gray; font-style: italic;">Tidak ada riwayat transaksi barang masuk pada rentang tanggal periode ini.</td>
            </tr>
        @endforelse

        <tr><td colspan="8"></td></tr>

        <tr>
            <td colspan="8" style="background-color: #f97316; color: white; font-weight: bold;">3. REKAP LOGISTIK: BAHAN KELUAR / PEMAKAIAN DAPUR & MANUAL</td>
        </tr>
        <tr>
            <td style="font-weight: bold; border: 1px solid black; background-color:#ffedd5;">Tanggal</td>
            <td style="font-weight: bold; border: 1px solid black; background-color:#ffedd5;">Nama Bahan Baku</td>
            <td style="font-weight: bold; border: 1px solid black; background-color:#ffedd5;">Menu / Tujuan</td>
            <td style="font-weight: bold; border: 1px solid black; background-color:#ffedd5;">Keluar</td>
            <td colspan="4" style="font-weight: bold; border: 1px solid black; background-color:#ffedd5;">Nilai HPP (Rp)</td>
        </tr>
        @forelse($gabunganKeluar as $out)
            <tr>
                <td style="border: 1px solid black;">{{ \Carbon\Carbon::parse($out->date)->translatedFormat('d M Y') }}</td>
                <td style="border: 1px solid black;">{{ $out->item_name }}</td>
                <td style="border: 1px solid black;">{{ $out->keterangan }}</td>
                <td style="border: 1px solid black;">{{ floatval($out->qty) }} {{ $out->unit }}</td>
                <td colspan="4" style="border: 1px solid black;">Rp {{ number_format($out->hpp, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align: center; border: 1px solid black; color: gray; font-style: italic;">Tidak ada riwayat rekap barang keluar pada rentang tanggal periode ini.</td>
            </tr>
        @endforelse

    </table>
</body>
</html>