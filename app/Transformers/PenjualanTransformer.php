<?php



namespace App\Transformers;



use App\Models\PenjualanDetail;

use App\Models\User;



use League\Fractal\TransformerAbstract;



use DB;



class PenjualanTransformer extends TransformerAbstract

{

    /**

     * A Fractal transformer.

     *

     * @return array

     */

    public function transform($dtl)

    {

        

        $getnmksr = User::where('id',$dtl->id_user)->first();

        if($getnmksr){

            $nama_user = $getnmksr->name;

        }else{

            $nama_user = "-";

        }



        



        $respon["id"] = $dtl->id;

        $respon["id_local"] = $dtl->id_local;

        $respon["meja"] = $dtl->meja;

        $respon["id_toko"] = $dtl->id_toko;

        $respon["id_user"] = $dtl->id_user;

        $respon["id_hutang"] = $dtl->id_hutang;

        $respon["id_pelanggan"] = $dtl->id_pelanggan;
        
        $respon["nama_pelanggan"] = $dtl->pelanggan->nama_pelanggan ?? '-';

        $respon["nama_user"] = $nama_user;

        $respon["total_item"] = $dtl->total_item;

        $respon["diskon_total"] = $dtl->diskon_total;

        $respon["sub_total"] = $dtl->sub_total;

        $respon["total"] = $dtl->total;

        $respon["bayar"] = $dtl->bayar;

        $respon["kembalian"] = $dtl->kembalian;

        $respon["tgl_penjualan"] = $dtl->tgl_penjualan;

        $respon["metode_bayar"] = $dtl->metode_bayar;

        $respon["status"] = $dtl->status;

        $respon["aktif"] = $dtl->aktif;
        
        $respon["diskon_kasir"] = $dtl->diskon_kasir;



        $detailpnjl = PenjualanDetail::where('id_penjualan',$dtl->id)->get();

        foreach ($detailpnjl as $dtlpenjl) {



            $respon["detail_item"][] = array(

                                "id_penjualan"  => $dtlpenjl->id_penjualan,

                                "id_produk"     => $dtlpenjl->id_produk,

                                "id_kategori"   => $dtlpenjl->id_kategori,

                                "id_jenis_stock" => $dtlpenjl->id_jenis_stock,

                                "nama_brg"      => $dtlpenjl->nama_brg,

                                "harga_brg"     => $dtlpenjl->harga_brg,

                                "harga_modal"   => $dtlpenjl->harga_modal,

                                "qty"           => $dtlpenjl->qty,

                                "diskon_brg"    => $dtlpenjl->diskon_brg,

                                "diskon_kasir" => $dtlpenjl->diskon_kasir,

                                "total"         => $dtlpenjl->total,

                            ); 

            

        }

        

        return $respon;

    }

    

}
