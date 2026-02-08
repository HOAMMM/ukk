<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'tb_transaksi';
    protected $primaryKey = 'transaksi_id';
    public $timestamps = false; // karena bukan created_at / updated_at standar


    protected $fillable = [
        'transaksi_csname',
        'transaksi_orderid',
        'transaksi_amount',
        'transaksi_change',
        'transaksi_total',
        'transaksi_status',
        'transaksi_channel',
        'transaksi_code',
        'created_at'
    ];

    public function details()
    {
        return $this->hasMany(
            TransaksiDetail::class,
            'trans_code',
            'transaksi_code'
        );
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'transaksi_orderid', 'order_id');
    }
}
