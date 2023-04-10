<?php

namespace App\Transformers;

use App\Models\Konten;
use App\Models\Appversion;
use App\Models\Toko;
use App\Models\Penjualan;
use App\Models\Beban;

use League\Fractal\TransformerAbstract;

use DB;

class HomeTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($toko)
    {
        $cektoko = Toko::where('id',$toko)->first();

        if($cektoko){
            $idtoko = $cektoko->id_toko;
            $toko = $cektoko;
            $cekpendapatan = Penjualan::select(DB::Raw('SUM(total) as harga'))
                                        ->where('id_toko',$idtoko)
                                        ->where('created_at',date('Y-m-d'))
                                        ->where('status', 1)
                                        ->first();

            $cekbiaya = Beban::select(DB::Raw('SUM(jumlah) as jumlah'))
                                        ->where('id_toko',$idtoko)
                                        ->where('tgl',date('Y-m-d'))
                                        ->first();

            if($cekpendapatan->harga){
                $pendapatan = $cekpendapatan->harga;
            }else{
                $pendapatan = 0; 
            }

            
            if($cekbiaya->jumlah){
                $biaya = $cekbiaya->jumlah;
            }else{
                $biaya = 0; 
            }

            $respon["success"] = true;
            $respon["message"] = "Data Berhasil Ditampilkan!";

        }else{
            $toko = [];
            $pendapatan = 0; 
            $biaya = 0; 

            $respon["success"] = false;
            $respon["message"] = "Tidak Ada Data Ditemukan!";
        }


        $isikonten = array();
        $getknten = Konten::get();

        foreach ($getknten as $ds){
            $ds->photo = url('/').'/'.$ds->foto;
            unset($ds->foto);
            array_push($isikonten,$ds);
        }

        $versi = Appversion::get();
        
        $konten[] = array("toko" => $toko,
                         "konten" => $isikonten, 
                        "pendapatan" => $pendapatan, 
                        "beban" => $biaya, 
                        "version" => $versi);

        
        $respon["data"] = $konten;

        return $respon;
    }
}