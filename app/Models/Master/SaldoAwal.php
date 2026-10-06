<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
#[Fillable(['tsKodeBank', 'tsNamaBank', 'tsSaldoAwal', 'tsNoTrans', 'tsTahun', 'tsTanggal', 'tsKodeBankKas'])]
#[Table('tr_saldoawal')]
class SaldoAwal extends Model
{
    protected $primaryKey = 'tsId';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
}
