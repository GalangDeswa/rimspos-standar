<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Model;

// use Illuminate\Database\Eloquent\SoftDeletes;



class Produk extends Model

{

    // use SoftDeletes;



    protected $table = 'produk';



    protected $fillable = [

        'id_toko',

        'id_local',

        'barcode',

        'id_user',

        'id_jenis',

        'id_kategori',

        'id_jenis_stock',

        'nama_produk',

        'deskripsi' ,

        'qty',

        'harga',

        'harga_modal',

        'diskon_barang',

        'image',

        'status'

    ];



    // /**

    //  * The attributes that should be mutated to dates.

    //  *

    //  * @var array

    //  */

    // protected $dates = ['deleted_at'];





    public function jenisproduk()

    {

        return $this->belongsTo(ProdukJenis::class, 'id_jenis', 'id_local');

    }



    public function kategoriproduk()

    {

        return $this->belongsTo(ProdukKategori::class, 'id_kategori', 'id');

    }

}
