<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Fillable(['msJnsNama', 'msJnsCreatedBy', 'msJnsUpdatedBy'])]
#[Table('ms_jns_terima')]
class JenisPenerimaanModel extends Model
{
    protected $table = 'ms_jns_terima';
    protected $primaryKey = 'msJnsId';
    public $timestamps = false;
}
