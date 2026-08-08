<?php

namespace App\Models\Kas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Fillable(['trKelNoTrans', 'trKelTanggal', 'trKelJenisTrans', 'trKelNominal','trKelUserId', 'trKelKeterangan', 'trKelCreatedBy', 'trKelUpdatedBy'])]
#[Table('tr_pengeluaran')]
class PengeluaranModel extends Model
{
    protected $table = 'tr_pengeluaran';
    protected $primaryKey = 'trKelId';
    public $timestamps = false;
}
