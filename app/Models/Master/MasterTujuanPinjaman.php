<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['mstId', 'mstKode', 'mstNama', 'mstKeterangan', 'mstCreatedBy', 'mstUpdatedBy'])]
#[Table('ms_tujuanpinjaman')]
class MasterTujuanPinjaman extends Model
{
    protected $primaryKey = 'mstId';
    public const CREATED_AT = 'mstCreatedAt';
    public const UPDATED_AT = 'mstUpdatedAt';
    public const DELETED_AT = null;
    public const CREATED_BY = 'mstCreatedBy';
    public const UPDATED_BY = 'mstUpdatedBy';
    public const DELETED_BY = null;
}
