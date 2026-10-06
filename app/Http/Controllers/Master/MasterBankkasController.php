<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\BaseController;
use App\Models\Master\MasterBankKas;
use App\Models\Master\SaldoAwal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MasterBankkasController extends BaseController
{
    public function __construct()
    {
        $this->model = MasterBankKas::class;
        $this->route = 'ms-bank-kas';
        $this->titlePage = 'Daftar Bank dan Kas';
        $this->primaryKey = 'msbkId';
        $this->table = 'ms_bankkas';
        $this->searchColumn = ['msbkKode', 'msbkObjekNm'];

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

        $this->rules = [
            'msbkKode' => 'required|unique:ms_bankkas,msbkKode',
            'msbkObjekKd' => 'required|unique:ms_bankkas,msbkObjekKd',
            'msbkTanggal' => 'required|date',
            'msbkStatus' => 'required',
        ];

        $this->form = [
            [
                'name' => 'msbkKode',
                'label' => 'Kode Bank/Kas',
                'placeholder' => 'Masukkan kode bank/kas',
                'type' => 'text',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'msbkObjekKd',
                'nameValue' => 'msbkObjekNm',
                'label' => 'Kode Objek',
                'placeholder' => '-- Cari dan pilih objek --',
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
                'name' => 'msbkNoRek',
                'label' => 'Nomor Rekening',
                'placeholder' => 'Isi Jika Bank',
                'type' => 'text',
                'col' => 'col-md-6',
                'required' => false,
            ],
            [
                'name' => 'msbkAtasNama',
                'label' => 'Atas Nama',
                'placeholder' => 'Isi Jika Bank',
                'type' => 'text',
                'col' => 'col-md-6',
                'required' => false,
            ],
            [
                'name' => 'msbkSaldoAwal',
                'label' => 'Saldo Awal',
                'placeholder' => 'Isi 0 jika kosong',
                'type' => 'angka',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'msbkTanggal',
                'label' => 'Tanggal',
                'placeholder' => 'Pilih tanggal',
                'type' => 'date',
                'col' => 'col-md-6',
                'required' => true,
            ],
            [
                'name' => 'msbkStatus',
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
                    'label' => 'Kode Bank/Kas',
                    'field' => 'msbkKode',
                    'type' => 'text'
                ],
                [
                    'label' => 'Nama Objek',
                    'field' => 'msbkObjekNm',
                    'type' => 'text'
                ],
                [
                    'label' => 'Kode Objek',
                    'field' => 'msbkObjekKd',
                    'type' => 'text'
                ],
                [
                    'label' => 'Saldo Awal',
                    'field' => 'msbkSaldoAwal',
                    'type' => 'angka'
                ],
                [
                    'label' => 'Saldo Sekarang',
                    'field' => 'saldoCoa',
                    'type' => 'angka'
                ],
                [
                    'label' => 'Status',
                    'field' => 'msbkStatus',
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
        $items->getCollection()->transform(function ($item) {
            $item->saldoCoa = $this->getSaldoCoa($item->msbkObjekKd, 'D');
            return $item;
        });

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

    public function store(Request $request): RedirectResponse
    {
        $modelClass = $this->model;

        $validated = $request->validate(
            $this->buildValidationRules()
        );

        try {
            DB::transaction(function () use ($modelClass, $validated) {
                $data = $validated;

                if (method_exists($this, 'beforeSave')) {
                    $data = $this->beforeSave($validated, null);
                }

                $data[$modelClass::CREATED_BY] = auth()->user()->name;
                $data[$modelClass::CREATED_AT] = now();

                $res = $modelClass::create($data);

                if (method_exists($this, 'afterSave')) {
                    $this->afterSave($res->toArray());
                }
            });

            return redirect()
                ->route($this->route . '.index')
                ->with('success', $this->titlePage . ' berhasil ditambahkan.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', $this->titlePage . ' gagal ditambahkan.');
        }
    }

    protected function afterSave(array $data): void
    {
        $saldoAwal = new SaldoAwal;
        $saldoAwal->tsKodeBank = $data['msbkObjekKd'];
        $saldoAwal->tsNamaBank = $data['msbkObjekNm'];
        $saldoAwal->tsSaldoAwal = $data['msbkSaldoAwal'];
        $saldoAwal->tsNoTrans = 'SALDOAWAL-' . now()->format('Ymd');
        $saldoAwal->tsTahun = $data['msbkTanggal'] ? date('Y', strtotime($data['msbkTanggal'])) : now()->format('Y');
        $saldoAwal->tsTanggal = $data['msbkTanggal'] ? date('Y-m-d', strtotime($data['msbkTanggal'])) : now()->format('Y-m-d');
        $saldoAwal->tsKodeBankKas = $data['msbkKode'];
        $saldoAwal->created_at = now();
        $saldoAwal->save();

        $this->createJurnal('SALDOAWAL', $saldoAwal->tsKodeBank, $saldoAwal->toArray(), 'SA-BANKKAS');
    }

    protected function beforeDelete($id)
    {
        $bankKas = $this->model::find($id);
        if ($bankKas) {
            $this->hapusSaldoawalBykode('SA-BANKKAS', $bankKas->msbkKode);
        }
    }
}
