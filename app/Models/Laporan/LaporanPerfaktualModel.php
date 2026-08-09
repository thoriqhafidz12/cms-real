<?php

namespace App\Models\Laporan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Fillable(['rpId', 'rpHeadId', 'rpTanggal', 'rpTerimaNominal', 'rpKeluarNominal', 'rpJenisTrans', 'rpKeterangan', 'rpUserId', 'rpCreatedBy', 'rpCreatedAt', 'rpUpdatedBy', 'rpUpdatedAt'])]
#[Table('rpt_perfaktual')]
class LaporanPerfaktualModel extends Model
{
    protected $table = 'rpt_perfaktual';
    protected $primaryKey = 'rpId';
    public $timestamps = false;
}
