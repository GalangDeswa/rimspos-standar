<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pelanggan extends Model
{
   protected $table = 'pelanggan';

    protected $fillable = [

    'id_toko',

    'nama_pelanggan',

    'no_hp',


    ];

     

    public function pelanggantoko()

    {

    return $this->belongsTo(Toko::class, 'id_toko', 'id');

    }

    public function penjualan()

    {

    return $this->hasMany(Penjualan::class, 'id_pelanggan', 'id');

    }
}
