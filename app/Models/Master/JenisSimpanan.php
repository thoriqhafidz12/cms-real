<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table('ms_jnssimpanan')]
#[Fillable(['mjsKodeSimpanan', 'mjsNamaSimpanan', 'mjsTipeSimpanan', 'mjsNominalMinimal', 'mjsBisaDitarik', 'mjsAkunGl', 'mjsKeterangan', 'mjsStatus', 'mjsCreateBy', 'mjsUpdateBy', 'mjsDeleteBy'])]

class JenisSimpanan extends Model
{
    protected $primaryKey = 'mjsJnsSimpananId';
    public const CREATED_AT = 'mjsCreateTime';
    public const UPDATED_AT = 'mjsUpdateTime';
    public const DELETED_AT = 'mjsDeleteTime';
    public const CREATED_BY = 'mjsCreateBy';
    public const UPDATED_BY = 'mjsUpdateBy';
    public const DELETED_BY = 'mjsDeleteBy';
}
