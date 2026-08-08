<?php

namespace App\Models\Kas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Fillable(['trNoTrans', 'trTanggal', 'trJenisTrans', 'trTerimaNominal', 'trTerimaUserId', 'trKeterangan', 'trCreatedBy', 'trUpdatedBy'])]
#[Table('tr_penerimaan')]
class PenerimaanModel extends Model
{
    protected $table = 'tr_penerimaan';
    protected $primaryKey = 'trId';
    public $timestamps = false;
}
