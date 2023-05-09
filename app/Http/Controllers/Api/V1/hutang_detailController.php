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
use Symfony\Component\HttpKernel\Exception\HttpException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class hutang_detailController extends Controller
{
    
    public function hutang_detail_all(Request $request){

    try {

        $validator = Validator::make($request->all(),

        array(

        'id_toko' => 'required|int',
        'id_hutang' => 'required|int'


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

        $hutang = hutang_detail::where('id_toko',$request->id_toko)->where('id_hutang', $request->id_hutang)

        // ->whereRaw('status = 1 AND (nama_produk LIKE "%'.$request->search.'%")

        // OR status = 1 AND (barcode LIKE "%'.$request->search.'%") ')

        // //->whereRaw('barcode LIKE "%'.$request->search.'%"')

        

        ->orderBy('tgl_bayar','ASC')

        ->paginate(10);

    if(count($hutang) > 0){

        $hutangrow = $hutang->getCollection();

        $success = true;

        $status_code = 200;

        $messages = 'Data Berhasil Ditampilkan!';

        $respone = fractal()

        ->collection($hutangrow, new hutang_detailTransformer, 'data')

        ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))

        ->paginateWith(new IlluminatePaginatorAdapter($hutang))

        ->addMeta([

        'catatan' => array(

        'status' => '1. lunas, 2. hutang ')

        ])

        ->toArray();

    return response()->json($respone, 200);

    }else{

        $hutangrow = $hutang->getCollection();

        $success = false;

        $status_code = 200;

        $messages = 'Tidak Ada Data Ditemukan!';

        $respone = fractal()

        ->collection($hutangrow, new hutang_detailTransformer, 'data')

        ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))

        ->paginateWith(new IlluminatePaginatorAdapter($hutang))

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


}
