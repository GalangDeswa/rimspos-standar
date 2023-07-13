<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;



use DB;

use Validator;

use Image;



use App\Models\Toko;

use App\Models\Produk;

use App\Models\ProdukJenis;

use App\Models\ProdukKategori;
use App\Models\User;
use App\Transformers\HomeTransformer;



use App\Transformers\ProdukTransformer;

use App\Transformers\ErorrTransformer;

use App\Transformers\SuccessTransformer;
use App\Models\Konten;

use App\Transformers\kontenTransformer;




use League\Fractal\Pagination\IlluminatePaginatorAdapter;

use Spatie\Fractalistic\ArraySerializer;

use App\Http\Controllers\Api\ArraySerializerV2;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class kontenController extends Controller
{
    public function loadkonten_square(Request $request){

    try {


$konten = Konten::where('tipe',1 )->get();
    $success = true;

    $status_code = 200;

    $messages = 'Data Berhasil Ditampilkan!';

     $respone = fractal()

     ->collection($konten, new KontenTransformer, 'data')

     ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))

     // ->paginateWith(new IlluminatePaginatorAdapter($konten))

     ->toArray();

     return response()->json($respone, 200);



    // if(count($konten) > 0){

    //     $kontenrow = $konten->getCollection();

    //     $success = true;

    //     $status_code = 200;

    //     $messages = 'Data Berhasil Ditampilkan!';

    //     $respone = fractal()

    //     ->collection($kontenrow, new KontenTransformer, 'data')

    //     ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))

    //     // ->paginateWith(new IlluminatePaginatorAdapter($konten))


    // ->toArray();

    // return response()->json($respone, 200);

    // }









    } catch (QueryException $ex) {

    throw new HttpException(401, "Gagal menampilkan data, coba lagi!");

    }

    



    }

    public function loadkonten_banner(Request $request){

    try {

$x = 2;
    $konten = Konten::where('tipe',2 )->get();
    $success = true;

    $status_code = 200;

    $messages = 'Data Berhasil Ditampilkan!';

    $respone = fractal()

    ->collection($konten, new KontenTransformer, 'data')

    ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))

    // ->paginateWith(new IlluminatePaginatorAdapter($konten))

    ->toArray();

    return response()->json($respone, 200);



    // if(count($konten) > 0){

    // $kontenrow = $konten->getCollection();

    // $success = true;

    // $status_code = 200;

    // $messages = 'Data Berhasil Ditampilkan!';

    // $respone = fractal()

    // ->collection($kontenrow, new KontenTransformer, 'data')

    // ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))

    // // ->paginateWith(new IlluminatePaginatorAdapter($konten))


    // ->toArray();

    // return response()->json($respone, 200);

    // }









    } catch (QueryException $ex) {

    throw new HttpException(401, "Gagal menampilkan data, coba lagi!");

    }



    }





    public function tambahusermanual(Request $request){

    try {

    $validator = Validator::make($request->all(),

        array(

       // 'id_user' => 'required|int',

        //'id_toko' => 'required|int',

        'nama' => 'required',

        'email' => 'required',

        'password' => 'required',

        'role' => 'required',

        )

    );

    if ($validator->fails()) {

        $error_messages = $validator->messages()->all();

        // $messages = 'Missing Parameter Value!';

        $status_code = 422;

        $response = fractal()

        ->item($error_messages)

        ->transformWith(new ErorrTransformer($status_code))

        ->serializeWith(new ArraySerializer())

        ->toArray();

        return response()->json($response, 422);



    }else{

       // $cektoko = Toko::where('id',$request->id_toko)->first();


        if($request->role != "1" && $request->role != "2"){

            $messages = 'Role akses hanya terdiri dari (1. kasir. 2.admin)';

            $status_code = 401;

            $response = fractal()

            ->item($messages)

            ->transformWith(new ErorrTransformer($status_code))

            ->serializeWith(new ArraySerializer)

            ->toArray();

        return response()->json($response, 401);

    }

            $data = new User();

          //  $data->created_by = $request->id_user;

            $data->id_toko = $request->id_toko;

            $data->name = $request->nama;

            $data->email = $request->email;

            $data->hp = $request->hp;

            $data->password = bcrypt($request->password);

            $data->role = $request->role;


            $data->status = 1;





    if($data->save()){

            $messages = 'Data Berhasil Ditambah';

            $response = fractal()

            ->item($messages)

            ->transformWith(new SuccessTransformer)

            ->serializeWith(new ArraySerializer)

            ->toArray();

            return response()->json($response, 200);

    }else{

            $messages = 'Tambah User tidak berhasil, silahkan coba kembali!';

            $status_code = 401;

            $response = fractal()

            ->item($messages)

            ->transformWith(new ErorrTransformer($status_code))

            ->serializeWith(new ArraySerializer)

            ->toArray();

            return response()->json($response, 401);

    }

    



    



    }





    } catch (QueryException $ex) {

          throw new HttpException(401, "Gagal Menambah data, coba lagi!");

    }



    }

    public function loadtokoall(){
    $toko = Toko::all();
    $response  =[];
    $response ['messages'] = 'Berhasil';
    $response ['status'] = 200;
    $response ['data'] = $toko;
   

    

    return response()->json($response, 200);
    }
}
