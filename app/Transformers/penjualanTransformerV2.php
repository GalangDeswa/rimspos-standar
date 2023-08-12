<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class penjualanTransformerV2 extends TransformerAbstract
{
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($dtl)


    {
        $response['id'] = $dtl->id;
        $response["id_local"] = $dtl->id_local;
        $response['id_user'] = $dtl->id_user;
        $response['id_penjualan'] = $dtl->id_penjualan;
        $response['id_produk'] = $dtl->id_produk;
        $response['id_kategori'] = $dtl->id_kategori;
        $response['id_jenis_stock'] = $dtl->id_jenis_stock;
        $response['nama_brg'] = $dtl->nama_brg;
        $response['harga_brg'] = $dtl->harga_brg;
        $response['harga_modal'] = $dtl->harga_modal;
        $response['qty'] = $dtl->qty;
        $response['diskon_brg'] = $dtl->diskon_brg;
        $response['diskon_kasir'] = $dtl->diskon_kasir;
        $response['total'] = $dtl->total;
        $response['tgl'] = $dtl->tgl;
        $response["aktif"] = $dtl->aktif;

        return $response;
    }
}
