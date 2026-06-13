<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BlangkoAvailability;
use App\Models\Antrian;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index()
    {
        $blangkoStock = optional(BlangkoAvailability::orderByDesc('created_at')->first())->jumlah_total ?? 0;
        return view('users.index', compact('blangkoStock'));
    }

    public function documentRequest()
    {
        $desaList = [
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

        $user = auth()->user();

        // Ambil antrian hari ini
        $antrianHariIni = Antrian::where('user_id', $user->id)
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        // Ambil dokumen terakhir user
        $lastRequest = DocumentRequest::where('user_id', $user->id)
            ->latest()
            ->first();

        // Ambil stok terakhir
        $stokBlangko = BlangkoAvailability::latest()->value('jumlah_total') ?? 0;

        return view('users.document.create', compact(
            'desaList',
            'user',
            'antrianHariIni',
            'lastRequest',
            'stokBlangko'
        ));
    }
}
