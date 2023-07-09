<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class hutang_detailTransformer extends TransformerAbstract
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

        $respon["id_hutang"] = $dtl->id_hutang;

        $respon["id_toko"] = $dtl->id_toko;

        $respon["id_pelanggan"] = $dtl->id_pelanggan;

        //$respon["hutang"] = $dtl->hutang;

        $respon["bayar"] = $dtl->bayar;

        $respon["sisa"] = $dtl->sisa;

        $respon["tgl_hutang"] = $dtl->tgl_hutang;

        $respon["tgl_bayar"] = $dtl->tgl_bayar;

        $respon["tgl_lunas"] = $dtl->tgl_lunas;

        $respon["aktif"] = $dtl->aktif;


        return $respon;
    }
}
