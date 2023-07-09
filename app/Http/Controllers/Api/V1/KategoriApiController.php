<?php



namespace App\Http\Controllers\Api\V1;



use Illuminate\Http\Request;

use App\Http\Controllers\Controller;



use DB;

use Validator;



use App\Models\Toko;

use App\Models\ProdukJenis;

use App\Models\BebanKategori;





use App\Transformers\ErorrTransformer;

use App\Transformers\SuccessTransformer;



use App\Transformers\PrudukJenisTransformer;

use App\Transformers\BebanKategoriTransformer;





use League\Fractal\Pagination\IlluminatePaginatorAdapter;

use Spatie\Fractalistic\ArraySerializer;

use App\Http\Controllers\Api\ArraySerializerV2;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;







class KategoriApiController extends Controller

{

   

////////JENIS-----------------------------

    public function datajenis(Request $request){

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

                $status_code = 422;

                $response =  fractal()

                    ->item($error_messages)

                    ->transformWith(new ErorrTransformer($status_code))

                    ->serializeWith(new ArraySerializer())

                    ->toArray();

                return response()->json($response, 422);



            }else{

              $ktr = ProdukJenis::where('id_toko',$request->id_toko)->get();

              if($ktr){

                                    

                    $success = true;

                    $status_code = 200;

                    $messages = 'Data Berhasil Ditampilkan!';

                    $response =  fractal()

                            ->collection($ktr, new PrudukJenisTransformer(), 'data')

                            ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))

                            ->addMeta([

                                'catatan' => '' 

                            ])

                            ->toArray();

