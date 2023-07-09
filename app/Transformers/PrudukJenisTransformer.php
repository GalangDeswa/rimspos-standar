<?php



namespace App\Transformers;



use League\Fractal\TransformerAbstract;



class PrudukJenisTransformer extends TransformerAbstract

{

    /**

     * A Fractal transformer.

     *

     * @return array

     */

    public function transform($data)

    {

        $respon["id"] = $data->id;
        $respon["id_local"] = $data->id_local;

        $respon["id_toko"] = $data->id_toko;

        $respon["nama_jenis"] = $data->nama_jenis;
        $respon["aktif"] = $data->aktif;



        return $respon;

    }

}
