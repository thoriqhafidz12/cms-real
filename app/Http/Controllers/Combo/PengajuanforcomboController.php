<?php

namespace App\Http\Controllers\Combo;

use App\Http\Controllers\BaseController;
use App\Models\Pinjaman\PengajuanPinjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengajuanforcomboController extends BaseController
{
    public function __construct()
    {
        $this->model = PengajuanPinjaman::class;
        $this->primaryKey = 'tpId';
        $this->searchColumn = ['tpAnggotaNama', 'tpKode'];
    }
    public function index(Request $request): JsonResponse
    {
        $search = $request->get('search');

        $data = $this->model::where('tpAnggotaNama', 'like', "%{$search}%")
            ->where('tpStatus', 1)
            ->orderBy('tpId', 'desc')
            ->limit(20)
            ->get(['tpId', 'tpKode', 'tpAnggotaId', 'tpAnggotaNama', 'tpNilaiDisetuji', 'tpTenorDisetuji', 'tpBunga']);

        return response()->json($data->map(function ($item) {
            return [
                'id' => $item->tpId,
                'text' => $item->tpKode . ' - ' . $item->tpAnggotaNama . ' | Rp ' . $this->formatRupiah($item->tpNilaiDisetuji, 2),
                'anggotaId' => $item->tpAnggotaId,
                'anggotaNama' => $item->tpAnggotaNama,
                'nominalPinjaman' => $item->tpNilaiDisetuji,
                'tenor' => $item->tpTenorDisetuji,
                'bunga' => $item->tpBunga,
            ];
        }));
    }
}
