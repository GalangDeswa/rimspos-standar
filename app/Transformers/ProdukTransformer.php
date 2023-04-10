<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use Carbon\Carbon;

class ProdukTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($dtl)
    {

        if($dtl->image != ''){
            $image = url('/').'/'.$dtl->image;
        }else{
            $image = '';
        }
        $respon["id"] = $dtl->id;
        $respon["id_toko"] = $dtl->id_toko;
        $respon["id_user"] = $dtl->id_user;
        $respon["id_jenis"] = $dtl->id_jenis;
        $respon["nama_jenis"] = $dtl->jenisproduk->nama_jenis;
        $respon["id_kategori"] = $dtl->id_kategori;
        $respon["nama_produk"] = $dtl->nama_produk;
        $respon["deskripsi"] = $dtl->deskripsi;
        $respon["qty"] = $dtl->qty;
        $respon["harga"] = $dtl->harga;
        $respon["image"] = $image;
        $respon["status"] = $dtl->status;
        $respon["updated"] = Carbon::parse($dtl->updated_at)->diffForHumans();
        $respon["created_at"] = date("d-m-Y",strtotime($dtl->created_at));
        $respon["updated_at"] = date("d-m-Y",strtotime($dtl->updated_at));
        

        return $respon;
    }
    
}