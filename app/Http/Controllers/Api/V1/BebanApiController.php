<?php



namespace App\Http\Controllers\Api\V1;



use Illuminate\Http\Request;

use App\Http\Controllers\Controller;



use DB;

use Validator;



use App\Models\Toko;

use App\Models\Beban;

use App\Models\BebanKategori;



use App\Transformers\ErorrTransformer;

use App\Transformers\SuccessTransformer;

use App\Transformers\BebanTransformer;





use League\Fractal\Pagination\IlluminatePaginatorAdapter;

use Spatie\Fractalistic\ArraySerializer;

use App\Http\Controllers\Api\ArraySerializerV2;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;









class BebanApiController extends Controller

{

   

////////BEBAN (tbl_beban)-----------------------------

    public function databeban_hariini(Request $request){

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

                

              $ktr = Beban::where('tgl',date("Y-m-d"))

                        ->whereRaw('id_toko = '.$request->id_toko.' AND (nama LIKE "%'.$request->search.'%" OR tgl LIKE "%'.$request->search.'%" OR jumlah LIKE "%'.$request->search.'%")')

                        ->with('kategoribeban')

                        ->orderBy('tgl','DESC')->get();

                    

                        

              if($ktr){
             

                    $success = true;

                    $status_code = 200;

                    $messages = 'Data Berhasil Ditampilkan!';

                    $respone =  fractal()

                          ->collection($ktr, new BebanTransformer(), 'data')

                            ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))

                    

                            ->addMeta([

                                'catatan' => '' 

                            ])

                            ->toArray();

