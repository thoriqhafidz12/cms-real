<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('ms_metodebayar')]
#[Fillable(['mmKode', 'mmNama', 'mmTipe', 'mmBank', 'mmNoRek', 'mmAtasNama', 'mmBiayaAdmin', 'mmAkunGl', 'mmKeterangan', 'mmStatus', 'mmCreateBy', 'mmUpdateBy', 'mmDeleteBy'])]
class MetodePembayaran extends Model
{
    protected $primaryKey = 'mmId';
    public const CREATED_AT = 'mmCreateTime';
    public const CREATED_BY = 'mmCreateBy';
    public const UPDATED_AT = 'mmUpdateTime';
    public const UPDATED_BY = 'mmUpdateBy';
    public const DELETED_AT = 'mmDeleteTime';
    public const DELETED_BY = 'mmDeleteBy';
}
