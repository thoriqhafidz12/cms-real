<?php

namespace App\Models\Jurnal;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
#[Fillable(['kNo', 'kNilai', 'kTgl', 'kKeterangan', 'kCoa', 'kPenagihan', 'kStatus', 'kTerpakai', 'kIdPengajuanBelanja', 'kSumber', 'kSumberId', 'created_by', 'updated_by'])]
#[Table('tr_keluar')]

class Pengeluaran extends Model
{
    protected $primaryKey = 'kId';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const CREATED_BY = 'created_by';
    public const UPDATED_BY = 'updated_by';
}
