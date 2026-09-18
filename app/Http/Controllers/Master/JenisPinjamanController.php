<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\BaseController;
use App\Models\Master\JenisPinjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JenisPinjamanController extends BaseController
{
    public function __construct()
    {
        $this->model = JenisPinjaman::class;
        $this->route = 'ms-jns-pinjaman';
        $this->titlePage = 'Daftar Jenis Pinjaman';
        $this->primaryKey = 'mjPinjamanId';
        $this->table = 'ms_jnspinjaman';
        $this->searchColumn = 'mjPinjamanNama';

        // $this->rules = [
        //     'mjPinjamanKode' => 'required|unique:ms_jnspinjaman,mjPinjamanKode',
        // ];

        $this->status = [
            ['value' => 'Active', 'name' => 'Active'],
            ['value' => 'Inactive', 'name' => 'Inactive'],
        ];

        $this->tipeBunga = [
            ['value' => 'Flat', 'name' => 'Flat'],
            ['value' => 'Efektif', 'name' => 'Efektif'],
        ];

        $this->form = [
            [
                'name' => 'mjPinjamanKode',
                'label' => 'Kode Pinjaman',
                'placeholder' => 'Masukkan kode pinjaman',
                'type' => 'text',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'mjPinjamanNama',
                'label' => 'Nama Pinjaman',
                'placeholder' => 'Masukkan nama pinjaman',
                'type' => 'text',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'mjSukuBunga',
                'label' => 'Suku Bunga (%)',
                'placeholder' => 'Masukkan suku bunga',
                'type' => 'number',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'mjTipeBunga',
                'label' => 'Tipe Bunga',
                'placeholder' => '-- Pilih tipe bunga --',
                'type' => 'select',
                'col' => 'col-md-6',
                'required' => true,
                'options' => $this->tipeBunga
            ],
            [
                'name' => 'mjPlafonMaksimal',
                'label' => 'Plafon Maksimal',
                'placeholder' => 'Masukkan plafon maksimal',
                'type' => 'angka',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'mjTenorMaksimal',
                'label' => 'Tenor Maksimal (bulan)',
                'placeholder' => 'Masukkan tenor maksimal',
                'type' => 'number',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'mjBiayaAdmin',
                'label' => 'Biaya Admin (%)',
                'placeholder' => 'Masukkan biaya admin',
                'type' => 'number',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'mjBiayaProvisi',
                'label' => 'Biaya Provisi (%)',
                'placeholder' => 'Masukkan biaya provisi',
                'type' => 'number',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'mjDendaKeterlambatan',
                'label' => 'Denda Keterlambatan (%)',
                'placeholder' => 'Masukkan denda keterlambatan',
                'type' => 'number',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'mjAkunPiutang',
                'label' => 'Akun Piutang',
                'placeholder' => '-- Cari dan pilih akun piutang --',
                'type' => 'autocomplete',
                'col' => 'col-md-6',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.objek.search'),
                    'textField' => 'text',
                    'valueField' => 'id',
                ]
            ],
            [
                'name' => 'mjAkunBunga',
                'label' => 'Akun Bunga',
                'placeholder' => '-- Cari dan pilih akun bunga --',
                'type' => 'autocomplete',
                'col' => 'col-md-6',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.objek.search'),
                    'fill' => ['fObjek' => '1.03'],
                    'textField' => 'text',
                    'valueField' => 'id',
                ]
            ],
            [
                'name' => 'mjAkunAdmin',
                'label' => 'Akun Admin',
                'placeholder' => '-- Cari dan pilih akun admin --',
                'type' => 'autocomplete',
                'col' => 'col-md-6',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.objek.search'),
                    'textField' => 'text',
                    'valueField' => 'id',
                ]
            ],
            [
                'name' => 'mjAkunDenda',
                'label' => 'Akun Denda',
                'placeholder' => '-- Cari dan pilih akun denda --',
                'type' => 'autocomplete',
                'col' => 'col-md-6',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.objek.search'),
                    'textField' => 'text',
                    'valueField' => 'id',
                ]
            ],
            [
                'name' => 'mjKeterangan',
                'label' => 'Keterangan',
                'placeholder' => 'Masukkan keterangan',
                'type' => 'textarea',
                'col' => 'col-md-6',
                'required' => false,
            ],
            [
                'name' => 'mjStatus',
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
                    'label' => 'Kode Pinjaman',
                    'field' => 'mjPinjamanKode',
                    'type' => 'text'
                ],
                [
                    'label' => 'Nama Pinjaman',
                    'field' => 'mjPinjamanNama',
                    'type' => 'text'
                ],
                [
                    'label' => 'Tipe Bunga',
                    'field' => 'mjTipeBunga',
                    'type' => 'text'
                ],
                [
                    'label' => 'Status',
                    'field' => 'mjStatus',
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

    protected function beforeUpdate(array $data, $id): array
    {
        $data['mjCreateBy'] = auth()->user()->name ?? '';
        $data['mjUpdatedBy'] = auth()->user()->name ?? '';
        return $data;
    }
}
