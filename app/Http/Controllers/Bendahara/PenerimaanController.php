<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\BaseController;
use App\Models\Jurnal\Penerimaan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PenerimaanController extends BaseController
{
    public function __construct()
    {
        $this->model = Penerimaan::class;
        $this->route = 'penerimaan';
        $this->titlePage = 'Daftar Penerimaan';
        $this->primaryKey = 'tId';
        $this->table = 'tr_terima';
        $this->searchColumn = ['tKwitansi', 'tNoPenerimaan', 'tNilaiBayar', 'tTglBayar'];

        // $this->rules = [
        //     'mjPinjamanKode' => 'required|unique:ms_jnspinjaman,mjPinjamanKode',
        // ];

        $this->status = [
            ['value' => 'Active', 'name' => 'Active'],
            ['value' => 'Inactive', 'name' => 'Inactive'],
        ];

        $this->form = [
            [
                'name' => 'tNilaiBayar',
                'label' => 'Nilai Bayar',
                'placeholder' => 'Masukkan nilai bayar',
                'type' => 'angka',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'tTglBayar',
                'label' => 'Tanggal Bayar',
                'placeholder' => 'Masukkan tanggal bayar',
                'type' => 'date',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'tAsalPenerimaan',
                'label' => 'Asal Penerimaan',
                'placeholder' => 'Masukkan asal penerimaan',
                'type' => 'text',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'tCoa',
                'label' => 'COA',
                'placeholder' => '-- Cari dan pilih COA --',
                'type' => 'autocomplete',
                'col' => 'col-md-6',
                'required' => true,
                'autocomplete' => [
                    'url' => route('api.objek.search'),
                    'fill' => ['fObjek' => '4.0'],
                    'textField' => 'text',
                    'valueField' => 'id',
                ]
            ],
            [
                'name' => 'tDeskripsi',
                'label' => 'Deskripsi',
                'placeholder' => 'Masukkan deskripsi',
                'type' => 'textarea',
                'col' => 'col-md-6',
                'required' => true,
            ],
        ];

        $this->grid =
            [
                [
                    'label' => 'Nomor Kwitansi',
                    'field' => 'tKwitansi',
                    'type' => 'text'
                ],
                [
                    'label' => 'Tanggal',
                    'field' => 'tTglBayar',
                    'type' => 'text'
                ],
                [
                    'label' => 'Asal Penerimaan',
                    'field' => 'tAsalPenerimaan',
                    'type' => 'text'
                ],
                [
                    'label' => 'Nominal',
                    'field' => 'tNilaiBayar',
                    'type' => 'angka'
                ],
                [
                    'label' => 'Deskripsi',
                    'field' => 'tDeskripsi',
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

        return view('bendahara.penerimaan', array_merge([
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
        $counterTerima = $this->getTerimaCounter($data['tTglBayar']);
        $counterKwitansi = $this->getKwitansiCounter($data['tTglBayar']);

        $data['tNoPenerimaan'] = str_pad($counterTerima, 5, '0', STR_PAD_LEFT) . '/PENERIMAAN/' . $data['tTglBayar'];
        $data['tKwitansi'] = 'NB-' . date('dmYHi') . '-' . rand(1000, 9999);
        return $data;
    }

    protected function afterSave($data)
    {
        $this->createJurnal('PENERIMAAN', $data['tCoa'], $data, 'M-TERIMA');
    }

    protected function beforeUpdate(array $data, $record): array
    {
        $this->hapusJurnal('M-TERIMA', $record->{$this->primaryKey});
        return $data;
    }

    protected function afterUpdate($data, $id)
    {
        $this->createJurnal('PENERIMAAN', $data['tCoa'], $data, 'M-TERIMA');
    }

    protected function beforeDelete($id)
    {
        $this->hapusJurnal('M-TERIMA', $id);
    }
}
