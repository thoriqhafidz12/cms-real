<?php

namespace App\Models\Master\Coa;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['msoAkunKode', 'msoKelompokKode', 'msoJenisKode', 'msoKode', 'msoNama'])]
#[Table('ms_objek')]

class MasterObjek extends Model
{
    protected $primaryKey = 'msoId';
    public const CREATED_AT = 'msoCreatedAt';
    public const CREATED_BY = 'msoCreatedBy';
    public const UPDATED_AT = 'msoUpdatedAt';
    public const UPDATED_BY = 'msoUpdatedBy';
    public const DELETED_AT = 'msoDeletedAt';
    public const DELETED_BY = 'msoDeletedBy';
}
