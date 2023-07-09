<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Model;



class Penjualan extends Model

{

    protected $table = 'penjualan';



    public function detail()

    {

        return $this->hasMany(PenjualanDetail::class, 'id_penjualan', 'id_local');

    }


    public function pelanggan()

    {

    return $this->belongsTo(pelanggan::class, 'id_pelanggan', 'id_local');

    }

}
