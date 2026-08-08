<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Fillable(['msJnsKelNama', 'msJnsKelCreatedBy', 'msJnsKelUpdatedBy'])]
#[Table('msJnsKeluar')]
class JenisPengeluaranModel extends Model
{
    protected $table = 'msJnsKeluar';
    protected $primaryKey = 'msJnsKelId';
    public $timestamps = false;
}