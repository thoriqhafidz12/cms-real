<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\BaseController;
use App\Models\Master\MetodePembayaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MetodePembayaranController extends BaseController
{
    public function __construct()
    {
        $this->model = MetodePembayaran::class;
        $this->route = 'ms-metode-bayar';
        $this->titlePage = 'Daftar Metode Pembayaran';
        $this->primaryKey = 'mmId';
        $this->table = 'ms_metodebayar';
        $this->searchColumn = ['mmKode', 'mmNama'];

        $this->status = [
            ['value' => 'Active', 'name' => 'Active'],
            ['value' => 'Inactive', 'name' => 'Inactive'],
        ];

        $this->tipeMetode = [
            ['value' => 'Tunai', 'name' => 'Tunai'],
            ['value' => 'Transfer Bank', 'name' => 'Transfer Bank'],
            ['value' => 'E-Wallet(Qris, Dana, dll)', 'name' => 'E-Wallet(Qris, Dana, dll)'],
            ['value' => 'Potong Hasil Lelang', 'name' => 'Potong Hasil Lelang'],
        ];

        $this->form = [
            [
                'name' => 'mmKode',
                'label' => 'Kode Metode',
                'placeholder' => 'Masukkan kode metode',
                'type' => 'text',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'mmNama',
                'label' => 'Nama Metode',
                'placeholder' => 'Masukkan nama metode',
                'type' => 'text',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'mmTipe',
                'label' => 'Tipe Metode',
                'placeholder' => '-- Pilih tipe metode --',
                'type' => 'select',
                'col' => 'col-md-6',
                'required' => true,
                'options' => $this->tipeMetode
            ],
            [
                'name' => 'mmBiayaAdmin',
                'label' => 'Biaya Admin',
                'placeholder' => 'Isi 0 jika tanpa admin',
                'type' => 'angka',
                'col' => 'col-md-6',
                'required' => true
            ],
            [
                'name' => 'mmBank',
                'label' => 'Bank',
                'placeholder' => 'Masukkan nama bank',
                'type' => 'text',
                'col' => 'col-md-6',
                'required' => false,
            ],
            [
                'name' => 'mmNoRek',
                'label' => 'Nomor Rekening',
                'placeholder' => 'Masukkan nomor rekening',
                'type' => 'number',
                'col' => 'col-md-6',
                'required' => false,
            ],
            [
                'name' => 'mmAtasNama',
                'label' => 'Atas Nama',
                'placeholder' => 'Masukkan atas nama',
                'type' => 'text',
                'col' => 'col-md-6',
                'required' => false
            ],
            [
                'name' => 'mmAkunGl',
                'label' => 'Akun GL',
                'placeholder' => '-- Cari dan pilih akun GL --',
                'type' => 'autocomplete',
                'col' => 'col-md-6',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.objek.search'),
                    'textField' => 'text',
                    'fill' => ['fObjek' => '1.01'],
                    'valueField' => 'id',
                ]
            ],
            [
                'name' => 'mmKeterangan',
                'label' => 'Keterangan',
                'placeholder' => 'Masukkan keterangan',
                'type' => 'textarea',
                'col' => 'col-md-12',
                'required' => false,
            ],
            [
                'name' => 'mmStatus',
                'label' => 'Status',
                'placeholder' => '-- Pilih status --',
                'type' => 'select',
                'col' => 'col-md-12',
                'required' => true,
                'options' => $this->status
            ]
        ];

        $this->grid =
            [
                [
                    'label' => 'Kode Metode',
                    'field' => 'mmKode',
                    'type' => 'text'
                ],
                [
                    'label' => 'Nama Metode',
                    'field' => 'mmNama',
                    'type' => 'text'
                ],
                [
                    'label' => 'Tipe Metode',
                    'field' => 'mmTipe',
                    'type' => 'text'
                ],
                [
                    'label' => 'Status',
                    'field' => 'mmStatus',
                    'type' => 'text'
                ]
            ];
    }
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $editId = $request->get('edit');

        $query = $this->model::query();

        if ($search && $this->searchColumn) {
            $columns = (array) $this->searchColumn;
            $query->where(function ($q) use ($columns, $search) {
                foreach ($columns as $col) {
                    $q->orWhere($col, 'like', "%{$search}%");
                }
            });
        }

        $items = $query->orderBy($this->primaryKey, 'desc')
            ->paginate(10)
            ->withQueryString();

        $editData = null;
        if ($editId) {
            $editData = $this->model::where($this->primaryKey, $editId)->first();
        }

        $extra = [];
        foreach ($this->extraViewData as $key => $resolver) {
            $extra[$key] = is_callable($resolver) ? $resolver() : $resolver;
        }

        return view('master', array_merge([
            'items' => $items,
            'search' => $search,
            'editData' => $editData,
            'form' => $this->form,
            'route' => $this->route,
            'primaryKey' => $this->primaryKey,
            'titlePage' => $this->titlePage,
            'grid' => $this->grid,
        ], $extra));
    }

    public function search(Request $request): JsonResponse
    {
        $search = $request->get('search');

        $data = $this->model::where('mmNama', 'like', "%{$search}%")
            ->orderBy('mmKode')
            ->limit(20)
            ->get(['mmId', 'mmKode', 'mmNama']);

        return response()->json($data->map(function ($item) {
            return [
                'id' => $item->mmId,
                'text' => $item->mmId . ' - ' . $item->mmNama,
                'hiddenValue' => $item->mmNama,
            ];
        }));
    }
}
