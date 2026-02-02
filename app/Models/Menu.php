<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'tb_menu';
    protected $primaryKey = 'menu_id';

    public $timestamps = false; // karena bukan created_at / updated_at standar

    protected $fillable = [
        'menu_name',
        'menu_price',
        'menu_desc',
        'menu_kategori',
        'menu_code',
        'menu_image',
        'created_at',
        'update_at'
    ];
}
