<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'tb_kategori';
    protected $primaryKey = 'kategori_id';

    public $timestamps = false;

    protected $fillable = [
        'kategori_name',
        'kategori_code'
    ];
}
