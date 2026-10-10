<?php

namespace App\Http\Controllers\Combo;

use App\Http\Controllers\BaseController;
use App\Models\Master\Mapping\MappingPengeluaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MapKeluarforcomboController extends BaseController
{
    public function __construct()
    {
        $this->model = MappingPengeluaran::class;
        $this->primaryKey = 'mapId';
        $this->searchColumn = ['mapNamaAsal', 'mapKodeAsal'];
    }
    public function index(Request $request): JsonResponse
    {
        $search = $request->get('search');

        $data = $this->model::query()
            ->when($search !== null && $search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('mapNamaAsal', 'LIKE', "%{$search}%")
                        ->orWhere('mapKodeAsal', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('mapKodeAsal')
            ->limit(50)
            ->get(['mapId', 'mapKodeAsal', 'mapNamaAsal']);

        return response()->json($data->map(function ($item) {
            return [
                'id' => $item->mapKodeAsal,
                'text' => $item->mapKodeAsal . ' - ' . $item->mapNamaAsal,
            ];
        }));
    }
}
