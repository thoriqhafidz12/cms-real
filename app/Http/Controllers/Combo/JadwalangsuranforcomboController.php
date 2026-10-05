<?php

namespace App\Http\Controllers\Combo;

use App\Http\Controllers\BaseController;
use App\Models\Pinjaman\JadwalAngsuran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JadwalangsuranforcomboController extends BaseController
{
    public function __construct()
    {
        $this->model = JadwalAngsuran::class;
        $this->primaryKey = 'tjaId';
        $this->searchColumn = ['tjaCicilanKe'];
    }
    public function index(Request $request): JsonResponse
    {
        $search = $request->get('search');
        $fPinjaman = $request->get('tppPinjamanId');

        $data = $this->model::where('tjaCicilanKe', 'like', "%{$search}%")
            ->when($fPinjaman, function ($query) use ($fPinjaman) {
                $query->where('tjaPinjamanId', $fPinjaman);
            })
            ->where('tjaStatus', 0)
            ->orderBy('tjaCicilanKe', 'asc')
            // ->limit(20)
            ->get(['tjaId', 'tjaPinjamanId', 'tjaTanggalAngsuran', 'tjaCicilanKe', 'tjaNominalAngsuran', 'tjaNominalBunga', 'tjaTotalTagihan', 'tjaSisaTagihan', 'tjaStatus']);

        return response()->json($data->map(function ($item) {
            return [
                'id' => $item->tjaId,
                'text' => 'Angsuran ke-' . $item->tjaCicilanKe . ' | ' . $this->dateIDkosong($item->tjaTanggalAngsuran) . ' | Rp ' . $this->formatRupiah($item->tjaTotalTagihan, 2),
                'totalTagihan' => $item->tjaTotalTagihan,
                'nominalAngsuran' => $item->tjaNominalAngsuran,
                'nominalBunga' => $item->tjaNominalBunga,
                'sisaTagihan' => $item->tjaSisaTagihan,
            ];
        }));
    }
}
