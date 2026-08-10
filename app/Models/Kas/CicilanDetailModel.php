<?php

namespace App\Models\Kas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Fillable(['trcdHeadId', 'trcdTanggal', 'trcdNominal', 'trcdKeterangan', 'trcdCreatedAt', 'trcdCreatedBy', 'trcdUpdatedAt', 'trcdUpdatedBy'])]
#[Table('tr_cicilan_detail')]
class CicilanDetailModel extends Model
{
    protected $table = 'tr_cicilan_detail';
    protected $primaryKey = 'trcdId';
    public $timestamps = false;
}
