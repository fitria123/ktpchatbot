<?php

namespace App\Http\Controllers\Panel;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Antrian;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DocumentRequest;
use App\Models\BlangkoAvailability;
use App\Http\Controllers\Controller;

class AntrianController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        $users = User::where('role', 'user')
            ->when($keyword, function ($query, $keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            })
            ->get();

        $today = Carbon::today();
        $antriansToday = Antrian::whereDate('tanggal', $today)->get()->keyBy('user_id');
        $pengajuansToday = DocumentRequest::whereDate('created_at', $today)->get()->groupBy('user_id');
        $stokBlangko = BlangkoAvailability::orderByDesc('created_at')->value('jumlah_total') ?? 0;

        // Ambil status dokumen terakhir untuk setiap user
        $userDocStatuses = [];
        foreach ($users as $user) {
            $lastDoc = DocumentRequest::where('user_id', $user->id)
                ->latest()
                ->first();

            if (!$lastDoc) {
                // Kalau user punya antrian hari ini tapi belum ada DocumentRequest
                if ($antriansToday->has($user->id)) {
                    $userDocStatuses[$user->id] = 'dalam pengajuan';
                } else {
                    $userDocStatuses[$user->id] = 'Belum ada pengajuan';
                }
            } else {
                $userDocStatuses[$user->id] = $lastDoc->status;
            }
        }

        return view('admin.antrian.index', compact(
            'users',
            'antriansToday',
            'pengajuansToday',
            'today',
            'keyword',
            'stokBlangko',
            'userDocStatuses'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $userId = $request->user_id;

        // Ambil tanggal dari request (default = hari ini)
        $tanggal = $request->input('tanggal')
            ? Carbon::parse($request->input('tanggal'))
            : Carbon::today();

        // Cek document request terakhir
        $lastRequest = DocumentRequest::where('user_id', $userId)
            ->latest()
            ->first();

        if ($lastRequest) {
            if (in_array($lastRequest->status, [
                'dalam proses pencetakan',
                'sudah tercetak'
            ])) {
                return redirect()->route('admin.antrian.index')
                    ->with('info', 'User ini masih memiliki pengajuan yang belum selesai.');
            }

            if ($lastRequest->status === 'selesai pengambilan') {
                $tanggalSelesai = Carbon::parse($lastRequest->created_at)->toDateString();
                $today = Carbon::today()->toDateString();

                if ($tanggalSelesai === $today) {
                    return redirect()->route('admin.antrian.index')
                        ->with('info', 'User ini sudah menyelesaikan pengajuan hari ini, coba lagi besok.');
                }
            }
        }

        // Cek apakah sudah ada antrian untuk tanggal ini
        $existingAntrian = Antrian::where('user_id', $userId)
            ->whereDate('tanggal', $tanggal)
            ->first();

        if ($existingAntrian) {
            $docRequest = DocumentRequest::where('antrian_id', $existingAntrian->id)
                ->where('user_id', $userId)
                ->latest()
                ->first();

            if ($docRequest && $docRequest->status !== 'ditolak') {
                return redirect()->route('admin.antrian.index')
                    ->with('info', 'User ini sudah memiliki antrian di tanggal ini.');
            }

            return redirect()->route('admin.document.create', [
                'userId' => $userId,
                'antrian_id' => $existingAntrian->id
            ]);
        }

        // Ambil nomor antrian global (tidak reset per hari)
        $lastNomor = Antrian::max('nomor') ?? 0;
        $nextNomor = $lastNomor + 1;

        // Buat antrian baru
        $antrian = Antrian::create([
            'id' => Str::uuid(),
            'user_id' => $userId,
            'nomor' => $nextNomor,
            'tanggal' => $tanggal
        ]);

        return redirect()->route('admin.document.create', [
            'userId' => $userId,
            'antrian_id' => $antrian->id
        ])->with('info', 'Antrian berhasil diambil dan status user menjadi "dalam pengajuan".');
    }
}
