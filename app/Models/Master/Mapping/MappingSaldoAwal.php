<?php

namespace App\Models\Master\Mapping;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
#[Fillable(['mapKodeAsal', 'mapNamaAsal', 'mapKodeDebet', 'mapNamaDebet', 'mapKodeKredit', 'mapNamaKredit', 'mapStatusPenagihan'])]
#[Table('mapping_saldoawal')]

class MappingSaldoAwal extends Model
{
    protected $table = 'mapping_saldoawal';
    protected $primaryKey = 'mapId';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
}
