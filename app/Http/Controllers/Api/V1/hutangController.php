<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\hutang;
use App\Models\Toko;
use App\Transformers\hutangTransformer;
use App\Transformers\ErorrTransformer;

use App\Transformers\SuccessTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

use Spatie\Fractalistic\ArraySerializer;

use App\Http\Controllers\Api\ArraySerializerV2;
use App\Http\Controllers\Controller;
use App\Models\hutang_detail;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\Penjualan;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class hutangController extends Controller
{
    public function hutang_all(Request $request){

    try {

        $validator = Validator::make($request->all(),

        array(

        'id_toko' => 'required|int'


        )

    );

    if ($validator->fails()) {

        $error_messages = $validator->messages()->all();

        $status_code = 422;

        $response = fractal()

        ->item($error_messages)

        ->transformWith(new ErorrTransformer($status_code))

        ->serializeWith(new ArraySerializer())

        ->toArray();

        return response()->json($response, 422);



    }else{

        $hutang = Hutang::where('id_toko',$request->id_toko)

        // ->whereRaw('status = 1 AND (nama_produk LIKE "%'.$request->search.'%")

        // OR status = 1 AND (barcode LIKE "%'.$request->search.'%") ')

        // //->whereRaw('barcode LIKE "%'.$request->search.'%"')

        ->with('penjualan')

        ->orderBy('tgl_hutang','DESC')->get();

       

        if($hutang){

       

            $success = true;

            $status_code = 200;

            $messages = 'Data Berhasil Ditampilkan!';

            $respone = fractal()

            ->collection($hutang, new HutangTransformer, 'data')

            ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))

        

            ->addMeta([

            'catatan' => array(

            'status' => '1. lunas, 2. hutang ')

    ])

    ->toArray();

    return response()->json($respone, 200);

    }else{

     

        $success = false;

        $status_code = 200;

        $messages = 'Tidak Ada Data Ditemukan!';

        $respone = fractal()

        ->collection($hutang, new hutangTransformer, 'data')

        ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))

      

        ->addMeta([

        'catatan' => array(

        'status' => '1. lunas, 2. hutang ')

    ])

    ->toArray();

    return response()->json($respone, 200);

    }



    }

    } catch (QueryException $ex) {

    throw new HttpException(500, "Gagal menampilkan data, coba lagi!");

    }



    }



 public function tambah_hutang(Request $request){

     try {

        $validator = Validator::make($request->all(),

        array(

        'id_toko' => 'required|int',

        'id_pelanggan' => 'required|int',

        'hutang' => 'required',

        )

        );

     if ($validator->fails()) {

            $error_messages = $validator->messages()->all();

            $status_code = 422;

            $response = fractal()

            ->item($error_messages)

            ->transformWith(new ErorrTransformer($status_code))

            ->serializeWith(new ArraySerializer())

            ->toArray();

            return response()->json($response, 422);



     }else{



     $cektoko = Toko::where('id',$request->id_toko) ->first();

     if($cektoko){

     

            $addhutang = new hutang();

            $addhutang->id_toko = $request->id_toko;
            $addhutang->id_pelanggan = $request->id_pelanggan;
            $addhutang->hutang = $request->hutang;
            $addhutang->tgl_hutang = $request->tgl_hutang;
            $addhutang->status = 2;
           
     




     if($addhutang->save()){

        $messages = 'Data Berhasil Ditambah';

        $respone = fractal()

        ->item($messages)

        ->transformWith(new SuccessTransformer)

        ->serializeWith(new ArraySerializer)

        ->toArray();

        return response()->json($respone, 200);

     }else{

        $messages = 'Tambah hutang tidak berhasil, silahkan coba kembali!';

        $status_code = 401;

        $response = fractal()

        ->item($messages)

        ->transformWith(new ErorrTransformer($status_code))

        ->serializeWith(new ArraySerializer())

        ->toArray();

        return response()->json($response, 401);

     }





     }else{

         $messages = 'Id Toko Tidak Ditemukan!';

        $status_code = 401;

        $response = fractal()

        ->item($messages)

        ->transformWith(new ErorrTransformer($status_code))

        ->serializeWith(new ArraySerializer())

        ->toArray();

        return response()->json($response, 401);

     }



     }



     } catch (QueryException $ex) {

     throw new HttpException(500, "Gagal Menambah data, coba lagi!");

     }



     }


 public function bayar_hutang(Request $request){





     try {

        $validator = Validator::make($request->all(),

        array(

        'id_toko' => 'required|int',

        'id' => 'required|int',

       

        'bayar' => 'required',

        )

        );

     if ($validator->fails()) {

            $error_messages = $validator->messages()->all();

            $status_code = 422;

            $response = fractal()

            ->item($error_messages)

            ->transformWith(new ErorrTransformer($status_code))

            ->serializeWith(new ArraySerializer())

            ->toArray();

            return response()->json($response, 422);



     }else{



       $cektoko = Toko::where('id',$request->id_toko) ->first();

     if($cektoko){

            $cekpelanggan = hutang::where('id',$request->id) ->first();

            if($cekpelanggan){

              $xx = $cekpelanggan->hutang;

                   $bayar = new hutang_detail();

                   $bayar->id_hutang = $request->id;
                   $bayar->id_toko = $request->id_toko;
                   $bayar->id_pelanggan = $cekpelanggan->id_pelanggan;
                  // $bayar->hutang = $cekpelanggan->hutang;
                   $bayar->bayar = $request->bayar;
                   $bayar->sisa = $xx - $bayar->bayar;

                  
                   
                   $bayar->tgl_hutang = $cekpelanggan->tgl_hutang;
                   $bayar->tgl_bayar = date("Y-m-d H:i:s");

                    $cekpelanggan->hutang = $bayar->sisa;
                    //$cekpelanggan->save();

                   if($bayar->sisa == 0){
                            $bayar->tgl_lunas = date("Y-m-d H:i:s");
                            $cekpelanggan->status = 1;
                            $penjualan = Penjualan::where('id_hutang', $request->id)->first();
                            $penjualan->status = 1;
                            $penjualan->save();

                   }else{
                             $cekpelanggan->status = 2;
                   }

                   $cekpelanggan->save();

                   


              //      $cekpelanggan->bayar = $request->bayar;

              //      $cekpelanggan->sisa = $cekpelanggan->hutang - $cekpelanggan->bayar;

              //      $cekpelanggan->tgl_bayar = date("Y-m-d H:i:s");

              // //      if($cekpelanggan->sisa != 0 ){
                    
              // //      }

              //      if($cekpelanggan->sisa == 0){

              //           $cekpelanggan->tgl_lunas = date("Y-m-d H:i:s");

              //           $cekpelanggan->status = 1;

              //      }else{

              //       $cekpelanggan->status = 2;

              //      }

                   if($bayar->save()){

                        $messages = 'pembayaran hutang berhasil';

                        $respone = fractal()

                        ->item($messages)

                        ->transformWith(new SuccessTransformer)

                        ->serializeWith(new ArraySerializer)

                        ->toArray();

                        return response()->json($respone, 200);

                   }else{
                         $messages = 'bayar hutang tidak berhasil, silahkan coba kembali!';

                         $status_code = 401;

                         $response = fractal()

                         ->item($messages)

                         ->transformWith(new ErorrTransformer($status_code))

                         ->serializeWith(new ArraySerializer())

                         ->toArray();

                         return response()->json($response, 401);
                   }

            }else{

                 $messages = 'Pelanggan tidak di temukan, silahkan coba kembali!';

                 $status_code = 401;

                 $response = fractal()

                 ->item($messages)

                 ->transformWith(new ErorrTransformer($status_code))

                 ->serializeWith(new ArraySerializer())

                 ->toArray();

                 return response()->json($response, 401);
            }



     }else{

            $messages = 'Id Toko Tidak Ditemukan!';

            $status_code = 401;

            $response = fractal()

            ->item($messages)

            ->transformWith(new ErorrTransformer($status_code))

            ->serializeWith(new ArraySerializer())

            ->toArray();

            return response()->json($response, 401);

     }



     }



     } catch (QueryException $ex) {

           throw new HttpException(500, "Gagal Menambah data, coba lagi!");

     }



     }



