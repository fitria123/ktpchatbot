<?php

namespace App\Http\Controllers\Panel;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\BlangkoAvailability;
use App\Http\Controllers\Controller;

class BlangkoStockController extends Controller
{
    public function create()
    {
        return view('admin.blangko.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'no_bast' => 'required|string|max:100',
            'jumlah_blanko' => 'required|integer|min:1',
            'jumlah_total' => 'integer|min:1',
        ]);

        $data = $request->only(['tanggal', 'no_bast', 'jumlah_blanko']);
        $data['id'] = Str::uuid();
        $data['waktu'] = Carbon::now()->format('H:i:s'); // Tambahkan waktu otomatis

        // Ambil jumlah_total terakhir
        $last = BlangkoAvailability::orderByDesc('created_at')->first();
        $jumlah_total_sebelumnya = $last ? $last->jumlah_total : 0;

        $data['jumlah_total'] = $jumlah_total_sebelumnya + (int)$data['jumlah_blanko'];

        BlangkoAvailability::create($data);

        return redirect()->route('admin.blangko.index')->with('success', 'Stok blangko berhasil ditambahkan.');
    }
    
    public function destroy($id)
    {
        $blangko = BlangkoAvailability::findOrFail($id);
        $blangko->delete();

        return redirect()->route('admin.blangko.index')->with('success', 'Data blangko berhasil dihapus.');
    }

    public function detail($id)
    {
        $blangko = BlangkoAvailability::findOrFail($id);
        return view('admin.blangko.detail', compact('blangko'));
    }

    public function printAll(Request $request)
    {
        $request->validate([
            'bulan' => 'nullable|integer|min:1|max:12',
            'tahun' => 'nullable|integer|min:2000',
        ]);

        $bulanInput = $request->input('bulan');
        $tahun = $request->input('tahun');

        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $bulan = $bulanInput ? $namaBulan[$bulanInput] : null;

        $query = BlangkoAvailability::query();

        if ($bulanInput) {
            $query->whereMonth('tanggal', $bulanInput);
        }

        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        $blangkos = $query->get();

        if ($blangkos->isEmpty()) {
            abort(404, 'Data tidak ditemukan untuk filter yang diberikan.');
        }

        $pdf = Pdf::loadView('admin.blangko.pdf', compact('blangkos', 'bulan', 'tahun'));
        return $pdf->stream('semua-blangko-' . now()->format('Ymd-His') . '.pdf');
    }
}
