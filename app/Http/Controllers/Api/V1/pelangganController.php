<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;



use DB;

use Illuminate\Support\Facades\Validator;

use Image;



use App\Models\Toko;

use App\Models\Produk;

use App\Models\ProdukJenis;

use App\Models\ProdukKategori;



use App\Transformers\ProdukTransformer;

use App\Transformers\ErorrTransformer;

use App\Transformers\SuccessTransformer;





use League\Fractal\Pagination\IlluminatePaginatorAdapter;

use Spatie\Fractalistic\ArraySerializer;

use App\Http\Controllers\Api\ArraySerializerV2;
use App\Models\pelanggan;
use App\Transformers\pelangganTransformer;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class pelangganController extends Controller
{
    public function pelanggan_all(Request $request){

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

              $pelanggan = pelanggan::where('id_toko',$request->id_toko)

            ->with('penjualan')

            ->orderBy('nama_pelanggan','ASC')->get();

           

        if($pelanggan){

          

            $success = true;

            $status_code = 200;

            $messages = 'Data Berhasil Ditampilkan!';

            $respone = fractal()

            ->collection($pelanggan, new pelangganTransformer, 'data')

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

        ->collection($pelanggan, new pelangganTransformer, 'data')

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


     public function tambah_pelanggan(Request $request){

        try {

        $validator = Validator::make($request->all(),

        array(

        'id_toko' => 'required|int',

       // 'id_penjualan' => 'required|int',

        'nama_pelanggan' => 'required',

        'no_hp' => 'required',

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



        $cekpelanggan = Toko::where('id',$request->id_toko) ->first();

         if($cekpelanggan){

            $cekhp = pelanggan::where('no_hp', $request->no_hp)->first();

            if($cekhp){
                 $messages = 'Nomor hp pelanggan Sudah Terdaftar Pada Sistem';

                 $status_code = 401;

                 $response = fractal()

                 ->item($messages)

                 ->transformWith(new ErorrTransformer($status_code))

                 ->serializeWith(new ArraySerializer)

                 ->toArray();

                 return response()->json($response, 401);

            }else{
                
                 $addktr = new pelanggan();

                 $addktr->id_toko = $request->id_toko;

             

                 $addktr->nama_pelanggan = $request->nama_pelanggan;
                 $addktr->no_hp = $request->no_hp;
                 // $addktr->status = $request->status;
            }

        






         if($addktr->save()){

         $messages = 'Data Berhasil Ditambah';

         $respone = fractal()

         ->item($messages)

         ->transformWith(new SuccessTransformer)

         ->serializeWith(new ArraySerializer)

         ->toArray();

         return response()->json($respone, 200);

         }else{

         $messages = 'Tambah pelanggan tidak berhasil, silahkan coba kembali!';

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

//-----------------------------------------------------------------------------------------
        public function edit_pelanggan(Request $request){

        try {

        $validator = Validator::make($request->all(),

        array(

        'id' => 'required',

        'id_toko' => 'required',

        'nama_pelanggan' => 'required',
         

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



        $cektoko = Toko::where('id',$request->id_toko)->first();

        if($cektoko){



        $edit = pelanggan::where('id',$request->id)->where('id_toko',$request->id_toko)->first();

        if($edit){

        $edit->nama_pelanggan = $request->nama_pelanggan;
        $edit->no_hp = $request->no_hp;

        if($edit->save()){

        $messages = 'Data kategori beban Berhasil Diperbaharui';

        $respone = fractal()

        ->item($messages)

        ->transformWith(new SuccessTransformer)

        ->serializeWith(new ArraySerializer)

        ->toArray();

        return response()->json($respone, 200);

        }else{

        $messages = 'Edit pelanggan tidak berhasil, silahkan coba kembali!';

        $status_code = 401;

        $response = fractal()

        ->item($messages)

        ->transformWith(new ErorrTransformer($status_code))

        ->serializeWith(new ArraySerializer())

        ->toArray();

        return response()->json($response, 401);

        }

        }else{

        $messages = 'Id pelanggan / Id Toko tidak sesuai, silahkan coba kembali!';

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

        throw new HttpException(401, "Edit kategori beban tidak berhasil, silahkan coba kembali!");

        }



        }



//---------------------------------------------------------------------
        public function hapus_pelanggan(Request $request){

        try {

        $validator = Validator::make($request->all(),

        array(

        'id' => 'required',

        'id_toko' => 'required'

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



        $cektoko = Toko::where('id',$request->id_toko)->first();

        if($cektoko){



        $delete = pelanggan::where('id',$request->id)->where('id_toko',$request->id_toko)->first();

        if($delete){

        if($delete->delete()){

        $messages = 'Data pelanggan Berhasil Dihapus';

        $respone = fractal()

        ->item($messages)

        ->transformWith(new SuccessTransformer)

        ->serializeWith(new ArraySerializer)

        ->toArray();

        return response()->json($respone, 200);

        }else{

        $messages = 'Hapus pelanggan tidak berhasil, silahkan coba kembali!';

        $status_code = 401;

        $response = fractal()

        ->item($messages)

        ->transformWith(new ErorrTransformer($status_code))

        ->serializeWith(new ArraySerializer())

        ->toArray();

        return response()->json($response, 401);

        }

        }else{

        $messages = 'Id kategori beban / Id Toko tidak sesuai, silahkan coba kembali!';

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

        throw new HttpException(401, "Hapus kategori beban tidak berhasil, silahkan coba kembali!");

        }



        }




     public function pelanggan_local_to_database(Request $request){

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

            $nama_pelanggan= $request->nama_pelanggan;

            $no_hp = $request->no_hp;

         
            $aktif = $request->aktif;





            $checkid = pelanggan::where('id_local',$request->id_local)
            ->where('id_toko',$request->id_toko)

            ->first();

        if($checkid == null){

            $data = new pelanggan();
            $data->id_local = $id;

            $data->id_toko = $id_toko;
            $data->nama_pelanggan = $nama_pelanggan;
            $data->no_hp = $no_hp;
            $data->aktif = $aktif;
            

        if($data->save()){

            $messages = 'Data pelanggan Berhasil Ditambah';

            $respone = fractal()

            ->item($messages)

            ->transformWith(new SuccessTransformer)

            ->serializeWith(new ArraySerializer)

            ->toArray();

            return response()->json($respone, 200);

        }else{

            $messages = 'Tambah pelanggan tidak berhasil, silahkan coba kembali!';

            $status_code = 401;

            $response = fractal()

            ->item($messages)

            ->transformWith(new ErorrTransformer($status_code))

            ->serializeWith(new ArraySerializer())

            ->toArray();

            return response()->json($response, 401);

        }

        }else{

            $data = pelanggan::where('id_local',$request->id_local)->where('id_toko',$request->id_toko)->first();

           $data->id_toko = $id_toko;
           $data->nama_pelanggan = $nama_pelanggan;
           $data->no_hp = $no_hp;
           $data->aktif = $aktif;

            if($data->save()){

            $messages = 'Data pelanggan Berhasil Diupdate';

            $respone = fractal()

            ->item($messages)

            ->transformWith(new SuccessTransformer)

            ->serializeWith(new ArraySerializer)

            ->toArray();

            return response()->json($respone, 200);

        }else{

            $messages = 'Update pelanggan tidak berhasil, silahkan coba kembali!';

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
