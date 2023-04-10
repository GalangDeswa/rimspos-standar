<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class BebanKategoriTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($data)
    {
        $respon["id"] = $data->id;
        $respon["id_toko"] = $data->id_toko;
        $respon["kategori"] = $data->kategori;

        return $respon;
    }
}