public function hutang_local_to_database(Request $request){

            try {

                    $validator = Validator::make($request->all(),

                    array(



                    'id_toko' => 'required|int',



                    )

                    );

            if ($validator->fails()) {

                    $error_messages = $validator->messages()->all();

                    $status_code = 422;

                    $response = fractal()

                    ->item($error_messages)

                    ->transformWith(new ErorrTransformer($status_code))

                    ->serializeWith(new ArraySerializer())

                    ->toArray();

                    return response()->json($response, 422);



            }else{



                    $cektoko = Toko::where('id',$request->id_toko)->first();

                    if($cektoko){


                    $id = $request->id_local;

                    $id_toko = $request->id_toko;

                    $id_pelanggan= $request->id_pelanggan;

                    $hutang = $request->hutang;

                    $sisa_hutang = $request->sisa_hutang;

                    $tgl_hutang = $request->tgl_hutang;

                    $status = $request->status;

                    $aktif = $request->aktif;

                 



                    $checkid = hutang::where('id_local',$request->id_local)
                    ->where('id_toko',$request->id_toko)

                    ->first();

            if($checkid == null){

                    $data = new hutang();
                    $data->id_local = $id;

                    $data->id_toko = $id_toko;
                    $data->id_pelanggan = $id_pelanggan;
                    $data->hutang = $hutang;

                    $data->sisa_hutang = $sisa_hutang;

                    $data->tgl_hutang = $tgl_hutang;
                    $data->status = $status;
                    $data->aktif = $aktif;
                

            if($data->save()){

                    $messages = 'Data hutang Berhasil Ditambah';

                    $respone = fractal()

                    ->item($messages)

                    ->transformWith(new SuccessTransformer)

                    ->serializeWith(new ArraySerializer)

                    ->toArray();

                    return response()->json($respone, 200);

            }else{

                    $messages = 'Tambah hutang tidak berhasil, silahkan coba kembali!';

                    $status_code = 401;

                    $response = fractal()

                    ->item($messages)

                    ->transformWith(new ErorrTransformer($status_code))

                    ->serializeWith(new ArraySerializer())

                    ->toArray();

                    return response()->json($response, 401);

            }

            }else{

                    $data = hutang::where('id_local',$request->id_local)->where('id_toko',$request->id_toko)->first();

                    $data->id_toko = $id_toko;
                    $data->id_pelanggan = $id_pelanggan;
                    $data->hutang = $hutang;

                    $data->sisa_hutang = $sisa_hutang;
                    
                    $data->tgl_hutang = $tgl_hutang;
                    $data->status = $status;
                    $data->aktif = $aktif;

                    if($data->save()){

                    $messages = 'Data hutang Berhasil Diupdate';

                    $respone = fractal()

                    ->item($messages)

                    ->transformWith(new SuccessTransformer)

                    ->serializeWith(new ArraySerializer)

                    ->toArray();

                    return response()->json($respone, 200);

            }else{

                    $messages = 'Update hutang tidak berhasil, silahkan coba kembali!';

                    $status_code = 401;

                    $response = fractal()

                    ->item($messages)

                    ->transformWith(new ErorrTransformer($status_code))

                    ->serializeWith(new ArraySerializer())

                    ->toArray();

                    return response()->json($response, 401);

            }

            }



            }else{

                    $messages = 'Id Toko Tidak Ditemukan!';

                    $status_code = 401;

                    $response = fractal()

                    ->item($messages)

                    ->transformWith(new ErorrTransformer($status_code))

                    ->serializeWith(new ArraySerializer())

                    ->toArray();

                    return response()->json($response, 401);

            }



            }




            } catch (QueryException $ex) {

            throw new HttpException(500, "Gagal Menambah data, coba lagi!");

            }



            }
}
