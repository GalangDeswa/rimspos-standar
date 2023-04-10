<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class ListLaporanTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($dtl)
    {
        
        
        $respon["id"] = $dtl->id;
        $respon["nama"] = $dtl->nama;
        $respon["link"] = $dtl->link;

        return $respon;
    }
    
}