                      return response()->json($respone, 200);

              }else{

             

                    $success = false;

                    $status_code = 200;

                    $messages = 'Tidak Ada Data Ditemukan!';

                    $response =  fractal()

                        ->collection($ktr, new BebanTransformer(), 'data')

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

    

    public function databeban(Request $request){

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

                

              $ktr = Beban::whereRaw('id_toko = '.$request->id_toko.' AND (nama LIKE "%'.$request->search.'%" OR tgl LIKE "%'.$request->search.'%" OR jumlah LIKE "%'.$request->search.'%")')

                    ->orderBy('tgl','DESC')->get();

                    


              if($ktr){

                               

                    $success = true;

                    $status_code = 200;

                    $messages = 'Data Berhasil Ditampilkan!';

                    $respone =  fractal()

                          ->collection($ktr, new BebanTransformer(), 'data')

                            ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))


                            ->addMeta([

                                'catatan' => '' 

                            ])

                            ->toArray();

                      return response()->json($respone, 200);

              }else{

     

                    $success = false;

                    $status_code = 200;

                    $messages = 'Tidak Ada Data Ditemukan!';

                    $response =  fractal()

                        ->collection($ktr, new BebanTransformer(), 'data')

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



    public function tambah_beban(Request $request){

        try {

            $validator = Validator::make($request->all(),

                             array(

                        'id_toko' => 'required|int',

                        'id_ktr_beban' => 'required|int',

                        'id_user' => 'required|int',

                        'nama' => 'required',

                        'keterangan' => 'required',

                        'tgl' => 'required',

                        'jumlah' => 'required',

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

                    

                    $addktr = new Beban();

                    $addktr->id_toko = $request->id_toko;

                    $addktr->id_ktr_beban = $request->id_ktr_beban;

                    $addktr->id_user = $request->id_user;

                    $addktr->nama = $request->nama;

                    $addktr->keterangan = $request->keterangan;

                    $addktr->tgl = date("Y-m-d",strtotime($request->tgl));

                    $addktr->jumlah = $request->jumlah;

                    



                    if($addktr->save()){

                        $messages = 'Data Berhasil Ditambah';

                        $respone =  fractal()

                            ->item($messages)

                            ->transformWith(new SuccessTransformer)

                            ->serializeWith(new ArraySerializer)        

                            ->toArray();

                        return response()->json($respone, 200); 

                    }else{

                        $messages = 'Tambah kategori tidak berhasil, silahkan coba kembali!';

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

            throw new HttpException(401, "Tambah kategori tidak berhasil, silahkan coba kembali!");

        }



    }



    public function edit_beban(Request $request){

        try {

            $validator = Validator::make($request->all(),

                             array(

                        'id' => 'required|int',

                        'id_toko' => 'required|int',

                        'id_ktr_beban' => 'required|int',

                        'nama' => 'required',

                        'keterangan' => 'required',

                        'tgl' => 'required',

                        'jumlah' => 'required',

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

                    

                    $addktr = Beban::where('id',$request->id)->where('id_toko',$request->id_toko)->first();

                    if($addktr){

                        $addktr->id_ktr_beban = $request->id_ktr_beban;

                        $addktr->nama = $request->nama;

                        $addktr->keterangan = $request->keterangan;

                        $addktr->tgl = date("Y-m-d",strtotime($request->tgl));

                        $addktr->jumlah = $request->jumlah;

                        if($addktr->save()){

                            $messages = 'Data Beban Berhasil Diperbaharui';

                            $respone =  fractal()

                                ->item($messages)

                                ->transformWith(new SuccessTransformer)

                                ->serializeWith(new ArraySerializer)        

                                ->toArray();

                            return response()->json($respone, 200); 

                        }else{

                            $messages = 'Edit Beban tidak berhasil, silahkan coba kembali!';

                            $status_code = 401;

                            $response =  fractal()

                                ->item($messages)

                                ->transformWith(new ErorrTransformer($status_code))

                                ->serializeWith(new ArraySerializer())

                                ->toArray();

                            return response()->json($respone, 401);

                        }

                    }else{

                        $messages = 'Id Beban / Id Toko tidak sesuai, silahkan coba kembali!';

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

            throw new HttpException(401, "Edit kategori tidak berhasil, silahkan coba kembali!");

        }



    }



    public function hapus_beban(Request $request){

        try {

            $validator = Validator::make($request->all(),

                             array(

                        'id' => 'required|int',

                        'id_toko' => 'required|int'

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

                    

                    $addktr = Beban::where('id',$request->id)->where('id_toko',$request->id_toko)->first();

                    if($addktr){

                        if($addktr->delete()){

                            $messages = 'Data Beban Berhasil Dihapus';

                            $respone =  fractal()

                                ->item($messages)

                                ->transformWith(new SuccessTransformer)

                                ->serializeWith(new ArraySerializer)        

                                ->toArray();

                            return response()->json($respone, 200); 

                        }else{

                            $messages = 'Hapus Beban tidak berhasil, silahkan coba kembali!';

                            $status_code = 401;

                            $response =  fractal()

                                ->item($messages)

                                ->transformWith(new ErorrTransformer($status_code))

                                ->serializeWith(new ArraySerializer())

                                ->toArray();

                            return response()->json($respone, 401);

                        }

                    }else{

                        $messages = 'Id Beban / Id Toko tidak sesuai, silahkan coba kembali!';

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

            throw new HttpException(401, "Hapus Beban tidak berhasil, silahkan coba kembali!");

        }



    }





    public function beban_local_to_database(Request $request){

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

            $id_ktr_beban = $request->id_ktr_beban;

            $id_user = $request->id_user;

            $nama = $request->nama;

            $keterangan = $request->keterangan;

            $tgl = $request->tgl;

            $jumlah = $request->jumlah;
            $aktif = $request->aktif;

        
        
            $checkid = Beban::where('id_local',$request->id_local)
                            ->where('id_toko',$request->id_toko)
                                
                            ->first();

        if($checkid == null){

            $data = new Beban;
            $data->id_local = $id;
        
            $data->id_toko = $id_toko;
            $data->id_user = $id_user;
            $data->id_ktr_beban = $id_ktr_beban;
            $data->nama = $nama;
            $data->keterangan = $keterangan;
            $data->tgl = date("Y-m-d",strtotime($tgl));
            $data->jumlah = $jumlah;
            $data->aktif = $aktif;
        
            if($data->save()){

            $messages = 'Data Beban Berhasil Ditambah';

            $respone = fractal()

            ->item($messages)

            ->transformWith(new SuccessTransformer)

            ->serializeWith(new ArraySerializer)

            ->toArray();

            return response()->json($respone, 200);

        }else{

            $messages = 'Tambah Beban tidak berhasil, silahkan coba kembali!';

            $status_code = 401;

            $response = fractal()

            ->item($messages)

            ->transformWith(new ErorrTransformer($status_code))

            ->serializeWith(new ArraySerializer())

            ->toArray();

            return response()->json($response, 401);

        }

        }else{

            $data = Beban::where('id_local',$request->id_local)->where('id_toko',$request->id_toko)->first();

        $data->id_toko = $id_toko;
        $data->id_user = $id_user;
        $data->id_ktr_beban = $id_ktr_beban;
        $data->nama = $nama;
        $data->keterangan = $keterangan;
        $data->tgl = date("Y-m-d",strtotime($tgl));
        $data->jumlah = $jumlah;
        $data->aktif = $aktif;

            if($data->save()){

            $messages = 'Data beban Berhasil Diupdate';

            $respone = fractal()

            ->item($messages)

            ->transformWith(new SuccessTransformer)

            ->serializeWith(new ArraySerializer)

            ->toArray();

            return response()->json($respone, 200);

        }else{

            $messages = 'Update Beban tidak berhasil, silahkan coba kembali!';

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
