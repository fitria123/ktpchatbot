<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\BlangkoAvailability;
use App\Models\DocumentRequest;
use App\Models\Uploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentRequestController extends Controller
{
    // Form edit permohonan yang ditolak
    public function edit($id)
    {
        $user = auth()->user();

        $document = DocumentRequest::with('upload')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'ditolak')
            ->firstOrFail();

        $desaList = $this->desaList();
        $stokBlangko = BlangkoAvailability::latest()->value('jumlah_total') ?? 0;

        return view('users.document.edit', compact('document', 'desaList', 'stokBlangko'));
    }

    // Update permohonan yang ditolak (sudah diperbaiki)
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $document = DocumentRequest::with('upload')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'ditolak')
            ->firstOrFail();

        $desaList = $this->desaList();

        // -----------------------------
        // VALIDATION - KHUSUS EDIT (semua nullable)
        // -----------------------------
        $rules = [
            'kk' => 'required|digits:16',
            'nik' => 'required|digits:16',
            'desa_kelurahan' => 'required|string|in:' . implode(',', $desaList),
            'alasan' => 'required|string',
            // SEMUA FILE NULLABLE karena edit boleh tidak upload ulang
            'file_kk' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'file_swafoto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'file_ktp_lama' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'file_surat_kehilangan' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ];

        $messages = [
            'kk.required' => 'Nomor KK wajib diisi.',
            'kk.digits' => 'Nomor KK harus 16 digit.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus 16 digit.',
            'desa_kelurahan.required' => 'Desa/Kelurahan wajib dipilih.',
            'desa_kelurahan.in' => 'Desa/Kelurahan tidak valid.',
            'alasan.required' => 'Alasan permohonan wajib dipilih.',
            'file_kk.mimes' => 'File KK harus berupa JPG, JPEG, atau PNG.',
            'file_kk.max' => 'Ukuran file KK maksimal 2MB.',
            'file_swafoto.mimes' => 'Swafoto harus berupa JPG, JPEG, atau PNG.',
            'file_swafoto.max' => 'Ukuran swafoto maksimal 2MB.',
            'file_ktp_lama.mimes' => 'File KTP Lama harus berupa JPG, JPEG, atau PNG.',
            'file_ktp_lama.max' => 'Ukuran file KTP Lama maksimal 2MB.',
            'file_surat_kehilangan.mimes' => 'File Surat Kehilangan harus berupa JPG, JPEG, atau PNG.',
            'file_surat_kehilangan.max' => 'Ukuran file Surat Kehilangan maksimal 2MB.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // -----------------------------
        // UPDATE DATA
        // -----------------------------
        $document->update([
            'kk' => $request->kk,
            'nik' => $request->nik,
            'desa_kelurahan' => $request->desa_kelurahan,
            'alasan' => $request->alasan,
            'status' => 'dalam proses verifikasi',
            'alasan_ditolak' => null
        ]);

        $upload = $document->upload ?? new Uploads(['request_id' => $document->id]);

        $this->handleFileUpload($request, $upload, 'file_kk', 'uploads/kk');
        $this->handleFileUpload($request, $upload, 'file_ktp_lama', 'uploads/ktp_lama');
        $this->handleFileUpload($request, $upload, 'file_surat_kehilangan', 'uploads/surat_kehilangan');
        $this->handleFileUpload($request, $upload, 'file_swafoto', 'uploads/swafoto');

        $upload->save();

        return redirect()->route('user.document.status')
            ->with('success', 'Permohonan berhasil diperbarui dan dikirim ulang untuk verifikasi.');
    }

    // Store permohonan baru (TETAP SAMA, TIDAK DIUBAH)
    public function store(Request $request)
    {
        $desaList = $this->desaList();

        // -----------------------------
        // VALIDATION - STORE (semua required)
        // -----------------------------
        $rules = [
            'kk' => 'required|digits:16',
            'nik' => 'required|digits:16',
            'desa_kelurahan' => 'required|string|in:' . implode(',', $desaList),
            'alasan' => 'required|string',
            'file_kk' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'file_swafoto' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ];

        if ($request->alasan === 'Pembuatan KTP Rusak' || $request->alasan === 'pembaruan data KTP') {
            $rules['file_ktp_lama'] = 'required|file|mimes:jpg,jpeg,png|max:2048';
        } else {
            $rules['file_ktp_lama'] = 'nullable|file|mimes:jpg,jpeg,png|max:2048';
        }

        if ($request->alasan === 'KTP hilang') {
            $rules['file_surat_kehilangan'] = 'required|file|mimes:jpg,jpeg,png|max:2048';
        } else {
            $rules['file_surat_kehilangan'] = 'nullable|file|mimes:jpg,jpeg,png|max:2048';
        }

        $messages = [
            'kk.required' => 'Nomor KK wajib diisi.',
            'kk.digits' => 'Nomor KK harus 16 digit.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus 16 digit.',
            'desa_kelurahan.required' => 'Desa/Kelurahan wajib dipilih.',
            'desa_kelurahan.in' => 'Desa/Kelurahan tidak valid.',
            'alasan.required' => 'Alasan permohonan wajib dipilih.',
            'file_kk.required' => 'File KK wajib di-upload.',
            'file_kk.mimes' => 'File KK harus berupa JPG, JPEG, atau PNG.',
            'file_kk.max' => 'Ukuran file KK maksimal 2MB.',
            'file_swafoto.required' => 'Swafoto wajib di-upload.',
            'file_swafoto.mimes' => 'Swafoto harus berupa JPG, JPEG, atau PNG.',
            'file_swafoto.max' => 'Ukuran swafoto maksimal 2MB.',
            'file_ktp_lama.required' => 'File KTP Lama wajib di-upload untuk alasan ini.',
            'file_surat_kehilangan.required' => 'File Surat Kehilangan wajib di-upload untuk alasan ini.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // -----------------------------
        // STOK BLANGKO
        // -----------------------------
        $stokBlangko = BlangkoAvailability::latest()->value('jumlah_total') ?? 0;
        if ($stokBlangko <= 0) {
            return redirect()->back()->with('error', 'Stok blangko tidak mencukupi.');
        }

        // -----------------------------
        // STORE DATA
        // -----------------------------
        DB::beginTransaction();
        try {
            $antrianHariIni = Antrian::where('user_id', auth()->id())
                ->whereDate('tanggal', now()->toDateString())
                ->firstOrFail();

            $document = DocumentRequest::create([
                'id' => Str::uuid(),
                'user_id' => auth()->id(),
                'antrian_id' => $antrianHariIni->id,
                'kk' => $request->kk,
                'nik' => $request->nik,
                'desa_kelurahan' => $request->desa_kelurahan,
                'alasan' => $request->alasan,
                'status' => 'dalam proses verifikasi'
            ]);

            $upload = new Uploads([
                'id' => Str::uuid(),
                'request_id' => $document->id,
            ]);

            $this->handleFileUpload($request, $upload, 'file_kk', 'uploads/kk');
            $this->handleFileUpload($request, $upload, 'file_ktp_lama', 'uploads/ktp_lama');
            $this->handleFileUpload($request, $upload, 'file_surat_kehilangan', 'uploads/surat_kehilangan');
            $this->handleFileUpload($request, $upload, 'file_swafoto', 'uploads/swafoto');

            $upload->save();

            // Kurangi stok blangko
            $stok = BlangkoAvailability::latest()->first();
            if ($stok) $stok->decrement('jumlah_total');

            DB::commit();

            return redirect()->route('user.index')->with('success', 'Permintaan dokumen berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Riwayat permohonan
    public function status()
    {
        $requests = DocumentRequest::with('upload', 'statusLogs', 'antrian')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('users.document.status', compact('requests'));
    }

    // Daftar desa
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

    // Helper function untuk upload file (SUDAH DIPERBAIKI - tidak ada else null)
    private function handleFileUpload(Request $request, Uploads $upload, $fieldName, $folder)
    {
        if ($request->hasFile($fieldName)) {
            // hapus file lama jika ada
            if (
                !empty($upload->$fieldName) &&
                Storage::disk('public')->exists($upload->$fieldName)
            ) {
                Storage::disk('public')->delete($upload->$fieldName);
            }

            // simpan file baru
            $upload->$fieldName = $request
                ->file($fieldName)
                ->store($folder, 'public');
        }

        // jika tidak upload file baru, biarkan file lama tetap ada
    }
}