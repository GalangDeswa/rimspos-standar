<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Transformers\ErorrTransformer;
use App\Models\hutang_detail;
use App\Transformers\hutang_detailTransformer;

use App\Transformers\SuccessTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

use Spatie\Fractalistic\ArraySerializer;

use App\Http\Controllers\Api\ArraySerializerV2;
use App\Models\Toko;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class hutang_detailController extends Controller
{
    
    public function hutang_detail_all(Request $request){

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

        $hutang = hutang_detail::where('id_toko',$request->id_toko)->get();

      
        

        

       

    if($hutang){

        

        $success = true;

        $status_code = 200;

        $messages = 'Data Berhasil Ditampilkan!';

        $respone = fractal()

        ->collection($hutang, new hutang_detailTransformer, 'data')

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

        ->collection($hutang, new hutang_detailTransformer, 'data')

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


    public function hutang_detail_local_to_database(Request $request){

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

            $id_hutang= $request->id_hutang;

            $id_pelanggan = $request->id_pelanggan;

            $bayar = $request->bayar;

            $sisa = $request->sisa;

            $tgl_hutang = $request->tgl_hutang;

            $tgl_bayar = $request->tgl_bayar;
            $tgl_lunas = $request->tgl_lunas;
            $aktif = $request->aktif;





            $checkid = hutang_detail::where('id_local',$request->id_local)
            ->where('id_toko',$request->id_toko)

            ->first();

        if($checkid == null){

            $data = new hutang_detail();
            $data->id_local = $id;

            $data->id_toko = $id_toko;
            $data->id_pelanggan = $id_pelanggan;
            $data->id_hutang = $id_hutang;
            $data->tgl_hutang = $tgl_hutang;
            $data->bayar = $bayar;
            $data->aktif = $aktif;
            $data->sisa = $sisa;
            $data->tgl_bayar = $tgl_bayar;
            $data->tgl_lunas = $tgl_lunas;


            if($data->save()){

            $messages = 'Data hutang detail Berhasil Ditambah';

            $respone = fractal()

            ->item($messages)

            ->transformWith(new SuccessTransformer)

            ->serializeWith(new ArraySerializer)

            ->toArray();

            return response()->json($respone, 200);

        }else{

            $messages = 'Tambah hutang detail tidak berhasil, silahkan coba kembali!';

            $status_code = 401;

            $response = fractal()

            ->item($messages)

            ->transformWith(new ErorrTransformer($status_code))

            ->serializeWith(new ArraySerializer())

            ->toArray();

            return response()->json($response, 401);

        }

        }else{

            $data = hutang_detail::where('id_local',$request->id_local)->where('id_toko',$request->id_toko)->first();

            $data->id_toko = $id_toko;
            $data->id_pelanggan = $id_pelanggan;
            $data->id_hutang = $id_hutang;
            $data->tgl_hutang = $tgl_hutang;
            $data->bayar = $bayar;
            $data->aktif = $aktif;
            $data->sisa = $sisa;
            $data->tgl_bayar = $tgl_bayar;
            $data->tgl_lunas = $tgl_lunas;

            if($data->save()){

            $messages = 'Data hutang detail Berhasil Diupdate';

            $respone = fractal()

            ->item($messages)

            ->transformWith(new SuccessTransformer)

            ->serializeWith(new ArraySerializer)

            ->toArray();

            return response()->json($respone, 200);

        }else{

            $messages = 'Update hutang detail tidak berhasil, silahkan coba kembali!';

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
