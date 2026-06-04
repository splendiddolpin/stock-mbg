<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

@php
    $groupedTargets = $dataTargetHarian->groupBy('date');
    $colspanMax = 8; // Total Lebar Tabel adalah 8 Kolom
@endphp

<table style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    
    <tr>
        <td colspan="{{ $colspanMax }}" style="text-align: center; font-size: 20px; font-weight: bold; color: #1e293b; border: none; padding-top: 15px;">
            LAPORAN REKAPITULASI KOMPREHENSIF MBG
        </td>
    </tr>
    <tr>
        <td colspan="{{ $colspanMax }}" style="text-align: center; font-size: 16px; font-weight: bold; color: #4338ca; border: none;">
            PERIODE: {{ strtoupper($period->name) }}
        </td>
    </tr>
    <tr>
        <td colspan="{{ $colspanMax }}" style="text-align: center; font-size: 12px; color: #64748b; border: none; border-bottom: 2px solid #000000; padding-bottom: 15px;">
            Tanggal Pelaksanaan: {{ \Carbon\Carbon::parse($period->start_date)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($period->end_date)->translatedFormat('d F Y') }}
        </td>
    </tr>
    
    <tr><td colspan="{{ $colspanMax }}" style="border: none;"></td></tr>

    <tr>
        <td colspan="{{ $colspanMax }}" style="background-color: #4f46e5; color: #ffffff; font-size: 14px; font-weight: bold; text-align: left; padding: 10px; border: 1px solid #000000;">
            1. REKAP TARGET PORSI HARIAN PENERIMA MANFAAT
        </td>
    </tr>
    <tr style="background-color: #e0e7ff; font-weight: bold; text-align: center; vertical-align: middle;">
        <td width="120" style="padding: 10px; border: 1px solid #000000;">Tanggal</td>
        <td width="300" style="padding: 10px; border: 1px solid #000000;">Nama Sekolah/Posyandu</td>
        <td width="100" style="padding: 10px; border: 1px solid #000000;">Tipe</td>
        <td width="100" style="padding: 10px; border: 1px solid #000000;">Porsi Besar</td>
        <td width="100" style="padding: 10px; border: 1px solid #000000;">Porsi Kecil</td>
        <td width="100" style="padding: 10px; border: 1px solid #000000;">Total Balita</td>
        <td width="120" style="padding: 10px; border: 1px solid #000000;">Total Bumil/Busui</td>
        <td width="120" style="padding: 10px; border: 1px solid #000000;">Status Hari</td>
    </tr>
    @foreach($groupedTargets as $date => $rows)
        @foreach($rows as $index => $row)
            <tr>
                @if($index === 0)
                    <td rowspan="{{ count($rows) }}" style="padding: 5px; text-align: center; vertical-align: top; font-weight: bold; background-color: #f8fafc; border: 1px solid #000000;">
                        {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                    </td>
                @endif
                <td style="padding: 5px; border: 1px solid #000000;">{{ $row->beneficiary->school_name ?? '-' }}</td>
                <td style="padding: 5px; text-align: center; border: 1px solid #000000;">{{ ucfirst($row->beneficiary->type ?? '-') }}</td>
                <td style="padding: 5px; text-align: center; border: 1px solid #000000;">{{ $row->porsi_besar }}</td>
                <td style="padding: 5px; text-align: center; border: 1px solid #000000;">{{ $row->porsi_kecil }}</td>
                <td style="padding: 5px; text-align: center; font-weight: {{ $row->total_balita > 0 ? 'bold' : 'normal' }}; border: 1px solid #000000;">{{ $row->total_balita }}</td>
                <td style="padding: 5px; text-align: center; font-weight: {{ $row->total_bumil_busui > 0 ? 'bold' : 'normal' }}; border: 1px solid #000000;">{{ $row->total_bumil_busui }}</td>
                <td style="padding: 5px; font-weight: bold; text-align: center; border: 1px solid #000000; {{ $row->is_holiday ? 'color: #dc2626;' : 'color: #16a34a;' }}">
                    {{ $row->is_holiday ? 'LIBUR' : 'AKTIF' }}
                </td>
            </tr>
        @endforeach
    @endforeach

    <tr><td colspan="{{ $colspanMax }}" style="border: none;"></td></tr>
    <tr><td colspan="{{ $colspanMax }}" style="border: none;"></td></tr>

    <tr>
        <td colspan="{{ $colspanMax }}" style="background-color: #059669; color: #ffffff; font-size: 14px; font-weight: bold; text-align: left; padding: 10px; border: 1px solid #000000;">
            2. REKAP LOGISTIK: BARANG MASUK KE GUDANG
        </td>
    </tr>
    <tr style="background-color: #d1fae5; font-weight: bold; text-align: center; vertical-align: middle;">
        <td style="padding: 10px; border: 1px solid #000000;">Tanggal</td>
        <td colspan="3" style="padding: 10px; border: 1px solid #000000;">Nama Bahan Baku</td>
        <td style="padding: 10px; border: 1px solid #000000;">Masuk</td>
        <td colspan="3" style="padding: 10px; border: 1px solid #000000;">Keterangan / Supplier</td>
    </tr>
    @forelse($dataBarangMasuk as $in)
        <tr>
            <td style="padding: 5px; text-align: center; border: 1px solid #000000;">{{ \Carbon\Carbon::parse($in->date)->format('d/m/Y') }}</td>
            <td colspan="3" style="padding: 5px; font-weight: bold; border: 1px solid #000000;">{{ $in->item->name ?? 'Bahan Dihapus' }}</td>
            <td style="padding: 5px; text-align: center; color: #059669; font-weight: bold; border: 1px solid #000000;">+{{ $in->quantity }} {{ $in->item->unit ?? '' }}</td>
            <td colspan="3" style="padding: 5px; border: 1px solid #000000;">{{ $in->description ?? '-' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="{{ $colspanMax }}" style="text-align: center; padding: 10px; color: #94a3b8; font-style: italic; border: 1px solid #000000;">Tidak ada riwayat transaksi barang masuk pada periode ini.</td>
        </tr>
    @endforelse

    <tr><td colspan="{{ $colspanMax }}" style="border: none;"></td></tr>
    <tr><td colspan="{{ $colspanMax }}" style="border: none;"></td></tr>

    <tr>
        <td colspan="{{ $colspanMax }}" style="background-color: #ea580c; color: #ffffff; font-size: 14px; font-weight: bold; text-align: left; padding: 10px; border: 1px solid #000000;">
            3. REKAP LOGISTIK: BAHAN KELUAR / PEMAKAIAN DAPUR
        </td>
    </tr>
    <tr style="background-color: #ffedd5; font-weight: bold; text-align: center; vertical-align: middle;">
        <td style="padding: 10px; border: 1px solid #000000;">Tanggal</td>
        <td colspan="2" style="padding: 10px; border: 1px solid #000000;">Nama Bahan Baku</td>
        <td colspan="2" style="padding: 10px; border: 1px solid #000000;">Menu Terkait</td>
        <td style="padding: 10px; border: 1px solid #000000;">Keluar</td>
        <td colspan="2" style="padding: 10px; border: 1px solid #000000;">Nilai HPP (Rp)</td>
    </tr>
    @forelse($dataBarangKeluar as $out)
        <tr>
            <td style="padding: 5px; text-align: center; border: 1px solid #000000;">{{ \Carbon\Carbon::parse($out->date)->format('d/m/Y') }}</td>
            <td colspan="2" style="padding: 5px; font-weight: bold; border: 1px solid #000000;">{{ $out->item->name ?? 'Bahan Dihapus' }}</td>
            <td colspan="2" style="padding: 5px; border: 1px solid #000000;">{{ $out->menu->name ?? '-' }}</td>
            <td style="padding: 5px; text-align: center; color: #ea580c; font-weight: bold; border: 1px solid #000000;">-{{ floatval($out->quantity_out) }} {{ $out->unit }}</td>
            <td colspan="2" style="padding: 5px; text-align: right; font-weight: bold; border: 1px solid #000000;">Rp {{ number_format($out->total_cost, 0, ',', '.') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="{{ $colspanMax }}" style="text-align: center; padding: 10px; color: #94a3b8; font-style: italic; border: 1px solid #000000;">Tidak ada riwayat rekap barang keluar pada periode ini.</td>
        </tr>
    @endforelse

</table>