                      return response()->json($response, 200);

                }else{

                    

                

                    $success = false;

                    $status_code = 200;

                    $messages = 'Tidak Ada Data Ditemukan!';

                    $response =  fractal()

                        ->collection($ktr, new PrudukJenisTransformer(), 'data')

                        ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))


                        ->addMeta([

                                'catatan' => '' 

                            ])

                        ->toArray();

                    return response()->json($response, 200); 

                }

            }

        } catch (QueryException $ex) {

            throw new HttpException(401, "Gagal menampilkan data, coba lagi!");

        }



    }



    public function tambah_jenis(Request $request){

        try {

            $validator = Validator::make($request->all(),

                             array(

                        'id_toko' => 'required',

                        'nama_jenis' => 'required',

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

                

              $cektoko = Toko::where('id',$request->id_toko)->first();

              if($cektoko){

                    

                    $addktr = new ProdukJenis();

                    $addktr->id_toko = $request->id_toko;

                    $addktr->nama_jenis = $request->nama_jenis;

                    



                    if($addktr->save()){

                        $messages = 'Data Berhasil Ditambah';

                        $respone =  fractal()

                            ->item($messages)

                            ->transformWith(new SuccessTransformer)

                            ->serializeWith(new ArraySerializer)        

                            ->toArray();

                        return response()->json($respone, 200); 

                    }else{

                        $messages = 'Tambah Jenis tidak berhasil, silahkan coba kembali!';

                        $status_code = 401;

                        $response =  fractal()

                            ->item($messages)

                            ->transformWith(new ErorrTransformer($status_code))

                            ->serializeWith(new ArraySerializer())

                            ->toArray();

                        return response()->json($respone, 401);

                    }



                    

              }else{

                    $messages = 'Id Toko Tidak Ditemukan!';

                    $status_code = 401;

                    $response =  fractal()

                        ->item($messages)

                        ->transformWith(new ErorrTransformer($status_code))

                        ->serializeWith(new ArraySerializer())

                        ->toArray();

                    return response()->json($respone, 401); 

              }

              

            }

            



        } catch (QueryException $ex) {

            throw new HttpException(401, "Tambah Jenis tidak berhasil, silahkan coba kembali!");

        }



    }



    public function edit_jenis(Request $request){

        try {

            $validator = Validator::make($request->all(),

                             array(

                        'id' => 'required',

                        'id_toko' => 'required',

                        'nama_jenis' => 'required',

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

                

              $cektoko = Toko::where('id',$request->id_toko)->first();

              if($cektoko){

                    

                    $addktr = ProdukJenis::where('id',$request->id)->where('id_toko',$request->id_toko)->first();

                    if($addktr){

                        $addktr->nama_jenis = $request->nama_jenis;

                        if($addktr->save()){

                            $messages = 'Data Jenis Berhasil Diperbaharui';

                            $respone =  fractal()

                                ->item($messages)

                                ->transformWith(new SuccessTransformer)

                                ->serializeWith(new ArraySerializer)        

                                ->toArray();

                            return response()->json($respone, 200); 

                        }else{

                            $messages = 'Edit Jenis tidak berhasil, silahkan coba kembali!';

                            $status_code = 401;

                            $response =  fractal()

                                ->item($messages)

                                ->transformWith(new ErorrTransformer($status_code))

                                ->serializeWith(new ArraySerializer())

                                ->toArray();

                            return response()->json($respone, 401);

                        }

                    }else{

                        $messages = 'Id Jenis / Id Toko tidak sesuai, silahkan coba kembali!';

                        $status_code = 401;

                        $response =  fractal()

                            ->item($messages)

                            ->transformWith(new ErorrTransformer($status_code))

                            ->serializeWith(new ArraySerializer())

                            ->toArray();

                        return response()->json($respone, 401);

                    }

                    



                    

              }else{

                    $messages = 'Id Toko Tidak Ditemukan!';

                    $status_code = 401;

                    $response =  fractal()

                        ->item($messages)

                        ->transformWith(new ErorrTransformer($status_code))

                        ->serializeWith(new ArraySerializer())

                        ->toArray();

                    return response()->json($respone, 401); 

              }

              

            }



        } catch (QueryException $ex) {

            throw new HttpException(401, "Edit Jenis tidak berhasil, silahkan coba kembali!");

        }



    }



    public function hapus_jenis(Request $request){

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

                $response =  fractal()

                    ->item($error_messages)

                    ->transformWith(new ErorrTransformer($status_code))

                    ->serializeWith(new ArraySerializer())

                    ->toArray();

                return response()->json($response, 422);



            }else{

                

              $cektoko = Toko::where('id',$request->id_toko)->first();

              if($cektoko){

                    

                    $addktr = ProdukJenis::where('id',$request->id)->where('id_toko',$request->id_toko)->first();

                    if($addktr){

                        if($addktr->delete()){

                            $messages = 'Data Jenis Berhasil Dihapus';

                            $respone =  fractal()

                                ->item($messages)

                                ->transformWith(new SuccessTransformer)

                                ->serializeWith(new ArraySerializer)        

                                ->toArray();

                            return response()->json($respone, 200); 

                        }else{

                            $messages = 'Hapus Jenis tidak berhasil, silahkan coba kembali!';

                            $status_code = 401;

                            $response =  fractal()

                                ->item($messages)

                                ->transformWith(new ErorrTransformer($status_code))

                                ->serializeWith(new ArraySerializer())

                                ->toArray();

                            return response()->json($respone, 401);

                        }

                    }else{

                        $messages = 'Id Jenis / Id Toko tidak sesuai, silahkan coba kembali!';

                        $status_code = 401;

                        $response =  fractal()

                            ->item($messages)

                            ->transformWith(new ErorrTransformer($status_code))

                            ->serializeWith(new ArraySerializer())

                            ->toArray();

                        return response()->json($respone, 401);

                    }

                    

              }else{

                    $messages = 'Id Toko Tidak Ditemukan!';

                    $status_code = 401;

                    $response =  fractal()

                        ->item($messages)

                        ->transformWith(new ErorrTransformer($status_code))

                        ->serializeWith(new ArraySerializer())

                        ->toArray();

                    return response()->json($respone, 401); 

              }

              

            }



        } catch (QueryException $ex) {

            throw new HttpException(401, "Hapus Jenis tidak berhasil, silahkan coba kembali!");

        }



    }



