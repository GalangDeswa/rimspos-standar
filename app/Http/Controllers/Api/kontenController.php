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
}
