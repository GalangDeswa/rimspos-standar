<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Validator;
use Image;

use App\Models\Toko;
use App\Models\User;
use App\Models\Produk;
use App\Models\ProdukJenis;
use App\Models\ProdukKategori;
use App\Models\Keranjang;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;

use App\Transformers\PenjualanTransformer;
use App\Transformers\ErorrTransformer;
use App\Transformers\SuccessTransformer;


use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Spatie\Fractalistic\ArraySerializer;
use App\Http\Controllers\Api\ArraySerializerV2;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



class PenjualanApiController extends Controller
{

    public function riwayat_penjualan(Request $request){
        try {
            $validator = Validator::make($request->all(),
                             array(
                        'id_toko' => 'required|int',
                        'id_user' => 'required|int',
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
                // $search = $request->search;
                $kasir = $request->kasir;
                $status = $request->status;
                
                if( $request->daritgl !='' &&  $request->sampaitgl != ''){
                    $daritgl = date("Y-m-d", strtotime($request->daritgl));
                    $sampaitgl = date("Y-m-d", strtotime($request->sampaitgl));
                }else{
                    $daritgl = $request->daritgl;
                    $sampaitgl = $request->sampaitgl;
                }

                $getkasir = User::where('id_toko',$request->id_toko)->where('id',$request->id_user)->first();
                if(!$getkasir){
                    $messages = 'Data User tidak ditemukan, silahkan coba kembali!';
                    $status_code = 422;
                    $response =  fractal()
                        ->item($messages)
                        ->transformWith(new ErorrTransformer($status_code))
                        ->serializeWith(new ArraySerializer())
                        ->toArray();
                    return response()->json($response, 422);
                }
                
                if($getkasir->role == 1){
                    $penjualan = Penjualan::where('id_toko',$request->id_toko)
                                ->where('id_user',$request->id_user)
                                ->when($status!='', function ($query) use ($status){
                                    return $query->where('status', '=', $status);
                                })
                                ->when($daritgl!='', function ($query) use ($daritgl){
                                    return $query->whereDate('tgl_penjualan', '>=', $daritgl);
                                })
                                ->when($sampaitgl!='', function ($query) use ($sampaitgl){
                                    return $query->whereDate('tgl_penjualan', '<=', $sampaitgl);
                                })
                                ->orderBy('tgl_penjualan','DESC')
                                ->paginate(10);
                }elseif($getkasir->role == 2){
                    $penjualan = Penjualan::where('id_toko',$request->id_toko)
                                ->when($status!='', function ($query) use ($status){
                                    return $query->where('status', '=', $status);
                                })
                                ->when($daritgl!='', function ($query) use ($daritgl){
                                    return $query->whereDate('tgl_penjualan', '>=', $daritgl);
                                })
                                ->when($sampaitgl!='', function ($query) use ($sampaitgl){
                                    return $query->whereDate('tgl_penjualan', '<=', $sampaitgl);
                                })
                                ->orderBy('tgl_penjualan','DESC')
                                ->paginate(10);
                }

                if(count($penjualan) > 0){ 
                    $penjualanrow = $penjualan->getCollection();   
                    $success = true;
                    $status_code = 200;
                    $messages = 'Data Berhasil Ditampilkan!';
                    $respone =  fractal()
                            ->collection($penjualanrow, new PenjualanTransformer, 'data')
                            ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))
                            ->paginateWith(new IlluminatePaginatorAdapter($penjualan))
                            ->addMeta(['catatan'=> array(
                                                            'status'=> '1 Selesai, 2 Hutang, 3 Bayar nanti, 4 transaksi batal (Reversal)',
                                                            'metode_bayar_dtl'=> '0. cash, 1. debit card, 2. kredit card, 3. bayar nanti (kasbon)',
                                                        ),
                                            
                                            ])
                            ->toArray();
                      return response()->json($respone, 200);
                }else{
                    $penjualanrow = $penjualan->getCollection();                    
                    $success = false;
                    $status_code = 200;
                    $messages = 'Tidak Ada Data Ditemukan!';
                    $respone =  fractal()
                            ->collection($penjualanrow, new PenjualanTransformer, 'data')
                            ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))
                            ->paginateWith(new IlluminatePaginatorAdapter($penjualan))
                            ->addMeta(['catatan'=> ''])
                            ->toArray();
                      return response()->json($respone, 200);
                }
              
            }
            

        } catch (QueryException $ex) {
            throw new HttpException(500, "Gagal Menampilkan data, coba lagi!");
        }
    }

    public function penjualan_detail(Request $request){
        try {
            $validator = Validator::make($request->all(),
                             array(
                        'id_toko' => 'required|int',
                        'id' => 'required|int',
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
                
                $penjualan = Penjualan::where('id_toko',$request->id_toko)
                                ->where('id',$request->id)
                                ->get();

                if(count($penjualan) > 0){  
                    $success = true;
                    $status_code = 200;
                    $messages = 'Data Berhasil Ditampilkan!'; 
                    $respone =  fractal()
                            ->collection($penjualan, new PenjualanTransformer, 'data')
                            ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))
                            ->addMeta(['catatan'=> array(
                                                            'status'=> '1 Selesai, 2 Hutang, 3 Bayar nanti, 4 transaksi batal (Reversal)',
                                                            'metode_bayar_dtl'=> '0. cash, 1. debit card, 2. kredit card, 3. bayar nanti (kasbon)',
                                                        ),
                                            
                                            ])
                            ->toArray();
                      return response()->json($respone, 200);
                }else{                   
                    $success = false;
                    $status_code = 200;
                    $messages = 'Tidak Ada Data Ditemukan!';
                    $respone =  fractal()
                            ->collection($penjualan, new PenjualanTransformer, 'data')
                            ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))
                            ->addMeta(['catatan'=> ''])
                            ->toArray();
                      return response()->json($respone, 200);
                }
              
            }
            

        } catch (QueryException $ex) {
            throw new HttpException(500, "Gagal Menampilkan data, coba lagi!");
        }
    }

    public function reversal_data(Request $request){
        try {
            $validator = Validator::make($request->all(),
                             array(
                        'id_toko' => 'required|int',
                        'id_user' => 'required|int',
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
                $getkasir = User::where('id_toko',$request->id_toko)->where('id',$request->id_user)->first();
                if($getkasir->status == 1){
                    $penjualan = Penjualan::where('id_toko',$request->id_toko)
                                ->where('id_user',$request->id_user)
                                ->whereDate('tgl_penjualan',date("Y-m-d"))
                                ->whereIn('status',[1, 2, 3])
                                // ->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))
                                ->orderBy('tgl_penjualan','DESC')
                                ->paginate(10);
                }elseif($getkasir->status == 2){
                    $penjualan = Penjualan::where('id_toko',$request->id_toko)
                                ->whereDate('tgl_penjualan',date("Y-m-d"))
                                ->whereIn('status',[1, 2, 3])
                                ->orderBy('tgl_penjualan','DESC')
                                ->paginate(10);
                }

                if(count($penjualan) > 0){ 
                    $penjualanrow = $penjualan->getCollection();   
                    $success = true;
                    $status_code = 200;
                    $messages = 'Data Berhasil Ditampilkan!'; 
                    $respone =  fractal()
                            ->collection($penjualanrow, new PenjualanTransformer, 'data')
                            ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))
                            ->paginateWith(new IlluminatePaginatorAdapter($penjualan))
                            ->addMeta(['catatan'=> array(
                                                            'status'=> '1 Selesai, 2 Hutang, 3 Bayar nanti, 4 transaksi batal (Reversal)',
                                                            'metode_bayar_dtl'=> '0. cash, 1. debit card, 2. kredit card, 3. bayar nanti (kasbon)',
                                                        ),
                                            
                                            ])
                            ->toArray();
                      return response()->json($respone, 200);
                }else{
                    $penjualanrow = $penjualan->getCollection();                    
                    $success = false;
                    $status_code = 200;
                    $messages = 'Tidak Ada Data Ditemukan!';
                    $respone =  fractal()
                            ->collection($penjualanrow, new PenjualanTransformer, 'data')
                            ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))
                            ->paginateWith(new IlluminatePaginatorAdapter($penjualan))
                            ->addMeta(['catatan'=> ''])
                            ->toArray();
                      return response()->json($respone, 200);
                }
              
            }
            

        } catch (QueryException $ex) {
            throw new HttpException(500, "Gagal Menampilkan data, coba lagi!");
        }
    }


    public function reversal_aksi(Request $request){
        try {
            $validator = Validator::make($request->all(),
                             array(
                        'id_toko' => 'required|int',
                        'id' => 'required|int',
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
                       
                $penjualan = Penjualan::where('id_toko',$request->id_toko)
                            ->where('id',$request->id)
                            ->whereIn('status',[1, 2, 3])
                            ->first();
                if($penjualan){
                    $detail = PenjualanDetail::where('id_penjualan',$penjualan->id)->get();
                    if(count($detail) > 0){
                        foreach ($detail as $dtl) {
                            ####///////----START---KEMBALIKAN STOK-------/////////////
                            $update_stok = Produk::where('id',$dtl->id_produk)
                                                    ->where('id_toko',$penjualan->id_toko)
                                                    ->first();
                            $update_stok->qty = $update_stok->qty + $dtl->qty;
                            $update_stok->save();                            
                        }

                        $penjualan->status = 4;
                        if($penjualan->save()){
                            $messages = 'Transaksi Berhasil Di Batalkan!';
                            $respone =  fractal()
                                ->item($messages)
                                ->transformWith(new SuccessTransformer) 
                                ->serializeWith(new ArraySerializer())     
                                ->toArray();
                            return response()->json($respone, 200);
                        }else{
                            $messages = 'Gagal Membatalkan Transaksi (Reversal), coba lagi -s!';
                            $status_code = 409;
                            $response =  fractal()
                                ->item($messages)
                                ->transformWith(new ErorrTransformer($status_code))
                                ->serializeWith(new ArraySerializer())
                                ->toArray();
                            return response()->json($respone, 500);    
                        }
                    }else{
                        $messages = 'Gagal Membatalkan Transaksi (Reversal), Detail Penjualan Kosong!';
                        $status_code = 409;
                        $response =  fractal()
                            ->item($messages)
                            ->transformWith(new ErorrTransformer($status_code))
                            ->serializeWith(new ArraySerializer())
                            ->toArray();
                        return response()->json($respone, 409); 
                    }
                }else{
                    $messages = 'Gagal Membatalkan Transaksi (Reversal), Data Penjualan Kosong!';
                    $status_code = 409;
                    $response =  fractal()
                        ->item($messages)
                        ->transformWith(new ErorrTransformer($status_code))
                        ->serializeWith(new ArraySerializer())
                        ->toArray();
                    return response()->json($respone, 409); 
                } 
            }
        } catch (QueryException $ex) {
            throw new HttpException(500, "Gagal Membatalkan Transaksi (Reversal), coba lagi!");
        }
    }
}