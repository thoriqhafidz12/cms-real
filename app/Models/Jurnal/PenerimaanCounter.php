<?php

namespace App\Models\Jurnal;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
#[Fillable(['tTahun', 'tCounter'])]
#[Table('terima_counter')]

class PenerimaanCounter extends Model
{
    protected $primaryKey = 'tId';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
}
