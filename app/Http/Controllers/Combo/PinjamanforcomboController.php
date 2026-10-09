<?php

namespace App\Http\Controllers\Combo;

use App\Http\Controllers\BaseController;
use App\Models\Pinjaman\Pinjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PinjamanforcomboController extends BaseController
{
    public function __construct()
    {
        $this->model = Pinjaman::class;
        $this->primaryKey = 'trpjId';
        $this->searchColumn = ['trpAnggotaNama', 'trpNoPinjaman'];
    }
    public function index(Request $request): JsonResponse
    {
        $search = $request->get('search');

        $data = $this->model::where('trpAnggotaNama', 'like', "%{$search}%")
            ->where('trpStatusPinjaman', 0)
            ->orderBy('trpjId', 'desc')
            ->limit(20)
            ->get(['trpjId', 'trpAnggotaId', 'trpAnggotaNama', 'trpNoPinjaman', 'trpNominalPinjaman', 'trpBunga', 'trpCicilanPokok', 'trpCicilanBunga', 'trpTotalCicilan', 'trpTenor', 'trpSudahDibayar', 'trpSisaCicilan']);

        return response()->json($data->map(function ($item) {
            return [
                'id' => $item->trpjId,
                'text' => $item->trpNoPinjaman . ' | ' . $item->trpAnggotaNama,
                'noPinjaman' => $item->trpNoPinjaman,
                'anggotaId' => $item->trpAnggotaId,
                'anggotaNama' => $item->trpAnggotaNama,
                'nominalPinjaman' => $this->formatRupiah($item->trpNominalPinjaman, 2),
                'totalPinjaman' => $this->formatRupiah($item->trpTotalCicilan * $item->trpTenor, 2),
                'bunga' => $item->trpBunga,
                'cicilanPokok' => $this->formatRupiah($item->trpCicilanPokok, 2),
                'cicilanBunga' => $this->formatRupiah($item->trpCicilanBunga, 2),
                'totalCicilan' => $this->formatRupiah($item->trpTotalCicilan, 2),
                'sudahDibayar' => $this->formatRupiah($item->trpSudahDibayar, 2),
                'sisaCicilan' => $this->formatRupiah($item->trpSisaCicilan, 2),
                'tenor' => $item->trpTenor,
            ];
        }));
    }
}
