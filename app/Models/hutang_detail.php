<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hutang_detail extends Model
{
    protected $table = 'hutang_detail';

    protected $fillable = [

    'id_pelanggan',

    'id_toko',

    'hutang',

    'bayar',

    'tgl_hutang',

    'tgl_bayar',

    'tgl_lunas',

    'sisa',

    'status',



    ];

    public $timestamps = false;

      public function hutang()

      {

      return $this->hasMany(hutang::class, 'id_hutang', 'id');

      }
}
