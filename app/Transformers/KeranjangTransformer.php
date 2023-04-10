<?php

namespace App\Transformers;
use League\Fractal\TransformerAbstract;

use Carbon\Carbon;

class KeranjangTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($dtl)
    {

        $respon["id"] = $dtl->id;
        $respon["meja"] = $dtl->meja;
        $respon["id_toko"] = $dtl->id_toko;
        $respon["id_produk"] = $dtl->id_produk;

        if($dtl->produk){
            if($dtl->produk->image != ''){
                $image = url('/').'/'.$dtl->produk->image;
            }else{
                $image = '';
            }

             $respon["detail_produk"] = array(
                                // "id" => $dtl->produk->id,
                                "id_jenis" => $dtl->produk->id_jenis,
                                // "id_kategori" => $dtl->produk->id_kategori,
                                // "nama_produk" => $dtl->produk->nama_produk,
                                "deskripsi" => $dtl->produk->deskripsi,
                                // "qty" => $dtl->produk->qty,
                                // "harga" => $dtl->produk->harga,
                                "image" => $image,
                                // "updated" => Carbon::parse($dtl->produk->updated_at)->diffForHumans(),
                                // "created_at" => date("d-m-Y",strtotime($dtl->produk->created_at)),
                                // "updated_at" => date("d-m-Y",strtotime($dtl->produk->updated_at)),
                        );
            
        }else{
             $respon["detail_produk"] = [];
        }


        $respon["id_kategori"] = $dtl->id_kategori;
        $respon["id_user"] = $dtl->id_user;
        $respon["nama_user"] = ($dtl->kasir) ? $dtl->kasir->name : "-";

        $respon["nama_brg"] = $dtl->nama_brg;
        $respon["harga_brg"] = $dtl->harga_brg;
        $respon["diskon_brg"] = $dtl->diskon_brg;
        $respon["qty"] = $dtl->qty;
        $respon["total"] = $dtl->total;
        $respon["status"] = $dtl->status;
        $respon["updated"] = Carbon::parse($dtl->updated_at)->diffForHumans();
        $respon["created_at"] = date("d-m-Y H:i",strtotime($dtl->created_at));
        // $respon["updated_at"] = date("d-m-Y H:i",strtotime($dtl->updated_at));

        return $respon;
    }
    
}