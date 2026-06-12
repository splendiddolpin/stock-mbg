<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ArchiveController extends Controller
{
    public function index()
    {
        // Jalur folder excel di Laravel 11 milikmu
        $dirPrivate = storage_path('app/private/excel_archives');
        $archiveFiles = [];

        // Jika foldernya ada, mari kita bongkar isinya
        if (is_dir($dirPrivate)) {
            $files = scandir($dirPrivate);

            foreach ($files as $file) {
                // Kita hanya mengambil file yang berakhiran .xls dan ada kata FROZEN
                if (str_ends_with($file, '.xls') && str_contains($file, 'FROZEN')) {
                    $fullPath = $dirPrivate . DIRECTORY_SEPARATOR . $file;
                    
                    // Ambil waktu kapan file tersebut dibuat/di-generate oleh robot
                    $createdAt = filemtime($fullPath); 

                    // Mempercantik nama file untuk tampilan UI (menghilangkan format kaku robot)
                    // Contoh: "Arsip_Matang_MBG_periode_16_FROZEN.xls" jadi "Periode 16"
                    $cleanName = str_replace(['Arsip_Matang_MBG_', '_FROZEN', '.xls', '_'], [' ', '', '', ' '], $file);
                    $cleanName = trim(ucwords($cleanName));

                    $archiveFiles[] = [
                        'filename' => $file, // ini untuk link download
                        'display_name' => $cleanName, // ini untuk nama di tabel
                        'size' => round(filesize($fullPath) / 1024, 2) . ' KB', // Ukuran file
                        'date' => Carbon::createFromTimestamp($createdAt)->translatedFormat('d F Y H:i') // Tanggal pembuatan
                    ];
                }
            }

            // Urutkan file berdasarkan tanggal terbaru di paling atas
            usort($archiveFiles, function ($a, $b) {
                return strtotime($b['date']) <=> strtotime($a['date']);
            });
        }

        return view('archives.index', compact('archiveFiles'));
    }

    public function download($filename)
    {
        // Pastikan nama file aman (mencegah user nakal memanipulasi URL)
        $filename = basename($filename);
        $filePath = storage_path('app/private/excel_archives/' . $filename);

        if (file_exists($filePath)) {
            // Ubah nama file saat didownload agar rapi, misal: "Arsip_Final_Periode_16.xls"
            $downloadName = str_replace('Arsip_Matang_MBG_', 'Arsip_Final_', $filename);
            return response()->download($filePath, $downloadName);
        }

        return redirect()->back()->with('error', 'Maaf, file fisik tidak ditemukan di server.');
    }
}