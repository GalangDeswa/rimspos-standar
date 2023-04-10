<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

use App\Models\Appversion;
use App\Models\Toko;
use App\Models\User;


use App\Transformers\HomeTransformer;
use App\Transformers\ErorrTransformer;


use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Spatie\Fractalistic\ArraySerializer;
use App\Http\Controllers\Api\ArraySerializerV2;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class HomeApiController extends Controller
{
   
    public function loadtoko(Request $request){
        try {
            $validator = Validator::make($request->all(),
                             array(
                        'id_toko' => 'required|int'
                            ), array(
                        'id_toko.required' => 'id_toko wajib diisi!'
                            )
            );


            if ($validator->fails()) {
                $error_messages = $validator->messages()->all();

                // $messages = 'Missing Parameter Value!';
                $status_code = 422;
                $response =  fractal()
                    ->item($error_messages)
                    ->transformWith(new ErorrTransformer($status_code))
                    ->serializeWith(new ArraySerializer())
                    ->toArray();
                return response()->json($response, 422);

            }else{
                
              $toko = $request->id_toko;
              $respone =  fractal()
                    ->item($toko)
                    ->transformWith(new HomeTransformer)
                    ->serializeWith(new ArraySerializer)
                    ->toArray();
              return response()->json($respone, 200);
            }
            

        } catch (QueryException $ex) {
            throw new HttpException(401, "Gagal menampilkan data, coba lagi!");
        }

    }
    
}