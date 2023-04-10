<?php

namespace App\Transformers;

use App\Models\PenjualanDetail;
use App\Models\User;

use League\Fractal\TransformerAbstract;

use DB;

class PembayaranTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($dtl)
    {
        
        $getnmksr = User::where('id',$dtl->id_user)->first();
        if($getnmksr){
            $nama_user = $getnmksr->name;
        }else{
            $nama_user = "-";
        }

        

        $respon["id"] = $dtl->id;
        $respon["meja"] = $dtl->meja;
        $respon["id_toko"] = $dtl->id_toko;
        $respon["id_user"] = $dtl->id_user;
        $respon["nama_user"] = $nama_user;
        $respon["total_item"] = $dtl->total_item;
        $respon["diskon_total"] = $dtl->diskon_total;
        $respon["sub_total"] = $dtl->sub_total;
        $respon["total"] = $dtl->total;
        $respon["bayar"] = $dtl->bayar;
        $respon["kembalian"] = $dtl->kembalian;
        $respon["tgl_penjualan"] = date("d-m-Y / H:i",strtotime($dtl->tgl_penjualan));
        $respon["metode_bayar"] = $dtl->metode_bayar;
        $respon["status"] = $dtl->status;

        $detailpnjl = PenjualanDetail::where('id_penjualan',$dtl->id)->get();
        foreach ($detailpnjl as $dtlpenjl) {

            $respon["detail_item"][] = array(
                                "id_penjualan"  => $dtlpenjl->id_penjualan,
                                "id_produk"     => $dtlpenjl->id_produk,
                                "id_kategori"   => $dtlpenjl->id_kategori,
                                "nama_brg"      => $dtlpenjl->nama_brg,
                                "harga_brg"     => $dtlpenjl->harga_brg,
                                "qty"           => $dtlpenjl->qty,
                                "diskon_brg"    => $dtlpenjl->diskon_brg,
                                "total"         => $dtlpenjl->total,
                            ); 
            
        }
        
        return $respon;
    }
    
}