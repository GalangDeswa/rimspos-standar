<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hutang extends Model
{
   protected $table = 'hutang';

   protected $fillable = [

   'id_pelanggan',

   'id_local',

   'id_toko',

   'hutang',


   'tgl_hutang',


    'status',



   ];

   public $timestamps = false;

    public function penjualan()

    {

        return $this->hasMany(Penjualan::class, 'id_hutang', 'id_local');

    }


    public function pelanggan()
    {
         return $this->belongsTo(pelanggan::class, 'id_pelanggan', 'id_local');
    }

     public function riwayat_hutang()
     {
     return $this->hasMany(hutang_detail::class, 'id_hutang', 'id_local');
     }
}
