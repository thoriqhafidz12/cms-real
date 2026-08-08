<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Fillable(['msJnsKelNama', 'msJnsKelCreatedBy', 'msJnsKelUpdatedBy'])]
#[Table('ms_jns_keluar')]
class JenisPengeluaranModel extends Model
{
    protected $table = 'ms_jns_keluar';
    protected $primaryKey = 'msJnsKelId';
    public $timestamps = false;
}