////////KATEGORI BEBAN-----------------------------

    public function datakategoribeban(Request $request){

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

                $status_code = 422;

                $response =  fractal()

                    ->item($error_messages)

                    ->transformWith(new ErorrTransformer($status_code))

                    ->serializeWith(new ArraySerializer())

                    ->toArray();

                return response()->json($response, 422);



            }else{

              $ktr = BebanKategori::where('id_toko',$request->id_toko)->get();

              if($ktr){

                          

                    $success = true;

                    $status_code = 200;

                    $messages = 'Data Berhasil Ditampilkan!';

                    $response =  fractal()

                            ->collection($ktr, new BebanKategoriTransformer(), 'data')

                            ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))


                            ->addMeta([

                                'catatan' => '' 

                            ])

                            ->toArray();

                      return response()->json($response, 200);

                }else{

                    

              

                    $success = false;

                    $status_code = 200;

                    $messages = 'Tidak Ada Data Ditemukan!';

                    $response =  fractal()

                        ->collection($ktr, new BebanKategoriTransformer(), 'data')

                        ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))


                        ->addMeta([

                                'catatan' => '' 

                            ])

                        ->toArray();

                    return response()->json($response, 200); 

                }

            }

        } catch (QueryException $ex) {

            throw new HttpException(401, "Gagal menampilkan data, coba lagi!");

        }



    }



    public function tambah_kategoribeban(Request $request){

        try {

            $validator = Validator::make($request->all(),

                             array(

                        'id_toko' => 'required',

                        'kategori' => 'required',

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

                

              $cektoko = Toko::where('id',$request->id_toko)->first();

              if($cektoko){

                    

                    $addktr = new BebanKategori();

                    $addktr->id_toko = $request->id_toko;

                    $addktr->kategori = $request->kategori;

                    



                    if($addktr->save()){

                        $messages = 'Data Berhasil Ditambah';

                        $respone =  fractal()

                            ->item($messages)

                            ->transformWith(new SuccessTransformer)

                            ->serializeWith(new ArraySerializer)        

                            ->toArray();

                        return response()->json($respone, 200); 

                    }else{

                        $messages = 'Tambah kategori beban tidak berhasil, silahkan coba kembali!';

                        $status_code = 401;

                        $response =  fractal()

                            ->item($messages)

                            ->transformWith(new ErorrTransformer($status_code))

                            ->serializeWith(new ArraySerializer())

                            ->toArray();

                        return response()->json($respone, 401);

                    }



                    

              }else{

                    $messages = 'Id Toko Tidak Ditemukan!';

                    $status_code = 401;

                    $response =  fractal()

                        ->item($messages)

                        ->transformWith(new ErorrTransformer($status_code))

                        ->serializeWith(new ArraySerializer())

                        ->toArray();

                    return response()->json($respone, 401); 

              }

              

            }

            



        } catch (QueryException $ex) {

            throw new HttpException(401, "Tambah kategori beban tidak berhasil, silahkan coba kembali!");

        }



    }



    public function edit_kategoribeban(Request $request){

        try {

            $validator = Validator::make($request->all(),

                             array(

                        'id' => 'required',

                        'id_toko' => 'required',

                        'kategori' => 'required',

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

                

              $cektoko = Toko::where('id',$request->id_toko)->first();

              if($cektoko){

                    

                    $addktr = BebanKategori::where('id',$request->id)->where('id_toko',$request->id_toko)->first();

                    if($addktr){

                        $addktr->kategori = $request->kategori;

                        if($addktr->save()){

                            $messages = 'Data kategori beban Berhasil Diperbaharui';

                            $respone =  fractal()

                                ->item($messages)

                                ->transformWith(new SuccessTransformer)

                                ->serializeWith(new ArraySerializer)        

                                ->toArray();

                            return response()->json($respone, 200); 

                        }else{

                            $messages = 'Edit kategori beban tidak berhasil, silahkan coba kembali!';

                            $status_code = 401;

                            $response =  fractal()

                                ->item($messages)

                                ->transformWith(new ErorrTransformer($status_code))

                                ->serializeWith(new ArraySerializer())

                                ->toArray();

                            return response()->json($respone, 401);

                        }

                    }else{

                        $messages = 'Id kategori beban / Id Toko tidak sesuai, silahkan coba kembali!';

                        $status_code = 401;

                        $response =  fractal()

                            ->item($messages)

                            ->transformWith(new ErorrTransformer($status_code))

                            ->serializeWith(new ArraySerializer())

                            ->toArray();

                        return response()->json($respone, 401);

                    }

                    



                    

              }else{

                    $messages = 'Id Toko Tidak Ditemukan!';

                    $status_code = 401;

                    $response =  fractal()

                        ->item($messages)

                        ->transformWith(new ErorrTransformer($status_code))

                        ->serializeWith(new ArraySerializer())

                        ->toArray();

                    return response()->json($respone, 401); 

              }

              

            }



        } catch (QueryException $ex) {

            throw new HttpException(401, "Edit kategori beban tidak berhasil, silahkan coba kembali!");

        }



    }



    public function hapus_kategoribeban(Request $request){

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

                $response =  fractal()

                    ->item($error_messages)

                    ->transformWith(new ErorrTransformer($status_code))

                    ->serializeWith(new ArraySerializer())

                    ->toArray();

                return response()->json($response, 422);



            }else{

                

              $cektoko = Toko::where('id',$request->id_toko)->first();

              if($cektoko){

                    

                    $addktr = BebanKategori::where('id',$request->id)->where('id_toko',$request->id_toko)->first();

                    if($addktr){

                        if($addktr->delete()){

                            $messages = 'Data kategori beban Berhasil Dihapus';

                            $respone =  fractal()

                                ->item($messages)

                                ->transformWith(new SuccessTransformer)

                                ->serializeWith(new ArraySerializer)        

                                ->toArray();

                            return response()->json($respone, 200); 

                        }else{

                            $messages = 'Hapus kategori beban tidak berhasil, silahkan coba kembali!';

                            $status_code = 401;

                            $response =  fractal()

                                ->item($messages)

                                ->transformWith(new ErorrTransformer($status_code))

                                ->serializeWith(new ArraySerializer())

                                ->toArray();

                            return response()->json($respone, 401);

                        }

                    }else{

                        $messages = 'Id kategori beban / Id Toko tidak sesuai, silahkan coba kembali!';

                        $status_code = 401;

                        $response =  fractal()

                            ->item($messages)

                            ->transformWith(new ErorrTransformer($status_code))

                            ->serializeWith(new ArraySerializer())

                            ->toArray();

                        return response()->json($respone, 401);

                    }

                    

              }else{

                    $messages = 'Id Toko Tidak Ditemukan!';

                    $status_code = 401;

                    $response =  fractal()

                        ->item($messages)

                        ->transformWith(new ErorrTransformer($status_code))

                        ->serializeWith(new ArraySerializer())

                        ->toArray();

                    return response()->json($respone, 401); 

              }

              

            }



        } catch (QueryException $ex) {

            throw new HttpException(401, "Hapus kategori beban tidak berhasil, silahkan coba kembali!");

        }



    }



    public function sync_beban_kategori(Request $request){

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

        $kategori = $request->kategori;
        $aktif = $request->aktif;


        $checkid = BebanKategori::where('id_local',$request->id_local)
        ->where('id_toko',$request->id_toko)

        ->first();

    if($checkid == null){

        $data = new BebanKategori();
        $data->id_local = $id;

        $data->id_toko = $id_toko;
        $data->kategori = $kategori;
        $data->aktif = $aktif;
   

        if($data->save()){

        $messages = 'Data Beban kategori Berhasil Ditambah';

        $respone = fractal()

        ->item($messages)

        ->transformWith(new SuccessTransformer)

        ->serializeWith(new ArraySerializer)

        ->toArray();

        return response()->json($respone, 200);

    }else{

        $messages = 'Tambah Beban kategori tidak berhasil, silahkan coba kembali!';

        $status_code = 401;

        $response = fractal()

        ->item($messages)

        ->transformWith(new ErorrTransformer($status_code))

        ->serializeWith(new ArraySerializer())

        ->toArray();

        return response()->json($response, 401);

        }

    }else{

        $data = BebanKategori::where('id_local',$request->id_local)->where('id_toko',$request->id_toko)->first();

        $data->id_toko = $id_toko;
        $data->kategori = $kategori;
        $data->aktif = $aktif;

    if($data->save()){

        $messages = 'Data beban kategori Berhasil Diupdate';

        $respone = fractal()

        ->item($messages)

        ->transformWith(new SuccessTransformer)

        ->serializeWith(new ArraySerializer)

        ->toArray();

        return response()->json($respone, 200);

    }else{

        $messages = 'Update Beban kategori tidak berhasil, silahkan coba kembali!';

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




    public function sync_produk_jenis(Request $request){

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

            $nama_jenis = $request->nama_jenis;

            $aktif = $request->aktif;


            $checkid = ProdukJenis::where('id_local',$request->id_local)
                                   ->where('id_toko',$request->id_toko)

                                      ->first();

            if($checkid == null){

            $data = new ProdukJenis();

            $data->id_local = $id;
            $data->id_toko = $id_toko;
            $data->nama_jenis = $nama_jenis;
            $data->aktif = $aktif;


            if($data->save()){

            $messages = 'Data produk jenis Berhasil Ditambah';

            $respone = fractal()

            ->item($messages)

            ->transformWith(new SuccessTransformer)

            ->serializeWith(new ArraySerializer)

            ->toArray();

            return response()->json($respone, 200);

    }else{

            $messages = 'Tambah produk jenis tidak berhasil, silahkan coba kembali!';

            $status_code = 401;

            $response = fractal()

            ->item($messages)

            ->transformWith(new ErorrTransformer($status_code))

            ->serializeWith(new ArraySerializer())

            ->toArray();

            return response()->json($response, 401);

    }

    }else{

            $data = ProdukJenis::where('id_local',$request->id_local)->where('id_toko',$request->id_toko)->first();

            $data->id_toko = $id_toko;
            $data->nama_jenis = $nama_jenis;
            $data->aktif = $aktif;

            if($data->save()){

            $messages = 'Data produk jenis Berhasil Diupdate';

            $respone = fractal()

            ->item($messages)

            ->transformWith(new SuccessTransformer)

            ->serializeWith(new ArraySerializer)

            ->toArray();

            return response()->json($respone, 200);

    }else{

            $messages = 'Update produk jenis tidak berhasil, silahkan coba kembali!';

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
