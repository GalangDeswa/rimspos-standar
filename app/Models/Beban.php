<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beban extends Model
{
    protected $table = 'beban';
    public $timestamps = false;

    public function kategoribeban()
    {
        return $this->belongsTo(BebanKategori::class, 'id_ktr_beban', 'id');
    }

    public function kasir()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
