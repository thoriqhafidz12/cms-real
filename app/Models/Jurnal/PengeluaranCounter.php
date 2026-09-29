<?php

namespace App\Models\Jurnal;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
#[Fillable(['kTahun', 'kCounter'])]
#[Table('keluar_counter')]

class PengeluaranCounter extends Model
{
    protected $primaryKey = 'kId';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
}