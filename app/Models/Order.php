<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'tb_order';
    protected $primaryKey = 'order_id';

    public $timestamps = false; // karena bukan created_at / updated_at standar


    protected $fillable = [
        'order_csname',
        'order_meja',
        'order_total',
        'order_qty',
        'order_change',
        'order_status',
        'created_at'
    ];

    // Relasi ke transaksi
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'order_id', 'transaksi_id');
    }

    public function meja()
    {
        return $this->belongsTo(Meja::class, 'order_meja_id', 'meja_id');
    }
}
