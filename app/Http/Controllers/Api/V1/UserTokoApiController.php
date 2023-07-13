<?php



namespace App\Http\Controllers\Api\V1;



use Illuminate\Http\Request;

use App\Http\Controllers\Controller;



use DB;

use Validator;



use App\Models\Toko;

use App\Models\User;



use App\Transformers\ErorrTransformer;

use App\Transformers\UserTransformer;

use App\Transformers\SuccessTransformer;






use League\Fractal\Pagination\IlluminatePaginatorAdapter;

use Spatie\Fractalistic\ArraySerializer;

use App\Http\Controllers\Api\ArraySerializerV2;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;







class UserTokoApiController extends Controller

{



    public function cleannohp($nomor){

        $hp = '';

        if(substr($nomor,0,1) === '0'){

            $hp = $nomor;

        } elseif(substr($nomor,0,1) === '+'){

            if(substr($nomor,0,3) === '+62'){

                $hp = substr_replace($nomor,'0',0,3);

            } else{

                $hp = substr_replace($nomor,'0',0,1);

            }

        }else{

            $hp = $nomor;

        }

        return $hp;

    }



    

    public function datakasir(Request $request){

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

                $ktr = User::where('id_toko',$request->id_toko)->whereIn('role', ['1', '2'])->get();

                if($ktr){

                                      

                    $success = true;

                    $status_code = 200;

                    $messages = 'Data Berhasil Ditampilkan!';

                    $response =  fractal()

                            ->collection($ktr, new UserTransformer(), 'data')

                            ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))


                            ->addMeta([

                                'catatan' => array( 

                                            'role' => '1. kasir 2. admin',

                                            'status' => '1. aktif 2. tidak aktif',

                                         )

                            ])

                            ->toArray();

