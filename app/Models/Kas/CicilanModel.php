<?php

namespace App\Models\Kas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Fillable(['trcNoTrans', 'trcUserId', 'trcTanggal', 'trcNominalPokok', 'trcPokokBayar', 'trcTerbayar', 'trcTenor','trcJatuhTempo', 'trcKeterangan', 'trcStatus', 'trcCreatedBy', 'trcUpdatedBy'])]
#[Table('tr_cicilan')]
class CicilanModel extends Model
{
    protected $table = 'tr_cicilan';
    protected $primaryKey = 'trcId';
    public $timestamps = false;
}