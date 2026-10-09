<?php

namespace App\Models\Jurnal;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
#[Fillable(['tInvoice', 'tKwitansi', 'tNoPenerimaan', 'tNilaiBayar', 'tTglBayar', 'tAsalPenerimaan', 'tDeskripsi', 'tCoa', 'tSumber', 'tSumberId'])]
#[Table('tr_terima')]

class Penerimaan extends Model
{
    protected $primaryKey = 'tId';
    public const CREATED_BY = 'created_by';
    public const CREATED_AT = 'created_at';
    public const UPDATED_BY = 'updated_by';
    public const UPDATED_AT = 'updated_at';
}
