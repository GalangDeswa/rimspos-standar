<?php



namespace App\Http\Controllers\Api\V1;



use Illuminate\Http\Request;

use App\Http\Controllers\Controller;



use DB;

use Validator;

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

use Symfony\Component\HttpKernel\Exception\HttpException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;







class ProdukApiController extends Controller

{



////// PRODUK

    public function data_produk_all(Request $request){

        try {

            $validator = Validator::make($request->all(),

                             array(

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

                $produk = Produk::where('id_toko',$request->id_toko)

                        ->whereRaw('status = 1 AND (nama_produk LIKE "%'.$request->search.'%") 
                        
                        OR status = 1 AND (barcode LIKE "%'.$request->search.'%") ')

                        //->whereRaw('barcode LIKE "%'.$request->search.'%"')

                        ->with('jenisproduk','kategoriproduk')

                        ->orderBy('nama_produk','ASC')

                        ->paginate(10);

                if(count($produk) > 0){

                    $produkrow = $produk->getCollection();                    

                    $success = true;

                    $status_code = 200;

                    $messages = 'Data Berhasil Ditampilkan!';

                    $respone =  fractal()

                          ->collection($produkrow, new ProdukTransformer, 'data')

                            ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))

                            ->paginateWith(new IlluminatePaginatorAdapter($produk))

                            ->addMeta([

                                'catatan' => array('kategori' => '1. barang, 2. Jasa, 3. Paket',

                                                    'status' => '1. aktif, 2. tidak aktif   ') 

                            ])

                            ->toArray();

                      return response()->json($respone, 200);

                }else{

                    $produkrow = $produk->getCollection();                    

                    $success = false;

                    $status_code = 200;

                    $messages = 'Tidak Ada Data Ditemukan!';

                    $respone =  fractal()

                            ->collection($produkrow, new ProdukTransformer, 'data')

                            ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))

                            ->paginateWith(new IlluminatePaginatorAdapter($produk))

                            ->addMeta([

                                'catatan' => array('kategori' => '1. barang, 2. Jasa, 3. Paket',

                                                    'status' => '1. aktif, 2. tidak aktif   ') 

                            ])

                            ->toArray();

                      return response()->json($respone, 200);

                }

              

            }

        } catch (QueryException $ex) {

            throw new HttpException(500, "Gagal menampilkan data, coba lagi!");

        }



    }



    public function data_produk_byjenis(Request $request){

        try {

            $validator = Validator::make($request->all(),

                             array(

                        'id_toko' => 'required|int',

                        'id_jenis' => 'required|int',



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

                $produk = Produk::where('id_toko',$request->id_toko)

                        ->where('id_jenis',$request->id_jenis)

                        ->whereRaw('status = 1 AND (nama_produk LIKE "%'.$request->search.'%")')

                        ->with('jenisproduk','kategoriproduk')

                        ->orderBy('nama_produk','ASC')

                        ->paginate(10);

                if(count($produk) > 0){

                    $produkrow = $produk->getCollection();                    

                    $success = true;

                    $status_code = 200;

                    $messages = 'Data Berhasil Ditampilkan!';

                    $respone =  fractal()

                          ->collection($produkrow, new ProdukTransformer, 'data')

                            ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))

                            ->paginateWith(new IlluminatePaginatorAdapter($produk))

                            ->addMeta([

                                'catatan' => array('kategori' => '1. barang, 2. Jasa, 3. Paket',

                                                    'status' => '1. aktif, 2. tidak aktif   ') 

                            ])

                            ->toArray();

                      return response()->json($respone, 200);

                }else{

                    $produkrow = $produk->getCollection();                    

                    $success = false;

                    $status_code = 200;

                    $messages = 'Tidak Ada Data Ditemukan!';

                    $respone =  fractal()

                          ->collection($produkrow, new ProdukTransformer, 'data')

                            ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))

                            ->paginateWith(new IlluminatePaginatorAdapter($produk))

                            ->addMeta([

                                'catatan' => array('kategori' => '1. barang, 2. Jasa, 3. Paket',

                                                    'status' => '1. aktif, 2. tidak aktif   ') 

                            ])

                            ->toArray();

                      return response()->json($respone, 200);

                }

              

            }

        } catch (QueryException $ex) {

            throw new HttpException(500, "Gagal menampilkan data, coba lagi!");

        }



    }

    

    public function tambah_produk(Request $request){

        try {

            $validator = Validator::make($request->all(),

                             array(

                        'id_user' => 'required|int',

                        'id_toko' => 'required|int',

                        'id_jenis' => 'required|int',

                        // 'id_kategori' => 'required|int',

                        'nama_produk' => 'required',

                        'id_jenis_stock' => 'required|int',

                        'deskripsi' => 'required',

                        'qty' => 'required_if:id_jenis_stock,==,1|nullable|integer',

                        'harga' => 'required|int',

                        'harga_modal' => 'required|int',

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

                    

                    $data                            = new Produk();

                    if($request->barcode)
                    {
                          $data->barcode             = $request->barcode;
                    }

                    $data->id_user                   = $request->id_user;

                    $data->id_toko                   = $request->id_toko;

                    $data->id_jenis                  = $request->id_jenis;

                    $data->id_kategori               = 1;

                    $data->id_jenis_stock            = $request->id_jenis_stock;

                    $data->nama_produk               = $request->nama_produk;

                    $data->deskripsi                 = $request->deskripsi;

                    if($request->id_jenis_stock == 1){

                        $data->qty= $request->qty;

                    }
 
                    $data->harga                     = $request->harga;

                    $data->harga_modal               = $request->harga_modal;

                   if($request->diskon_barang){
                     $data->diskon_barang = $request->diskon_barang;
                   }

                    $data->status                     = 1;

                    

                    if ($request->file('image')) {

                      $image = $request->file('image');

                      $input['imagename'] =  date('ymdhis').'.'.$image->getClientOriginalExtension();

                      $destinationPath = ('uploads/produk');

                    //   $img = Image::make($image->getRealPath());

                    //   $img->resize(500, 500, function ($constraint) {

                    //       $constraint->aspectRatio();

                    //   })->save(public_path($destinationPath.'/'.$input['imagename']));



                    $image->move(public_path($destinationPath), '/'.$input['imagename']);



                      $direktori = $destinationPath.'/'.$input['imagename'];



                      $data->image         = $direktori;

                    }





                    if($data->save()){                       



                        $messages = 'Data Berhasil Ditambah';

                        $respone =  fractal()

                            ->item($messages)

                            ->transformWith(new SuccessTransformer)

                            ->serializeWith(new ArraySerializer)        

                            ->toArray();

                        return response()->json($respone, 200); 

                    }else{

                        $messages = 'Tambah Produk tidak berhasil, silahkan coba kembali!';

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

            throw new HttpException(500, "Gagal Menambah data, coba lagi!");

        }



    }



    public function edit_produk(Request $request){

        try {

            $validator = Validator::make($request->all(),

                             array(

                        'id' => 'required|int',

                        'id_toko' => 'required|int',

                        'id_jenis' => 'required|int',

                        'nama_produk' => 'required',

                        'deskripsi' => 'required',

                        // 'harga' => 'required',

                            )

            );

            if ($validator->fails()) {

                // $error_messages = $validator->messages()->all();

                $error_messages = 'Missing Parameter Value!';

                $response = fractal()

                    ->item($error_messages)

                    ->transformWith(new ErorValidasiTransformer)

                    ->serializeWith(new ArraySerializer)

                    ->toArray();



                return response()->json($response, 422);



            }else{

                

                $cektoko = Toko::where('id',$request->id_toko)->first();

                if($cektoko){

                    

                    $data                          = Produk::where('id',$request->id)->where('id_toko',$request->id_toko)->first();

                    $data->id_jenis                = $request->id_jenis;

                    if($request->barcode)
                    {
                        $data->barcode              = $request->barcode;
                    }

                  // $data->id_jenis_stock             = $request->id_jenis_stock;

                    $data->nama_produk               = $request->nama_produk;

                    $data->deskripsi                = $request->deskripsi;

                    

                    $data->harga                    = $request->harga;

                    $data->harga_modal               = $request->harga_modal;

                  if($request->diskon_barang){
                      $data->diskon_barang = $request->diskon_barang;
                  }


                    

                    if ($request->file('image')) {

                        if ($data->image != "" ||$data->image != null ) {

                            $path = $data->image;

                            unlink(public_path($path));

                        }

                      $image = $request->file('image');

                      $input['imagename'] =  date('ymdhis').'.'.$image->getClientOriginalExtension();

                      $destinationPath = ('uploads/produk');

                    //   $img = Image::make($image->getRealPath());

                    //   $img->resize(500, 500, function ($constraint) {

                    //       $constraint->aspectRatio();

                    //   })->save(public_path($destinationPath.'/'.$input['imagename']));



                      $image->move(public_path($destinationPath), '/'.$input['imagename']);



                      $direktori = $destinationPath.'/'.$input['imagename'];



                      $data->image         = $direktori;

                    }





                    if($data->save()){                                  



                        $messages = 'Data Berhasil Diperbaharui';

                        $respone =  fractal()

                            ->item($messages)

                            ->transformWith(new SuccessTransformer)

                            ->serializeWith(new ArraySerializer)        

                            ->toArray();

                        return response()->json($respone, 200); 

                    }else{

                        $messages = 'Edit Produk tidak berhasil, silahkan coba kembali!';

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

            throw new HttpException(500, "Gagal Merubah data, coba lagi!");

        }



    }



    public function hapus_produk(Request $request){

        try {

            $validator = Validator::make($request->all(),

                             array(

                        'id_toko' => 'required|int',

                        'id' => 'required|int',

                            )

            );





            if ($validator->fails()) {

                // $error_messages = $validator->messages()->all();

                $error_messages = 'Missing Parameter Value!';

                $response = fractal()

                    ->item($error_messages)

                    ->transformWith(new ErorValidasiTransformer)

                    ->serializeWith(new ArraySerializer)

                    ->toArray();



                return response()->json($response, 422);



            }else{

                

              $cektoko = Toko::where('id',$request->id_toko)->first();

              if($cektoko){

                    

                    $addktr = Produk::where('id', $request->id)->where('id_toko',$request->id_toko)->first();

                    if($addktr){

                        $addktr->status = 3;

                        if ($addktr->image != "" || $addktr->image != null) {

                            $path = $addktr->image;

                            unlink(public_path($path));

                        }

                        if($addktr->save()){

                            $messages = 'Data Produk Berhasil Dihapus';

                            $respone =  fractal()

                                ->item($messages)

                                ->transformWith(new SuccessTransformer)

                                ->serializeWith(new ArraySerializer)        

                                ->toArray();

                            return response()->json($respone, 200); 

                        }else{

                            $messages = 'Hapus Produk tidak berhasil, silahkan coba kembali!';

                            $status_code = 401;

                            $response =  fractal()

                                ->item($messages)

                                ->transformWith(new ErorrTransformer($status_code))

                                ->serializeWith(new ArraySerializer())

                                ->toArray();

                            return response()->json($response, 401);

                        }

                    }else{

                        $messages = 'Id Produk / Id Toko tidak sesuai, silahkan coba kembali!';

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

            throw new HttpException(500, "Hapus Produk tidak berhasil, silahkan coba kembali!");

        }



    }



    public function tambahstock_produk(Request $request){

        try {

            $validator = Validator::make($request->all(),

                             array(

                        'id_toko' => 'required|int',

                        'id' => 'required|int',

                        'qty' => 'required|int',

                            )

            );





            if ($validator->fails()) {

                // $error_messages = $validator->messages()->all();

                $error_messages = 'Missing Parameter Value!';

                $response = fractal()

                    ->item($error_messages)

                    ->transformWith(new ErorValidasiTransformer)

                    ->serializeWith(new ArraySerializer)

                    ->toArray();



                return response()->json($response, 422);



            }else{

                

              $cektoko = Toko::where('id',$request->id_toko)->first();

              if($cektoko){

                    $addktr = Produk::where('id', $request->id)->where('id_toko',$request->id_toko)->first();

                    if($addktr){

                        $addktr->qty = $addktr->qty+$request->qty;

                        if($addktr->save()){

                            $messages = 'Data Stock Produk Berhasil Diperbaharui';

                            $respone =  fractal()

                                ->item($messages)

                                ->transformWith(new SuccessTransformer)

                                ->serializeWith(new ArraySerializer)        

                                ->toArray();

                            return response()->json($respone, 200); 

                        }else{

                            $messages = 'Menambah Stock Produk tidak berhasil, silahkan coba kembali!';

                            $status_code = 401;

                            $response =  fractal()

                                ->item($messages)

                                ->transformWith(new ErorrTransformer($status_code))

                                ->serializeWith(new ArraySerializer())

                                ->toArray();

                            return response()->json($response, 401);

                        }

                    }else{

                        $messages = 'Id Produk / Id Toko tidak sesuai, silahkan coba kembali!';

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

            throw new HttpException(500, "Menambah Stock Produk tidak berhasil, silahkan coba kembali!");

        }



    }





}
