<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Fillable(['msJnsNama', 'msJnsCreatedBy', 'msJnsUpdatedBy'])]
#[Table('msJnsTerima')]
class JenisPenerimaanModel extends Model
{
    protected $table = 'msJnsTerima';
    protected $primaryKey = 'msJnsId';
    public $timestamps = false;
}
