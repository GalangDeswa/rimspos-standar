<?php

namespace App\Transformers;

use App\Models\hutang_detail;
use App\Models\Penjualan;
use League\Fractal\TransformerAbstract;
use App\Models\PenjualanDetail;

class hutangTransformer extends TransformerAbstract
{
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($dtl)
    {
        $respon["id"] = $dtl->id;

        $respon["id_pelanggan"] = $dtl->id_pelanggan;

        $respon["nama_pelanggan"] = $dtl->pelanggan->nama_pelanggan;

        $respon["id_toko"] = $dtl->id_toko;

        $respon["hutang"] = $dtl->hutang;
        
        $respon["tgl_hutang"] = $dtl->tgl_hutang;

      


         $respon["status"] = $dtl->status;


        $hutangpenjualan = Penjualan::where('id_hutang',$dtl->id)->get();

         if(count($hutangpenjualan) <= 0) { $respon["detail_hutang"]=[]; }

             foreach ($hutangpenjualan as $hutang) {
                
                $id_penjualan = $hutang->id;

                $respon["detail_hutang"][] = array(
                   

                "id_penjualan"  => $id_penjualan,   

                "id_toko" => $hutang->meja,

                "total_item" => $hutang->total_item,

                "tgl_penjualan" => $hutang->tgl_penjualan,

                "metode_bayar" => $hutang->metode_bayar,

                
                );

        

                 $detailpnjl = PenjualanDetail::where('id_penjualan',$id_penjualan)->get();

                 if(count($detailpnjl) <= 0) { $respon["detail_item"]=[]; }
                 
                 foreach ($detailpnjl as $dtlpenjl) {

                     $respon["detail_item"][]=array( "id_penjualan"=> $dtlpenjl->id_penjualan,

                     "id_produk" => $dtlpenjl->id_produk,

                     "id_kategori" => $dtlpenjl->id_kategori,

                     "id_jenis_stock" => $dtlpenjl->id_jenis_stock,

                     "nama_brg" => $dtlpenjl->nama_brg,

                     "harga_brg" => $dtlpenjl->harga_brg,

                     "qty" => $dtlpenjl->qty,

                     "diskon_brg" => $dtlpenjl->diskon_brg,

                     "total" => $dtlpenjl->total,

                     );



                     }


                      $riwayat = hutang_detail::where('id_hutang',$dtl->id)->get();

                      if(count($riwayat) <= 0) { $respon["riwayat_hutang"]=[]; }
                      
                      foreach ($riwayat as $detail) {
                          $respon["riwayat_hutang"][]=array(
                            
                            
                          "id"=>$detail->id,

                          "id_hutang" =>$detail->id_hutang,

                          "id_pelanggan" =>$detail->id_pelanggan,

                          "bayar" => $detail->bayar,

                          "sisa" => $detail->sisa,

                          "tgl_hutang" => $detail->tgl_hutang,

                          "tgl_bayar" => $detail->tgl_bayar,

                          "tgl_lunas" => $detail->tgl_lunas,

                          );



                          }


                     

         }

         

         return $respon;



    }
}