                      return response()->json($response, 200);

                }else{

                    

                  

                    $success = false;

                    $status_code = 200;

                    $messages = 'Tidak Ada Data Ditemukan!';

                    $response =  fractal()

                        ->collection($ktr, new UserTransformer(), 'data')

                        ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))

                    

                        ->addMeta([

                                'catatan' => array( 

                                            'role' => '1. kasir 2. admin',

                                            'status' => '1. aktif 2. tidak aktif',

                                         )

                            ])

                        ->toArray();

                    return response()->json($response, 200); 

                }

              

            }

        } catch (QueryException $ex) {

            throw new HttpException(401, "Gagal menampilkan data, coba lagi!");

        }



    }

    

    public function tambahkasir(Request $request){

        try {

            $validator = Validator::make($request->all(),

                             array(

                        'id_user' => 'required|int',

                        'id_toko' => 'required|int',

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

                $response =  fractal()

                    ->item($error_messages)

                    ->transformWith(new ErorrTransformer($status_code))

                    ->serializeWith(new ArraySerializer())

                    ->toArray();

                return response()->json($response, 422);



            }else{

                

                $cektoko = Toko::where('id',$request->id_toko)->first();

                if($cektoko){

                    $dataFound = User::where('email', $request->email)->first();

                    if($dataFound){

                        $messages = 'Email Sudah Terdaftar Pada Sistem';

                        $status_code = 401;

                        $response =  fractal()

                            ->item($messages)

                            ->transformWith(new ErorrTransformer($status_code))   

                            ->serializeWith(new ArraySerializer)  

                            ->toArray();

                        return response()->json($response, 401);

                    }else{

                        if($request->role != "1" && $request->role != "2"){

                            $messages = 'Role akses hanya terdiri dari (1. kasir. 2.admin)';

                            $status_code = 401;

                            $response =  fractal()

                                ->item($messages)

                                ->transformWith(new ErorrTransformer($status_code))   

                                ->serializeWith(new ArraySerializer)  

                                ->toArray();

                            return response()->json($response, 401);

                        }



                        $data                               = new User();

                        $data->created_by                      = $request->id_user;

                        $data->id_toko                = $request->id_toko;

                        $data->name              = $request->nama;

                        $data->email             = $request->email;

                        $data->hp               = $this->cleannohp($request->hp);

                        $data->password                  = bcrypt($request->password);

                        $data->role                  = $request->role;



                        $data->status                  = 1;

                        



                        if($data->save()){

                            $messages = 'Data Berhasil Ditambah';

                            $response =  fractal()

                                ->item($messages)

                                ->transformWith(new SuccessTransformer)

                                ->serializeWith(new ArraySerializer)        

                                ->toArray();

                            return response()->json($response, 200); 

                        }else{

                            $messages = 'Tambah User tidak berhasil, silahkan coba kembali!';

                            $status_code = 401;

                            $response =  fractal()

                                ->item($messages)

                                ->transformWith(new ErorrTransformer($status_code))   

                                ->serializeWith(new ArraySerializer)  

                                ->toArray();

                            return response()->json($response, 401);

                        }

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

            throw new HttpException(401, "Gagal Menambah data, coba lagi!");

        }



    }



    public function editkasir(Request $request){

        try {

            $validator = Validator::make($request->all(),

                             array(

                        'id' => 'required|int',

                        'id_user' => 'required|int',

                        'id_toko' => 'required|int',

                        'nama' => 'required',

                        'email' => 'required',

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

                    $cekidkasir = User::where('id', $request->id)->first();

                    if($cekidkasir){

                        $dataFound = User::where('id','<>', $request->id)->where('email', $request->email)->first();

                        if($dataFound){

                            $messages = 'Email Sudah Terdaftar Pada Sistem';

                            $status_code = 401;

                            $response =  fractal()

                                ->item($messages)

                                ->transformWith(new ErorrTransformer($status_code))

                                ->serializeWith(new ArraySerializer())

                                ->toArray();

                            return response()->json($response, 401);

                        }else{



                            $data        = User::where('id', $request->id)->where('id_toko',$request->id_toko)->first();

                            $data->created_by = $request->id_user;

                            $data->name      = $request->nama;

                            $data->email        = $request->email;

                            $data->hp        = $this->cleannohp($request->hp);

                            



                            if($data->save()){

                                $messages = 'Data Berhasil Diperbaharui';

                                $response =  fractal()

                                    ->item($messages)

                                    ->transformWith(new SuccessTransformer)

                                    ->serializeWith(new ArraySerializer)        

                                    ->toArray();

                                return response()->json($response, 200); 

                            }else{

                                $messages = 'Edit User tidak berhasil, silahkan coba kembali!';

                                $status_code = 401;

                                $response =  fractal()

                                    ->item($messages)

                                    ->transformWith(new ErorrTransformer($status_code))

                                    ->serializeWith(new ArraySerializer())

                                    ->toArray();

                                return response()->json($response, 401);

                            }

                        }

                    }else{

                        $messages = 'Id User Tidak Ditemukan!';

                        $status_code = 401;

                        $response =  fractal()

                            ->item($messages)

                            ->transformWith(new ErorrTransformer($status_code))

                            ->serializeWith(new ArraySerializer())

                            ->toArray();

                        return response()->json($response, 401);

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

            throw new HttpException(401, "Gagal Merubah data, coba lagi!");

        }



    }



    public function gantipasskasir(Request $request){

        try {

            $validator = Validator::make($request->all(),

                             array(

                        'id' => 'required|int',

                       // 'id_user' => 'required|int',

                        'id_toko' => 'required|int',

                        'password' => 'required',
                        'old_password' =>'required',

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

                    $cekidkasir = User::where('id', $request->id)->first();


                    if($cekidkasir){

                        if (Hash::check($request->old_password,$cekidkasir->password)){

                             $data = User::where('id_toko',$request->id_toko)->where('id', $request->id)->first();

                             $data->password = bcrypt($request->password);


                             if($data->save()){

                                $messages = 'Data Berhasil Diperbaharui';

                                $response = fractal()

                                ->item($messages)

                                ->transformWith(new SuccessTransformer)

                                ->serializeWith(new ArraySerializer)

                                ->toArray();

                                return response()->json($response, 200);

                             }else{

                                $messages = 'Edit Pass User tidak berhasil, silahkan coba kembali!';

                                $status_code = 401;

                                $response = fractal()

                                ->item($messages)

                                ->transformWith(new ErorrTransformer($status_code))

                                ->serializeWith(new ArraySerializer())

                                ->toArray();

                                return response()->json($response, 401);

                             }

                        }else{

                            $messages = 'Password lama salah';

                            $status_code = 401;

                            $response = fractal()

                            ->item($messages)

                            ->transformWith(new ErorrTransformer($status_code))

                            ->serializeWith(new ArraySerializer())

                            ->toArray();

                            return response()->json($response, 401);
                        }

                       
                    }else{

                        $messages = 'Id User Tidak Ditemukan!';

                        $status_code = 401;

                        $response =  fractal()

                            ->item($messages)

                            ->transformWith(new ErorrTransformer($status_code))

                            ->serializeWith(new ArraySerializer())

                            ->toArray();

                        return response()->json($response, 401);

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

            throw new HttpException(401, "Gagal Merubah data, coba lagi!");

        }



    }



    public function hapuskasir(Request $request){

        try {

            $validator = Validator::make($request->all(),

                             array(

                        'id' => 'required|int',

                        'id_toko' => 'required|int',

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

                    

                    $addktr = User::where('id', $request->id)->where('id_toko',$request->id_toko)->first();

                    if($addktr){

                        if($addktr->delete()){

                            $messages = 'Data User Berhasil Dihapus';

                            $response =  fractal()

                                ->item($messages)

                                ->transformWith(new SuccessTransformer)

                                ->serializeWith(new ArraySerializer)        

                                ->toArray();

                            return response()->json($response, 200); 

                        }else{

                            $messages = 'Hapus User tidak berhasil, silahkan coba kembali!';

                            $status_code = 401;

                            $response =  fractal()

                                ->item($messages)

                                ->transformWith(new ErorrTransformer($status_code))

                                ->serializeWith(new ArraySerializer())

                                ->toArray();

                            return response()->json($response, 401);

                        }

                    }else{

                        $messages = 'Id User / Id Toko tidak sesuai, silahkan coba kembali!';

                        $status_code = 401;

                        $response =  fractal()

                            ->item($messages)

                            ->transformWith(new ErorrTransformer($status_code))

                            ->serializeWith(new ArraySerializer())

                            ->toArray();

                        return response()->json($response, 401);

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

            throw new HttpException(401, "Hapus User tidak berhasil, silahkan coba kembali!");

        }



    }




   



}
