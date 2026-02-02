<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class Meja extends Model
{
    protected $table = 'tb_meja';
    protected $primaryKey = 'meja_id';
    public $timestamps = false;


    protected $fillable = [
        'meja_nama',
        'meja_kapasitas',
        'meja_status',
        'created_at',
        'updated_at'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'order_meja_id', 'meja_id');
    }
}
