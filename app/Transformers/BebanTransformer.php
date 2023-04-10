<?php

namespace App\Transformers;

use App\Http\Models\Beban;

use League\Fractal\TransformerAbstract;

class BebanTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($dtl)
    {
        $respon["id"] = $dtl->id;
        $respon["id_toko"] = $dtl->id_toko;
        $respon["id_user"] = $dtl->id_user;
        $respon["nama"] = $dtl->nama;
        $respon["keterangan"] = $dtl->keterangan;
        $respon["tgl"] = date('d-m-Y',strtotime($dtl->tgl));
        $respon["jumlah"] = $dtl->jumlah;
        $respon["id_ktr_beban"] = $dtl->id_ktr_beban;
        $respon["nama_ktr_beban"] = $dtl->kategoribeban->kategori;

        return $respon;
    }
    
}