<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['msjId', 'msjKode', 'msjNama', 'msjKeterangan', 'msjCreatedBy', 'msjUpdatedBy'])]
#[Table('ms_jaminan')]
class MasterJaminan extends Model
{
    protected $primaryKey = 'msjId';
    public const CREATED_AT = 'msjCreatedAt';
    public const UPDATED_AT = 'msjUpdatedAt';
    public const DELETED_AT = null;
    public const CREATED_BY = 'msjCreatedBy';
    public const UPDATED_BY = 'msjUpdatedBy';
    public const DELETED_BY = null;
}
