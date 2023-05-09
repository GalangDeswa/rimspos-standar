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

use App\Models\Keranjang;



use App\Transformers\KeranjangTransformer;

use App\Transformers\ErorrTransformer;

use App\Transformers\SuccessTransformer;





use League\Fractal\Pagination\IlluminatePaginatorAdapter;

use Spatie\Fractalistic\ArraySerializer;

use App\Http\Controllers\Api\ArraySerializerV2;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;







class KeranjangApiController extends Controller

{

    public function data_keranjang(Request $request){

        try {

            $validator = Validator::make($request->all(),

                             array(

                        'id_toko' => 'required|int',

                        // 'meja' => 'required|int',

                        'id_user' => 'required|int',

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

                if($request->meja){

                    $meja = $request->meja;

                }else{

                    $meja = 1;

                }

                $produk = Keranjang::where('id_toko',$request->id_toko)

                        ->where('id_user',$request->id_user)

                        ->where('meja',$meja)

                        ->where('status','0')

                        ->with('toko','produk','kasir')

                        ->orderBy('created_at','DESC')

                        ->get();

                if(count($produk) > 0){                    

                    $sumkeranjang = Keranjang::select(DB::raw('SUM(qty*harga_brg) as harga_item, SUM(diskon_brg) as diskon_total, SUM(total) as total'))

                                    ->where('id_toko',$request->id_toko)

                                    ->where('meja',$meja)

                                    ->where('id_user',$request->id_user)

                                    ->where('status','0')

                                    ->first();

                    $success = true;

                    $status_code = 200;

                    $messages = 'Data Berhasil Ditampilkan!';

                    $response =  fractal()

                          ->collection($produk, new KeranjangTransformer, 'data')

                            ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))

                            ->addMeta([ 'subtotal'=> $sumkeranjang->harga_item,

                                        'total'=> $sumkeranjang->total,

                                        'catatan' => array('kategori' => '1. barang, 2. Jasa, 3. Paket',

                                                    'status' => '0. Barang Keranjang, 1. selesai') 

                                        ])

                            ->toArray();

                      return response()->json($response, 200);

                }else{

                    $success = false;

                    $status_code = 200;

                    $messages = 'Keranjang Masih Kosong!';

                    $response =  fractal()

                            ->collection($produk, new KeranjangTransformer, 'data')

                            ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))

                            ->addMeta([

                                'subtotal'=> 0,

                                'total'=> 0,

                                'catatan' => array('kategori' => '1. barang, 2. Jasa, 3. Paket',

                                                    'status' => '0. Barang Keranjang, 1. selesai') 

                            ])

                            ->toArray();

                      return response()->json($response, 200);

                }

              

            }

        } catch (QueryException $ex) {

            throw new HttpException(500, "Gagal menampilkan data, coba lagi!");

        }



    }





    public function tambah_keranjang(Request $request){

        try {

            $validator = Validator::make($request->all(),

                             array(

                        'id_toko' => 'required|int',

                        // 'meja' => 'required|int',

                        'id_user' => 'required|int',

                        'id_produk' => 'required|int',

                        'id_jenis_stock' => 'required|int',

                        // 'id_kategori' => 'required',

                        'diskon_brg' => 'required|int',

                        'qty' => 'required|int',

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

                    

                    $getstok = Produk::where('id_toko',$request->id_toko)->where('id',$request->id_produk)->first();

                        

                    if($getstok){

                        $stk = $getstok->qty;

                        $hrga = $getstok->harga;


                        if($request->id_jenis_stock == 1){

                            if($getstok->qty == 0){

                                $messages = 'Stok Sudah Habis!, Silahkan Tambahkan Stok Terlebih dahulu';

                                $status_code = 401;

                                $response = fractal()

                                ->item($messages)

                                ->transformWith(new ErorrTransformer($status_code))

                                ->serializeWith(new ArraySerializer())

                                ->toArray();

                                return response()->json($response, 409);

                            }elseif($getstok->qty < $request->qty){

                                $messages='Stok yang tersedia tidak cukup!, Silahkan Tambahkan Stok Terlebih dahulu' ;
                                $status_code=401; $response=fractal() ->item($messages)

                                ->transformWith(new ErorrTransformer($status_code))

                                ->serializeWith(new ArraySerializer())

                                ->toArray();

                                return response()->json($response, 409);

                                }
                        }
                        
                  

                            if($request->meja){

                                $meja = $request->meja;

                            }else{

                                $meja = 1;

                            }



                            $cekkeranjang = Keranjang::where('id_toko',$request->id_toko)

                                            ->where('id_produk',$request->id_produk)

                                            ->where('id_user',$request->id_user)

                                            ->where('meja',$meja)

                                            ->first();



                            if($cekkeranjang){

                                if($request->diskon_brg == ''){

                                    $dskon = $cekkeranjang->diskon_brg; 

                                }else{

                                    $dskon = $request->diskon_brg; 

                                }



                                // $dskon = $cekkeranjang->diskon_brg+$request->diskon_brg;


                                // if($request->diskon_kasir){

                                //    $cekkeranjang->diskon_kasir = $request->diskon_kasir;

                                // }else{

                                //        $cekkeranjang->diskon_kasir = 0;

                                // }

                                $cekkeranjang->diskon_kasir = $request->diskon_kasir;

                                $cekkeranjang->diskon_brg = $dskon;


                                $tentukanharga = $hrga*($cekkeranjang->qty+$request->qty)-$dskon - $cekkeranjang->diskon_kasir/100;



                                $cekkeranjang->qty = $cekkeranjang->qty+$request->qty;

                                $cekkeranjang->total =  $tentukanharga;

                                $cekkeranjang->save();



                            }else{

                                $keranjang = new Keranjang();

                                $keranjang->id_toko     = $request->id_toko;

                                $keranjang->meja        = $meja;

                                $keranjang->id_produk   = $request->id_produk;

                                $keranjang->id_kategori = 1;

                                $keranjang->id_jenis_stock = $request->id_jenis_stock;

                                $keranjang->id_user    = $request->id_user; 

                                

                                $keranjang->nama_brg = $getstok->nama_produk;

                                $keranjang->harga_brg = $hrga;



                                if($request->diskon_brg == ''){

                                    $dskon = '0'; 

                                }else{

                                    $dskon = $request->diskon_brg; 

                                }

                                $keranjang->diskon_brg = $dskon; 

                                

                                if($request->qty > 1){

                                    $tentukanharga = ($hrga*$request->qty)-$dskon;

                                }else{

                                    $tentukanharga = $hrga-$dskon;

                                }

                                



                                $keranjang->qty = $request->qty;

                                $keranjang->total = $tentukanharga;

                                $keranjang->status = 0;



                                $keranjang->save();

                            }



                            $messages = 'Data Berhasil Ditambah';

                            $response =  fractal()

                                ->item($messages)

                                ->transformWith(new SuccessTransformer)

                                ->serializeWith(new ArraySerializer)        

                                ->toArray();

                            return response()->json($response, 200); 

                        

                    }else{

                        $messages = 'Tidak Ada Stok Ditemukan!';

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



    public function hapus_item_keranjang(Request $request){

        try {

            $validator = Validator::make($request->all(),

                             array(

                        'id' => 'required|int',

                        'id_toko' => 'required|int',

                        // 'meja' => 'required|int',

                        'id_user' => 'required|int',

                        'id_produk' => 'required|int',

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

                    if($request->meja){

                        $meja = $request->meja;

                    }else{

                        $meja = 1;

                    }

                    $addktr = Keranjang::where('id',$request->id)

                                    ->where('id_toko',$request->id_toko)

                                    ->where('meja',$meja)

                                    ->where('id_user',$request->id_user)

                                    ->where('id_produk',$request->id_produk)

                                    ->first();

                    if($addktr){

                        if($addktr->delete()){

                            $messages = 'Data Item Keranjang Berhasil Dihapus';

                            $response =  fractal()

                                ->item($messages)

                                ->transformWith(new SuccessTransformer)

                                ->serializeWith(new ArraySerializer)        

                                ->toArray();

                            return response()->json($response, 200); 

                        }else{

                            $messages = 'Hapus Item Keranjang tidak berhasil, silahkan coba kembali!';

                            $status_code = 401;

                            $response =  fractal()

                                ->item($messages)

                                ->transformWith(new ErorrTransformer($status_code))

                                ->serializeWith(new ArraySerializer())

                                ->toArray();

                            return response()->json($response, 401);

                        }

                    }else{

                        $messages = 'Hapus Item Keranjang tidak berhasil, silahkan coba kembali!';

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

            throw new HttpException(500, "Hapus Item Keranjang tidak berhasil, silahkan coba kembali!");

        }



    }



}
