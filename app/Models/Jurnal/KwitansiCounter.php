<?php

namespace App\Models\Jurnal;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
#[Fillable(['kTahun', 'kBulan', 'kCounter'])]
#[Table('kwitansi_counter')]
class KwitansiCounter extends Model
{
    protected $primaryKey = 'kId';
    public const CREATED_AT = null;
    public const UPDATED_AT = null;
}
