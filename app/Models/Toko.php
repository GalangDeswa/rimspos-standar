<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Toko extends Model
{
    use SoftDeletes;

    protected $table = 'toko';

    protected $fillable = [
        'id_user',
        'jenisusaha',
        'nama_toko',
        'alamat',
        'nohp' ,
        'email',
        'logo',
        'status',
        'tgl_aktif'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}
