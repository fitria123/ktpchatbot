<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\TakeEktp;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TakeEktpController extends Controller
{
    public function show($requestId)
    {
        $request = DocumentRequest::findOrFail($requestId);
        return view('admin.document.take_ektp.input', compact('request'));
    }

    // Simpan data pengambilan
    public function store(Request $request, $requestId)
    {
        $request->validate([
            'nama_pengambil' => 'required|string|max:255',
            'nik' => 'required|digits:16', 
            'tanggal_pengambilan' => 'required|date',
        ]);

        $documentRequest = DocumentRequest::with('user')->findOrFail($requestId);

        // Simpan data pengambilan
        TakeEktp::create([
            'id' => Str::uuid(),
            'request_id' => $documentRequest->id,
            'nama_pengambil' => $request->nama_pengambil,
            'nik' => $request->nik,
            'tanggal_pengambilan' => $request->tanggal_pengambilan,
        ]);

        // Update status dokumen
        $documentRequest->status = 'selesai pengambilan';
        $documentRequest->save();

        // Kirim notifikasi WA dengan format baru
        $tanggal = Carbon::parse($request->tanggal_pengambilan)->format('d-m-Y');
        $nama = $documentRequest->user?->name ?? 'Pemohon';
        
        // Format pesan baru
        $pesan = "-Layanan Pendaftaran Cetak KTP Online Kecamatan Brebes-\n" .
                 "Hallo {$nama} 😊\n" .
                 "KTP-el Anda telah diambil pada tanggal {$tanggal}.\n" .
                 "Terima kasih 🙏";

        $msg = 'Data pengambilan berhasil disimpan.';
        $phone = $documentRequest->user?->phone;

        if (!$phone) {
            $msg .= ' (Nomor WhatsApp pemohon tidak tersedia)';
        } else {
            $waResult = FonnteService::sendMessage($phone, $pesan);
            if (!($waResult['ok'] ?? false)) {
                $msg .= ' (Notifikasi WhatsApp gagal: ' . ($waResult['reason'] ?? 'tidak diketahui') . ')';
            }
        }

        return redirect()->route('admin.document.takeEktp')->with('success', $msg);
    }
}