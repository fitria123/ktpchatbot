<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\DocumentRequest;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AntrianController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        $antrianHariIni = Antrian::with('documentRequest')
            ->where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->latest()
            ->first();

        // Default status
        $status = 'belum_ambil';
        $docStatus = null;

        if ($antrianHariIni) {
            $request = $antrianHariIni->documentRequest;

            if (!$request) {
                $status = 'ambil_belum_ajukan';
            } elseif (in_array($request->status, ['dalam proses verifikasi', 'dalam proses pencetakan', 'sudah tercetak'])) {
                $status = 'sedang_diproses';
                $docStatus = $request->status;
            } elseif ($request->status === 'selesai pengambilan') {
                $status = 'selesai';
            } elseif ($request->status === 'ditolak') {
                // Hapus antrian jika permohonan ditolak
                $antrianHariIni->delete();
                $status = 'belum_ambil';
                $antrianHariIni = null;
            }
        }

        return view('users.antrian.index', compact('status', 'antrianHariIni', 'docStatus'));
    }

    public function takeAntrian()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        // Cek antrian terakhir user (global)
        $existingAntrian = Antrian::with('documentRequest')
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        if ($existingAntrian) {
            // User sudah ambil antrian tapi belum buat dokumen
            if (!$existingAntrian->documentRequest) {
                return redirect()->route('user.antrian.index')
                    ->with('info', 'Kamu sudah ambil antrian (No. ' . $existingAntrian->nomor . '). Silakan lanjutkan pengajuan dokumen.')
                    ->with('antrian_id', $existingAntrian->id);
            }

            // Cek status permohonan terakhir
            $status = $existingAntrian->documentRequest->status;

            // Jika masih proses, user tidak bisa ambil antrian baru
            if (!in_array($status, ['selesai pengambilan', 'ditolak'])) {
                return redirect()->route('user.antrian.index')
                    ->with('info', 'Permohonan kamu sedang diproses. Tidak bisa ambil antrian baru.');
            }
        }

        // Ambil nomor terakhir dari SEMUA antrian (global)
        $lastNomor = Antrian::max('nomor') ?? 0;
        $nextNomor = $lastNomor + 1;

        // Buat antrian baru
        $antrian = Antrian::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'nomor' => $nextNomor,
            'tanggal' => $today,
        ]);

        // Simpan ID antrian di session untuk dipakai di create document
        session(['antrian_id' => $antrian->id]);

        return redirect()->route('user.document.create')
            ->with('success', 'Nomor antrian berhasil diambil: ' . $antrian->nomor);
    }
}
