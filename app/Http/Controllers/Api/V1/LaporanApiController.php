<?php



namespace App\Http\Controllers\Api\V1;



use Illuminate\Http\Request;

use App\Http\Controllers\Controller;



use DB;

use Validator;

use Image;

use PDF;



use App\Models\Toko;

use App\Models\User;

use App\Models\Produk;

use App\Models\ProdukJenis;

use App\Models\ProdukKategori;

use App\Models\Keranjang;

use App\Models\Penjualan;

use App\Models\PenjualanDetail;

use App\Models\Laporan;

use App\Models\Beban;





use App\Transformers\ListLaporanTransformer;

use App\Transformers\ErorrTransformer;

use App\Transformers\SuccessTransformer;





use League\Fractal\Pagination\IlluminatePaginatorAdapter;

use Spatie\Fractalistic\ArraySerializer;

use App\Http\Controllers\Api\ArraySerializerV2;
use App\Models\hutang;
use App\Models\hutang_detail;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;







class LaporanApiController extends Controller

{



    public function list_laporan(Request $request){

        try {



            

            $ktr = Laporan::where('status','1')->paginate(10);

            if(count($ktr) > 0){

                $dtl = $ktr->getCollection();                    

                $success = true;

                $status_code = 200;

                $messages = 'Data Berhasil Ditampilkan!';

                $respone =  fractal()

                      ->collection($dtl, new ListLaporanTransformer, 'data')

                        ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))

                        ->paginateWith(new IlluminatePaginatorAdapter($ktr))

                        ->addMeta(['catatan'=> ''])

                        ->toArray();

                  return response()->json($respone, 200);

            }else{

                $dtl = $ktr->getCollection();

                $success = false;

                $status_code = 200;

                $messages = 'Tidak Ada Data Ditemukan!';        

                $respone =  fractal()

                      ->collection($dtl, new ListLaporanTransformer, 'data')

                        ->serializeWith(new ArraySerializerV2($success,$status_code,$messages))

                        ->paginateWith(new IlluminatePaginatorAdapter($ktr))

                        ->addMeta(['catatan'=> ''])

                        ->toArray();

                  return response()->json($respone, 200);

            }

            



        } catch (QueryException $ex) {

            throw new HttpException(500, "Gagal menampilkan data, coba lagi!");

        }



    }





    public function LaporanUmum(Request $request)

    {

        try{

            $id_toko = $request->id_toko;

            $cektoko = Toko::where('id',$id_toko)->first();

            if($cektoko){



                if( $request->date1 !='' &&  $request->date2 != ''){

                    $date1 = date("Y-m-d", strtotime($request->date1));

                    $date2 = date("Y-m-d", strtotime($request->date2));

                    $date = date("d-m-Y", strtotime($request->date1)) . ' s/d ' . date("d-m-Y", strtotime($request->date2));

                }else{

                    $date1 = $request->date1;

                    $date2 = $request->date2;

                    $date = '';

                }



                $penjualan = Penjualan::select(DB::raw('SUM(total) as total'))

                            ->where('id_toko',$id_toko)

                            ->where('status',1)

                         //   ->whereIn('metode_bayar',['0','1','2'])

                            ->whereDate('tgl_penjualan', '>=', $date1)

                            ->whereDate('tgl_penjualan', '<=', $date2)
                            
                            ->where('aktif', 'Y')

                            ->first();
                            

                $penjualan_cash = Penjualan::select(DB::raw('SUM(total) as total_cash'))

                             ->where('id_toko',$id_toko)

                             ->where('status',1)

                             ->whereDate('tgl_penjualan', '>=', $date1)
                             
                             ->where('aktif', 'Y')

                             ->whereDate('tgl_penjualan', '<=', $date2) ->first();



                 $penjualan_noncash = hutang::select(DB::raw('SUM(sisa_hutang) as total_hutang'))

                               ->where('id_toko',$id_toko)

                               ->where('status',2)

                               ->whereDate('tgl_hutang', '>=', $date1)
                               
                               ->where('aktif', 'Y')

                               ->whereDate('tgl_hutang', '<=', $date2) ->first();



                 if($date2 == Carbon::now()->format('Y-m-d')){

                        $hutang_dibayar_hari_ini = hutang_detail::select(DB::raw('SUM(bayar) as total_bayar_hari_ini'))

                        ->where('id_toko',$id_toko)

                        ->where('aktif','Y')

                        ->whereDate('tgl_bayar', Carbon::now())->first();
                    
                 }else{
                      $hutang_dibayar_hari_ini = '0';
                 }

                 
                
                    

               

             

                         



                $beban = Beban::select(DB::raw('SUM(jumlah) as jumlah'))

                            ->where('id_toko',$id_toko)

                            ->whereDate('tgl', '>=', $date1)

                            ->whereDate('tgl', '<=', $date2)
                            
                            ->where('aktif', 'Y')

                            ->first();


                $transaksi = Penjualan::

                            where('id_toko',$id_toko)

                            ->whereDate('tgl_penjualan', '>=', $date1)
                            
                            ->where('aktif', 'Y')

                            ->whereDate('tgl_penjualan', '<=', $date2) ->count();


                 $hutang = Penjualan::

                 where('id_toko',$id_toko)

                 ->where('status',2)

                 ->whereDate('tgl_penjualan', '>=', $date1)
                 
                 ->where('aktif', 'Y')

                 ->whereDate('tgl_penjualan', '<=', $date2) ->count();

                            
                $hutangtotal = hutang::select(DB::raw('SUM(sisa_hutang) as x'))

                    ->where('id_toko',$id_toko)

                    ->where('status', 2)
                    
                    ->where('aktif', 'Y')

                    ->whereDate('tgl_hutang', '>=', $date1)
                    

                    ->whereDate('tgl_hutang', '<=', $date2) ->first();


                // $kas = KasLaci::where('id_toko',$id_toko)->orderBy('tgl','DESC')->first();



                // $modal = DB::table('tbl_penjualan_detail as p')

                //         ->select(DB::raw('SUM(q.modal * p.qty) as modal'))

                //         ->leftJoin('tbl_hist_stok as q','p.id_hist_stok','=','q.id_stok')

                //         ->where('p.id_toko',$id_toko)

                //         ->whereDate('p.tgl', '>=', $date1)

                //         ->whereDate('p.tgl', '<=', $date2)

                //         ->first();



                // $laba = ($penjualan->total-$beban->jumlah)-$modal->modal;

                $laba = ($penjualan->total-$beban->jumlah);

                



                $data = (object) array(

                    'title'     => "LAPORAN UMUM",

                    'filter'    => (object) array(

                        'toko'    =>  $cektoko,

                        'date'    =>  $date,

                    ),

                    'hasil'    => (object) array(

                        'penjualan'    =>  $penjualan,

                        'beban'    =>  $beban,

                        'transaksi'=> $transaksi,

                        'hutang'=> $hutang,

                       // 'hutangtotal'=> $hutangtotal,


                

                        'laba'    =>  $laba,

                        'penjualan_cash' => $penjualan_cash,

                        'penjualan_noncash' => $penjualan_noncash,

                        'hutang_dibayar_hari_ini' => $hutang_dibayar_hari_ini,

                      

                       

                    ),

                );



                $pdf = PDF::loadView('laporan.api.laporanumum', compact('data'))->setPaper('A3','portrait');

                return $pdf->stream('LAPORAN UMUM.pdf');

               

            }else{

                return view('errors.missing');

            }

            

            

        }

        catch (QueryException $ex)

        {

            return abort(500);

        }

    }



    public function LaporanPenjualan(Request $request)

    {

        try{

            $id_toko = $request->id_toko;

            $cektoko = Toko::where('id',$id_toko)->first();

            if($cektoko){



                if( $request->date1 !='' &&  $request->date2 != ''){

                    $date1 = date("Y-m-d", strtotime($request->date1));

                    $date2 = date("Y-m-d", strtotime($request->date2));

                    $date = date("d-m-Y", strtotime($request->date1)) . ' s/d ' . date("d-m-Y", strtotime($request->date2));

                }else{

                    $date1 = $request->date1;

                    $date2 = $request->date2;

                    $date = '';

                }



                $penjualan = Penjualan::where('id_toko',$id_toko)

                            ->where('status',1)

                            //->whereIn('metode_bayar',['0','1','2'])

                            ->whereDate('tgl_penjualan', '>=', $date1)

                            ->whereDate('tgl_penjualan', '<=', $date2)
                            
                            ->where('aktif', 'Y')

                            ->with('detail')

                            ->get();

                



                $data = (object) array(

                    'title'     => "LAPORAN PENJUALAN",

                    'filter'    => (object) array(

                        'toko'    =>  $cektoko,

                        'date'    =>  $date,

                    ),

                    'hasil'    => (object) array(

                        'penjualan'    =>  $penjualan,

                    ),

                );



                $pdf = PDF::loadView('laporan.api.laporanpenjualan', compact('data'))->setPaper('A3','portrait');

                return $pdf->stream('LAPORAN PENJUALAN.pdf');

               

            }else{

                return view('errors.missing');

            }

            

            

        }

        catch (QueryException $ex)

        {

            return abort(500);

        }

    }



    public function LaporanBeban(Request $request)

    {

        try{

            $id_toko = $request->id_toko;

            $cektoko = Toko::where('id',$id_toko)->first();

            if($cektoko){



                if( $request->date1 !='' &&  $request->date2 != ''){

                    $date1 = date("Y-m-d", strtotime($request->date1));

                    $date2 = date("Y-m-d", strtotime($request->date2));

                    $date = date("d-m-Y", strtotime($request->date1)) . ' s/d ' . date("d-m-Y", strtotime($request->date2));

                }else{

                    $date1 = $request->date1;

                    $date2 = $request->date2;

                    $date = '';

                }



                $bebandata = Beban::where('id_toko',$id_toko)

                            ->whereDate('tgl', '>=', $date1)

                            ->whereDate('tgl', '<=', $date2)
                            
                            ->where('aktif', 'Y')

                            ->with('kategoribeban','kasir')

                            ->get();

                



                $data = (object) array(

                    'title'     => "LAPORAN BEBAN",

                    'filter'    => (object) array(

                        'toko'    =>  $cektoko,

                        'date'    =>  $date,

                    ),

                    'hasil'    => (object) array(

                        'bebandata'    =>  $bebandata,

                    ),

                );



                $pdf = PDF::loadView('laporan.api.laporanbeban', compact('data'))->setPaper('A3','portrait');

                return $pdf->stream('LAPORAN BEBAN.pdf');

               

            }else{

                return view('errors.missing');

            }

            

            

        }

        catch (QueryException $ex)

        {

            return abort(500);

        }

    }



    public function LaporanReversal(Request $request)

    {

        try{

            $id_toko = $request->id_toko;

            $cektoko = Toko::where('id',$id_toko)->first();

            if($cektoko){



                if( $request->date1 !='' &&  $request->date2 != ''){

                    $date1 = date("Y-m-d", strtotime($request->date1));

                    $date2 = date("Y-m-d", strtotime($request->date2));

                    $date = date("d-m-Y", strtotime($request->date1)) . ' s/d ' . date("d-m-Y", strtotime($request->date2));

                }else{

                    $date1 = $request->date1;

                    $date2 = $request->date2;

                    $date = '';

                }



                $penjualan = Penjualan::where('id_toko',$id_toko)

                            ->where('status',4)

                            //->whereIn('metode_bayar',['0','1','2'])

                            ->whereDate('tgl_penjualan', '>=', $date1)

                            ->whereDate('tgl_penjualan', '<=', $date2)
                            
                            //->where('aktif', 'N')

                            ->with('detail')

                            ->get();

                            

                



                $data = (object) array(

                    'title'     => "LAPORAN REVERSAL",

                    'filter'    => (object) array(

                        'toko'    =>  $cektoko,

                        'date'    =>  $date,

                    ),

                    'hasil'    => (object) array(

                        'penjualan'    =>  $penjualan,

                    ),

                );



                $pdf = PDF::loadView('laporan.api.laporanreversal', compact('data'))->setPaper('A3','portrait');

                return $pdf->stream('LAPORAN REVERSAL.pdf');

               

            }else{

                return view('errors.missing');

            }

            

            

        }

        catch (QueryException $ex)

        {

            return abort(500);

        }

    }

}
