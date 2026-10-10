<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\BaseController;
use App\Models\Jurnal\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengeluaranController extends BaseController
{
    public function __construct()
    {
        $this->model = Pengeluaran::class;
        $this->route = 'pengeluaran';
        $this->titlePage = 'Daftar Pengeluaran';
        $this->primaryKey = 'kId';
        $this->table = 'tr_keluar';
        $this->searchColumn = ['kNo', 'kKeterangan', 'kSumber'];

        $this->form = [
            [
                'name' => 'kNilai',
                'label' => 'Nilai Bayar',
                'placeholder' => 'Masukkan nilai bayar',
                'type' => 'angka',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'kTgl',
                'label' => 'Tanggal Bayar',
                'placeholder' => 'Masukkan tanggal bayar',
                'type' => 'date',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'kCoa',
                'label' => 'COA',
                'placeholder' => '-- Cari dan pilih COA --',
                'type' => 'autocomplete',
                'col' => 'col-md-6',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.mapping-pengeluaran.search'),
                    'textField' => 'text',
                    'valueField' => 'id',
                ]
            ],
            [
                'name' => 'kKeterangan',
                'label' => 'Keterangan',
                'placeholder' => 'Masukkan keterangan',
                'type' => 'textarea',
                'col' => 'col-md-6',
                'required' => true,
            ]
        ];

        $this->grid =
            [
                [
                    'label' => 'Nomor Pengeluaran',
                    'field' => 'kNo',
                    'type' => 'text'
                ],
                [
                    'label' => 'Tanggal',
                    'field' => 'kTgl',
                    'type' => 'date'
                ],
                [
                    'label' => 'Nominal',
                    'field' => 'kNilai',
                    'type' => 'angka'
                ],
                [
                    'label' => 'Keterangan',
                    'field' => 'kKeterangan',
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

        return view('bendahara.pengeluaran', array_merge([
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

    protected function beforeSave(array $data, $record = null): array
    {
        $counterKeluar = $this->getKeluarCounter($data['kTgl']);

        $data['kNo'] = str_pad($counterKeluar, 5, '0', STR_PAD_LEFT) . '/PENGELUARAN/' . $data['kTgl'];
        return $data;
    }

    protected function afterSave($data)
    {
        $this->createJurnal('PENGELUARAN', $data['kCoa'], $data, 'M-KELUAR');
    }

    protected function beforeUpdate(array $data, $record): array
    {
        $this->hapusJurnal('M-KELUAR', $record->{$this->primaryKey});
        return $data;
    }

    protected function afterUpdate($data, $id)
    {
        $this->createJurnal('PENGELUARAN', $data['kCoa'], $data, 'M-KELUAR');
    }

    protected function beforeDelete($id)
    {
        $this->hapusJurnal('M-KELUAR', $id);
    }
}
