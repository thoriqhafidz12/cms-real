<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Fillable(['trNoTrans', 'trTanggal', 'trJenisTrans', 'trNominal', 'trKeterangan', 'trCreatedBy', 'trUpdatedBy'])]
#[Table('trPenerimaan')]
class PenerimaanModel extends Model
{
    protected $table = 'trPenerimaan';
    protected $primaryKey = 'trId';
    public $timestamps = false;
}
