<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Validator;
use Image;

use App\Models\Toko;
use App\Models\Produk;
use App\Models\ProdukJenis;
use App\Models\ProdukKategori;
use App\Models\Keranjang;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;

use App\Transformers\PembayaranTransformer;
use App\Transformers\ErorrTransformer;
use App\Transformers\SuccessTransformer;


use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Spatie\Fractalistic\ArraySerializer;
use App\Http\Controllers\Api\ArraySerializerV2;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



class PembayaranApiController extends Controller
{

    public function pembayaran(Request $request){
        try {
            $validator = Validator::make($request->all(),
                             array(
                        'id_toko' => 'required|int',
                        // 'meja' => 'required|int',
                        'id_user' => 'required|int',
                        'bayar' => 'required|int',
                        // 'kembalian' => 'required',
                        // 'metode_bayar' => 'required|int',
                            )
            );
            if ($validator->fails()) {
                $error_messages = $validator->messages()->all();
                $status_code = 422;
                $response =  fractal()
                    ->item($error_messages)
                    ->transformWith(new ErorrTransformer($status_code))
                    ->serializeWith(new ArraySerializer())
                    ->toArray();
                return response()->json($response, 422);

            }else{
                
                $cektoko = Toko::where('id',$request->id_toko)->first();
                if($cektoko){
                    if($request->meja){
                        $meja = $request->meja;
                    }else{
                        $meja = 1;
                    }
                    $getkeranjang = Keranjang::where('id_toko',$request->id_toko)
                                    ->where('meja',$meja)
                                    ->where('id_user',$request->id_user)
                                    ->get();
                    

                    if(count($getkeranjang) > 0){
                        $sumkeranjang = Keranjang::select(DB::raw('SUM(qty) as total_item, SUM(qty*harga_brg) as sub_total, SUM(diskon_brg) as diskon_total, SUM(total) as total'))
                                    ->where('id_toko',$request->id_toko)
                                    ->where('meja',$meja)
                                    ->where('id_user',$request->id_user)
                                    ->first();

                        $addpenjualan = new Penjualan();
                        $addpenjualan->id_toko = $request->id_toko; 
                        $addpenjualan->meja = $meja; 
                        $addpenjualan->id_user = $request->id_user; 
                        $addpenjualan->bayar = $request->bayar; 
                        $addpenjualan->kembalian = $request->bayar-$sumkeranjang->total; 
                        $addpenjualan->tgl_penjualan = date("Y-m-d H:i:s"); 
                        
                        if($request->metode_bayar){
                            $addpenjualan->metode_bayar = $request->metode_bayar; 
                        }else{
                            $addpenjualan->metode_bayar = 0; 
                        }
                        $addpenjualan->status = 1; 

                        $addpenjualan->total_item = $sumkeranjang->total_item; 
                        $addpenjualan->diskon_total = $sumkeranjang->diskon_total; 
                        $addpenjualan->sub_total = $sumkeranjang->sub_total;
                        $addpenjualan->total = $sumkeranjang->total; 
                        $addpenjualan->save();


                        foreach ($getkeranjang as $keranjang) {
                            
                            $update_stok = Produk::where('id',$keranjang->id_produk)
                                                    ->where('id_toko',$keranjang->id_toko)
                                                    ->first();

                            ####///////----START---PENGURANGAN STOK-------/////////////
                            $update_stok->qty = $update_stok->qty - $keranjang->qty;
                            $update_stok->save();


                            ///////---CATAT Detail PENJUALAN
                            $dtlpenjualan = new PenjualanDetail();
                            $dtlpenjualan->id_penjualan     = $addpenjualan->id;
                            $dtlpenjualan->id_produk        = $keranjang->id_produk;
                            $dtlpenjualan->id_kategori      = $keranjang->id_kategori;
                            $dtlpenjualan->id_user          = $keranjang->id_user;
                            $dtlpenjualan->nama_brg         = $keranjang->nama_brg;
                            $dtlpenjualan->harga_brg        = $keranjang->harga_brg;
                            $dtlpenjualan->qty              = $keranjang->qty;
                            $dtlpenjualan->diskon_brg       = $keranjang->diskon_brg;
                            $dtlpenjualan->total            = $keranjang->total;
                            $dtlpenjualan->tgl              = date("Y-m-d H:i:s");
                            $dtlpenjualan->save();
                        }

                        

                        $getkeranjang = Keranjang::where('id_toko',$request->id_toko)
                                    ->where('meja',$meja)
                                    ->where('id_user',$request->id_user)
                                    ->delete();

                        $datapenjl = Penjualan::where('id_toko',$request->id_toko)
                                    ->where('meja',$meja)
                                    ->where('id_user',$request->id_user)
                                    ->get();

                        $success = true;
                        $status_code = 200;
                        $messages = 'Pembayaran Berhasil Dilakukan!';
                        $response =  fractal()
                                ->collection($datapenjl, new PembayaranTransformer, 'data')
                                ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))
                                ->addMeta(['catatan'=> array(
                                                            'status'=> '1 Selesai, 2 Hutang, 3 Bayar nanti, 4 transaksi batal (Reversal)',
                                                            'metode_bayar_dtl'=> '0. cash, 1. debit card, 2. kredit card, 3. bayar nanti (kasbon)',
                                                        ),
                                            
                                            ])
                                ->toArray();
                        return response()->json($response, 200);

                    }else{
                        $datapenjl = Penjualan::where('id_toko',$request->id_toko)
                                    ->where('meja',$meja)
                                    ->where('id_user',$request->id_user)
                                    ->get();
                        $success = false;
                        $status_code = 200;
                        $messages = 'Gagal Melakukan Pembayaran, Tidak ada Data di keranjang!';
                        $response =  fractal()
                                ->collection($datapenjl, new PembayaranTransformer, 'data')
                                ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))
                                ->addMeta(['catatan'=> ''])
                                ->toArray();
                        return response()->json($response, 200);

                    }
                    
                        
                }else{
                        $messages = 'Id Toko Tidak Ditemukan!';
                        $status_code = 401;
                        $response =  fractal()
                            ->item($messages)
                            ->transformWith(new ErorrTransformer($status_code))
                            ->serializeWith(new ArraySerializer())
                            ->toArray();
                        return response()->json($response, 401); 
                }
              
            }
            

        } catch (QueryException $ex) {
            throw new HttpException(500, "Gagal Melakukan Pembayaran, coba lagi!");
        }

    }

}