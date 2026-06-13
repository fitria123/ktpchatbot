<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\BlangkoAvailability;
use App\Models\DocumentRequest;
use App\Models\Uploads;
use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    private function desaList()
    {
        return [
            'Brebes',
            'Gandasuli',
            'Pasarbatang',
            'Limbangan Wetan',
            'Limbangan Kulon',
            'Randusanga Wetan',
            'Randusanga Kulon',
            'Kaligangsa Wetan',
            'Kaligangsa Kulon',
            'Krasak',
            'Padasugih',
            'Pemaron',
            'Pulosari',
            'Tengki',
            'Wangandalem',
            'Pagejugan',
            'Sigambir',
            'Kalimati',
            'Kaliwlingi',
            'Lembarawa',
            'Kedunguter',
            'Terlangu',
            'Banjaranyar'
        ];
    }

    private function handleFileUpload(Request $request, Uploads $upload, $fieldName, $folder)
    {
        if ($request->hasFile($fieldName)) {
            if ($upload->$fieldName && Storage::disk('public')->exists($upload->$fieldName)) {
                Storage::disk('public')->delete($upload->$fieldName);
            }
            $upload->$fieldName = $request->file($fieldName)->store($folder, 'public');
        }
    }

    public function create($userId)
    {
        $desaList = $this->desaList();
        $user = User::findOrFail($userId);

        $antrian = Antrian::where('user_id', $userId)
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        if (!$antrian) {
            return redirect()->route('admin.antrian.index')->with('error', 'User belum mengambil antrian hari ini.');
        }

        return view('admin.document.create', compact('desaList', 'user', 'antrian'));
    }

    public function store(Request $request)
    {
        $desaList = $this->desaList();

        $request->validate([
            'kk' => 'required|digits:16',
            'nik' => 'required|digits:16',
            'desa_kelurahan' => 'required|string|in:' . implode(',', $desaList),
            'alasan' => 'required|string',
        ]);

        $stokTerakhir = BlangkoAvailability::latest()->first();
        $stokTersedia = $stokTerakhir ? $stokTerakhir->jumlah_total : 0;

        if ($stokTersedia <= 0) {
            return redirect()->back()->with('error', 'Stok blangko tidak mencukupi. Permintaan tidak dapat diproses.');
        }

        DB::beginTransaction();
        try {
            $antrianHariIni = Antrian::where('id', $request->antrian_id)
                ->where('user_id', $request->user_id)
                ->first();

            if (!$antrianHariIni) {
                return redirect()->back()->with('error', 'Anda belum mengambil nomor antrian hari ini.');
            }

            $documentRequest = DocumentRequest::create([
                'id' => Str::uuid(),
                'user_id' => $request->user_id,
                'antrian_id' => $antrianHariIni->id,
                'kk' => $request->kk,
                'nik' => $request->nik,
                'desa_kelurahan' => $request->desa_kelurahan,
                'alasan' => $request->alasan,
                'status' => 'dalam proses pencetakan',
            ]);

            Uploads::create([
                'id' => Str::uuid(),
                'request_id' => $documentRequest->id,
                'file_kk' => null,
                'file_ktp_lama' => null,
                'file_surat_kehilangan' => null,
                'file_swafoto' => null,
            ]);

            $stokTerakhir->update(['jumlah_total' => $stokTersedia - 1]);

            DB::commit();
            return redirect()->route('admin.document.index')->with('success', 'Permohonan berhasil ditambahkan dan stok blangko dikurangi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function detailShow($id)
    {
        $request = DocumentRequest::with(['user', 'upload', 'antrian'])->findOrFail($id);
        return view('admin.document.detail', compact('request'));
    }

    public function detailPop($id)
    {
        $request = DocumentRequest::with(['user', 'upload', 'antrian'])->findOrFail($id);
        return view('admin.document._detail_modal', compact('request'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:dalam proses pencetakan,sudah tercetak,selesai pengambilan,ditolak',
            'alasan_ditolak' => $request->status === 'ditolak' ? 'required|string' : 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $document = DocumentRequest::with('user', 'antrian')->findOrFail($id);
            $previousStatus = $document->status;
            $newStatus = $request->status;

            $stokTerakhir = BlangkoAvailability::latest()->first();
            $stokTersedia = $stokTerakhir ? $stokTerakhir->jumlah_total : 0;

            // Kalau sebelumnya stok sudah dikurangi, lalu status berubah ke ditolak → balikin stok
            if ($document->stok_dikurangi && $newStatus === 'ditolak') {
                $stokTerakhir->update(['jumlah_total' => $stokTersedia + 1]);
                $document->stok_dikurangi = false;
            }

            $document->status = $newStatus;

            // simpan alasan ditolak hanya kalau status = ditolak
            if ($newStatus === 'ditolak') {
                $document->alasan_ditolak = $request->alasan_ditolak;
            } else {
                $document->alasan_ditolak = null;
            }

            $document->save();

            DB::commit();

            // Kirim notifikasi WA sesuai status baru (dengan format baru)
            $nama = $document->user?->name ?? 'Pemohon';
            $tanggalPendaftaran = $document->created_at ? Carbon::parse($document->created_at)->translatedFormat('d-m-Y') : '-';
            $nomorAntrian = $document->antrian?->nomor ?? '-'; // <-- PERBAIKAN: nomor_antrian -> nomor
            $nik = $document->nik ?? '-';
            $desa = $document->desa_kelurahan ?? '-';
            
            $pesan = match ($newStatus) {
                'dalam proses pencetakan' => "-Layanan Pendaftaran Cetak KTP Online Kecamatan Brebes-\n" .
                                             "Hallo {$nama} 😊\n" .
                                             "Berikut detail antrian Anda:\n" .
                                             "• Tanggal Pendaftaran : {$tanggalPendaftaran}\n" .
                                             "• Nomor Antrian : {$nomorAntrian}\n" .
                                             "• NIK : {$nik}\n" .
                                             "• Desa/Kelurahan : {$desa}\n\n" .
                                             "Status permohonan KTP-el Anda:\n" .
                                             "🕒 Dalam proses pencetakan\n\n" .
                                             "Mohon menunggu informasi selanjutnya. Terima kasih 🙏",
                                             
                'sudah tercetak' => "-Layanan Pendaftaran Cetak KTP Online Kecamatan Brebes-\n" .
                                    "Hallo {$nama} 😊\n" .
                                    "Berikut detail antrian Anda:\n" .
                                    "• Tanggal Pendaftaran : {$tanggalPendaftaran}\n" .
                                    "• Nomor Antrian : {$nomorAntrian}\n" .
                                    "• NIK : {$nik}\n" .
                                    "• Desa/Kelurahan : {$desa}\n\n" .
                                    "Status permohonan KTP-el Anda:\n" .
                                    "✅ KTP-el sudah selesai dicetak\n\n" .
                                    "Silakan datang ke Kantor Kecamatan Brebes untuk pengambilan.\n" .
                                    "Terima kasih 🙏",
                                    
                'ditolak' => "-Layanan Pendaftaran Cetak KTP Online Kecamatan Brebes-\n" .
                             "Hallo {$nama} 😊\n" .
                             "Berikut detail antrian Anda:\n" .
                             "• Tanggal Pendaftaran : {$tanggalPendaftaran}\n" .
                             "• Nomor Antrian : {$nomorAntrian}\n" .
                             "• NIK : {$nik}\n" .
                             "• Desa/Kelurahan : {$desa}\n\n" .
                             "Status permohonan KTP-el Anda:\n" .
                             "❌ Ditolak\n\n" .
                             "Alasan:\n" .
                             "{$document->alasan_ditolak}\n\n" .
                             "Silakan melakukan perbaikan data dan mengajukan kembali.\n" .
                             "Terima kasih 🙏",
                             
                default => null,
            };
            
            if ($pesan && $document->user?->phone) {
                FonnteService::sendMessage($document->user->phone, $pesan);
            }

            return $request->ajax()
                ? response()->json(['success' => true, 'message' => 'Status berhasil diperbarui.'])
                : redirect()->back()->with('success', 'Status berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function printHistory()
    {
        $requests = DocumentRequest::with('user')->latest()->get();
        return view('admin.document.print', compact('requests'));
    }

    public function print(Request $request)
    {
        $query = DocumentRequest::with('user');

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        if ($request->filled('alasan')) {
            $query->where('alasan', $request->alasan);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();

        $tanggal = $request->tanggal ? Carbon::parse($request->tanggal)->translatedFormat('d F Y') : 'Semua';
        $alasan = $request->alasan ?? 'Semua';
        $bulan = $request->bulan ? Carbon::create()->month($request->bulan)->translatedFormat('F') : 'Semua';
        $tahun = $request->tahun ?? 'Semua';

        $pdf = Pdf::loadView(
            'admin.document.pdf',
            compact('requests', 'tanggal', 'alasan', 'bulan', 'tahun')
        );

        return $pdf->stream('laporan-dokumen-' . now()->format('Y-m-d-His') . '.pdf');
    }
}