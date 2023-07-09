<?php

namespace App\Transformers;

use App\Models\Penjualan;
use League\Fractal\TransformerAbstract;

class pelangganTransformer extends TransformerAbstract
{
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($dtl)
    {
        $respon["id"] = $dtl->id;
        $respon["id_local"] = $dtl->id_local;
        $respon["id_toko"] = $dtl->id_toko;
        $respon["nama_pelanggan"] = $dtl->nama_pelanggan;
        $respon["no_hp"] = $dtl->no_hp;
        $respon["aktif"] = $dtl->aktif;

        
        return $respon;
          



//  $detailpnjl = Penjualan::where('id_pelanggan',$dtl->id)->get();
 
//  if(count($detailpnjl) <= 0) {
//     $respon["riwayat_pembelian"] = [];
//  }

//  foreach ($detailpnjl as $dtlpenjl) {


//     $respon["riwayat_pembelian"][] = array(

//     "meja" => $dtlpenjl->meja,

//     "id_user" => $dtlpenjl->id_user,

//     "total_item" => $dtlpenjl->total_item,

//     "diskon_total" => $dtlpenjl->diskon_total,

//     "subtotal" => $dtlpenjl->subtotal,

//     "total" => $dtlpenjl->total,

//     "bayar" => $dtlpenjl->bayar,

//     "kembalian" => $dtlpenjl->kembalian,

//     "tgl_penjualan" => $dtlpenjl->tgl_penjualan,

//     "metode_bayar" => $dtlpenjl->metode_bayar,

//     "status" => $dtlpenjl->status,

//  );



//  }


      

       

    }
}
