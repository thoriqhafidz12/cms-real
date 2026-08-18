<?php

namespace App\Http\Controllers\Aset;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Aset\AsetModel;
use Illuminate\View\View;

class TerimaasetController extends BaseController
{
    public function __construct()
    {
        $this->model = AsetModel::class;
        $this->route = 'terima-aset';
        $this->titlePage = 'Aset';
        $this->primaryKey = 'asId';
        $this->table = 'tr_aset';
        $this->searchColumn = ['asNamaBarang'];

        $this->routeDetail = 'api/aset/detail/';

        $this->form = [
            [
                'name' => 'asNamaBarang',
                'label' => 'Nama Barang',
                'placeholder' => 'Masukkan nama barang',
                'type' => 'text',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'asTglTerima',
                'label' => 'Tanggal Terima',
                'placeholder' => 'Masukkan tanggal',
                'type' => 'date',
                'col' => 'col-md-6',
                'required' => false,
            ],
            [
                'name' => 'asTahun',
                'label' => 'Tahun Terima',
                'placeholder' => 'Masukkan tahun',
                'type' => 'number',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'asMasaManfaat',
                'label' => 'Masa Manfaat',
                'placeholder' => '(Tahun) Masa Manfaat',
                'type' => 'text',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'asHarga',
                'label' => 'Nominal',
                'placeholder' => 'Masukkan nominal',
                'type' => 'angka',
                'col' => 'col-md-6',
                'required' => true
            ],
            [
                'name' => 'asKeterangan',
                'label' => 'Keterangan',
                'placeholder' => 'Masukkan keterangan',
                'type' => 'text',
                'col' => 'col-md-6',
                'required' => false,
            ]
        ];

        $this->grid = [
            [
                'label' => 'Nama',
                'field' => 'asNamaBarang',
                'type' => 'text'
            ],
            [
                'label' => 'Tahun Terima',
                'field' => 'asTahun',
                'type' => 'text'
            ],
            [
                'label' => 'Harga',
                'field' => 'asHarga',
                'type' => 'angka',
                'class' => 'text-right'
            ],
            [
                'label' => 'Masa Manfaat (Tahun)',
                'field' => 'asMasaManfaat',
                'type' => 'text'
            ]
        ];
    }
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $editId = $request->get('edit');

        $userId = auth()->user()->id; // Get the authenticated user's ID

        $query = $this->model::query();

        if ($search && $this->searchColumn) {
            $columns = (array) $this->searchColumn;
            $query->where(function ($q) use ($columns, $search) {
                foreach ($columns as $col) {
                    $q->orWhere($col, 'like', "%{$search}%");
                }
            });
        }

        $items = $query
            ->where('asUserId', $userId)
            ->orderBy($this->primaryKey, 'desc')
            ->paginate(10)
            ->withQueryString();

        $editData = null;
        if ($editId) {
            $editData = $this->model::find($editId);
        }

        $extra = [];
        foreach ($this->extraViewData as $key => $resolver) {
            $extra[$key] = is_callable($resolver) ? $resolver() : $resolver;
        }

        return view('aset.aset', array_merge([
            'items' => $items,
            'search' => $search,
            'editData' => $editData,
            'form' => $this->form,
            'route' => $this->route,
            'primaryKey' => $this->primaryKey,
            'titlePage' => $this->titlePage,
            'grid' => $this->grid,
            'routeDetail' => $this->routeDetail
        ], $extra));
    }
    protected function beforeSave(array $data, $record = null): array
    {
        $data['asUserId'] = auth()->user()->id;
        $data['asCreatedBy'] = auth()->user()->name;
        $data['asUpdatedBy'] = auth()->user()->name;

        return $data;
    }
    protected function beforeUpdate(array $data, $record): array
    {
        $data['asUpdatedBy'] = auth()->user()->name;

        return $data;
    }

    public function getDetail($id)
    {
        $item = $this->model::find($id);

        if ($item) {
            $penyusutanPertahun = $item->asHarga / $item->asMasaManfaat;
            $penyusutanThnBerjalan = $penyusutanPertahun * (date('Y') - $item->asTahun);
            $sisaNilai = $item->asHarga - $penyusutanThnBerjalan;
            $sudahDipakai = $penyusutanThnBerjalan / $penyusutanPertahun;
            $layakPakai = $item->asMasaManfaat - $sudahDipakai;
            $uangPerbulan = $penyusutanPertahun / 12;
            return response()->json([
                'success' => true,
                'data' => $item,
                'detail' => [
                    'penyusutanPertahun' => $penyusutanPertahun,
                    'penyusutanTahunBerjalan' => $penyusutanThnBerjalan,
                    'sisaNilaiPenyusutan' => $sisaNilai,
                    'sudahDipakai' => $sudahDipakai,
                    'sisaLayakPakai' => $layakPakai,
                    'uangPerbulan' => $uangPerbulan
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ], 404);
        }
    }
}
