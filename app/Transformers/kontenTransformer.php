<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class kontenTransformer extends TransformerAbstract
{
   
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($dtl)


    
    {

          $respon["tipe"] = $dtl->tipe;

          $respon["judul"] = $dtl->judul;


          $respon["deskripsi"] = $dtl->deskripsi;

          $respon["link"] = $dtl->link;

          $respon["foto"] = $dtl->foto;

          return $respon;
    }
}
