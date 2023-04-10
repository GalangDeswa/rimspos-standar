<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group(['prefix' => 'auth','namespace' => 'App\Http\Controllers\Api'], function ($api) {
       $api->post('login',  'AuthApiController@login')->name('login');
       $api->post('logout',  'AuthApiController@logout');
       $api->post('refresh_token',  'AuthApiController@refresh');
       $api->post('me',  'AuthApiController@me');
       $api->post('verify_token',  'AuthApiController@verify_token');
     });

    $api->group(['middleware' => 'auth:api','namespace' => 'App\Http\Controllers\Api\V1'], function ($api) {
        $api->post('loadtoko', 'HomeApiController@loadtoko');

        $api->group(['prefix' => 'user'], function ($api) {
            ////////USER TOKO-----------------------------
            $api->post('data', 'UserTokoApiController@datakasir');
            $api->post('tambah', 'UserTokoApiController@tambahkasir');
            $api->post('edit', 'UserTokoApiController@editkasir');
            $api->post('hapus', 'UserTokoApiController@hapuskasir');
            $api->post('gantipass', 'UserTokoApiController@gantipasskasir');
        });

        $api->group(['prefix' => 'produk/jenis'], function ($api) {
            ////////JENIS PRODUK-----------------------------
            $api->post('data', 'KategoriApiController@datajenis');
            $api->post('tambah', 'KategoriApiController@tambah_jenis');
            $api->post('edit', 'KategoriApiController@edit_jenis');
            $api->post('hapus', 'KategoriApiController@hapus_jenis');
        });

        $api->group(['prefix' => 'beban/kategori'], function ($api) {
            ////////KATEGORI BEBAN-----------------------------
            $api->post('data', 'KategoriApiController@datakategoribeban');
            $api->post('tambah', 'KategoriApiController@tambah_kategoribeban');
            $api->post('edit', 'KategoriApiController@edit_kategoribeban');
            $api->post('hapus', 'KategoriApiController@hapus_kategoribeban');
        });

        $api->group(['prefix' => 'beban'], function ($api) {
            ////////BEBAN-----------------------------
            $api->post('data/hariini', 'BebanApiController@databeban_hariini');
            $api->post('data', 'BebanApiController@databeban');
            $api->post('tambah', 'BebanApiController@tambah_beban');
            $api->post('edit', 'BebanApiController@edit_beban');
            $api->post('hapus', 'BebanApiController@hapus_beban');
        });

        $api->group(['prefix' => 'produk'], function ($api) {
            ////////PRODUK-----------------------------
            $api->post('data/allproduk', 'ProdukApiController@data_produk_all');
            $api->post('data/byjenis', 'ProdukApiController@data_produk_byjenis');
            $api->post('tambah', 'ProdukApiController@tambah_produk');
            $api->post('edit', 'ProdukApiController@edit_produk');
            $api->post('hapus', 'ProdukApiController@hapus_produk');
            $api->post('tambah/stock', 'ProdukApiController@tambahstock_produk');
        });

        $api->group(['prefix' => 'kasir'], function ($api) {
            $api->group(['prefix' => 'keranjang'], function ($api) {
                ////////Keranjang-----------------------------
                $api->post('data', 'KeranjangApiController@data_keranjang');
                $api->post('tambah', 'KeranjangApiController@tambah_keranjang');
                $api->post('hapus', 'KeranjangApiController@hapus_item_keranjang');

                $api->post('pembayaran', 'PembayaranApiController@pembayaran');
            });
            
        });

        $api->group(['prefix' => 'penjualan'], function ($api) {
            ////////Penjualan-----------------------------
            $api->post('data', 'PenjualanApiController@riwayat_penjualan');
            $api->post('data/detail', 'PenjualanApiController@penjualan_detail');
            $api->post('data/hariini', 'PenjualanApiController@reversal_data');

            $api->post('reversal', 'PenjualanApiController@reversal_aksi');
            
        });


        $api->group(['prefix' => 'laporan'], function ($api) {
            ////////LAPORAN-----------------------------
            $api->post('list', 'LaporanApiController@list_laporan');
            $api->post('umum', 'LaporanApiController@LaporanUmum');
            $api->post('penjualan', 'LaporanApiController@LaporanPenjualan');
            $api->post('beban', 'LaporanApiController@LaporanBeban');
            $api->post('reversal', 'LaporanApiController@LaporanReversal');
            
        });
    });

    
}); 