<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\BlangkoAvailability;
use App\Models\DocumentRequest;
use App\Models\RegistHistorys;
use App\Models\TakeEktp;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;


class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        // --- Statistik Ringkas ---
        $documentRequest = DocumentRequest::count();
        $blangkoStock    = optional(BlangkoAvailability::orderByDesc('created_at')->first())->jumlah_total ?? 0;
        $userCount = User::where('role', 'user')->count();


        // --- Data Line Chart ---
        $monthlyData = [];
        foreach (range(1, 12) as $m) {
            $monthlyData[] = DocumentRequest::whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->count();
        }

        // --- Data Pie Chart ---
        $alasanList = [
            'Pembuatan KTP Baru',
            'Pembuatan KTP Rusak',
            'KTP Hilang',
            'Pembaruan data KTP'
        ];

        $pieData = [];
        foreach ($alasanList as $alasan) {
            $pieData[$alasan] = DocumentRequest::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->whereRaw('LOWER(alasan) = ?', [strtolower($alasan)]) // case insensitive
                ->count();
        }

        return view('admin.index', compact(
            'documentRequest',
            'blangkoStock',
            'userCount',
            'monthlyData',
            'pieData',
            'month',
            'year'
        ));
    }

    public function chartData(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $alasanList = ['Pembuatan KTP Baru', 'Pembuatan KTP Rusak', 'KTP Hilang', 'Pembaruan data KTP'];
        $pieData = [];

        foreach ($alasanList as $alasan) {
            $pieData[$alasan] = DocumentRequest::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->where('alasan', $alasan)
                ->count();
        }

        return response()->json([
            'month' => $month,
            'year'  => $year,
            'data'  => $pieData
        ]);
    }

    public function document(Request $request)
    {
        $query = DocumentRequest::with('user');

        // Filter berdasarkan tanggal tertentu
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        // Filter berdasarkan alasan (baru, rusak, hilang, pembaruan data)
        if ($request->filled('alasan')) {
            $query->where('alasan', $request->alasan);
        }

        // Filter berdasarkan bulan (ambil dari created_at)
        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan);
        }

        // Filter berdasarkan tahun (opsional, default tahun sekarang)
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        } else {
            $query->whereYear('created_at', now()->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🔎 Filter berdasarkan NIK
        if ($request->filled('nik')) {
            $query->where('nik', 'like', '%' . $request->nik . '%');
        }

        $requests = $query->latest()->get();

        return view('admin.document.index', [
            'requests' => $requests,
            'filters' => $request->only(['tanggal', 'alasan', 'bulan', 'tahun', 'status', 'nik'])
        ]);
    }

    public function blangko()
    {
        // Ambil stok terakhir (paling baru)
        $stokSaatIni = optional(BlangkoAvailability::orderByDesc('created_at')->first())->jumlah_total ?? 0;

        // Ambil seluruh riwayat update stok (untuk tabel/laporan)
        $riwayat = BlangkoAvailability::orderByDesc('tanggal')->orderByDesc('waktu')->get();

        return view('admin.blangko.index', [
            'stokSaatIni' => $stokSaatIni,
            'riwayat' => $riwayat,
        ]);
    }

    public function users()
    {
        $users = User::where('role', 'user')->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function takeEktp(Request $request)
    {
        // Query untuk DocumentRequest yang sudah tercetak
        $query = DocumentRequest::with(['user', 'take'])
            ->where('status', 'sudah tercetak');

        // 🔎 Filter NIK (pemohon ATAU pengambil, satu input)
        if ($request->filled('nik')) {
            $nik = $request->nik;
            $query->where(function ($q) use ($nik) {
                $q->where('nik', 'like', "%{$nik}%") // NIK Pemohon
                    ->orWhereHas('take', function ($sub) use ($nik) {
                        $sub->where('nik', 'like', "%{$nik}%"); // NIK Pengambil
                    });
            });
        }

        // Data eKTP hasil filter (atau semua kalau tidak ada filter)
        $ektp = $query->latest()->get();

        // Data pengambilan (tetap semua kalau tidak ada filter)
        $pengambilan = TakeEktp::with('documentRequest.user')
            ->when($request->filled('nik'), function ($q) use ($request) {
                $nik = $request->nik;
                $q->where('nik', 'like', "%{$nik}%")
                    ->orWhereHas('documentRequest', function ($sub) use ($nik) {
                        $sub->where('nik', 'like', "%{$nik}%");
                    });
            })
            ->latest()
            ->get();

        return view('admin.document.take_ektp.index', [
            'ektp' => $ektp,
            'pengambilan' => $pengambilan,
            'filters' => $request->only('nik')
        ]);
    }
}
