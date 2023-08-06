@extends('laporan.api.layout.style')

@section('content')



<div>





    @if($data->filter->toko->logo != '-')
    <div class="container text-center"><img class="text-center" width="20%"
            src="data:image/png;base64,{{$data->filter->toko->logo}}" /></div>



    @else
    <div class="container text-center"><img class="text-center" width="20%"
            src="{{ asset('uploads/logo/default.png')}}" /></div>


    @endif
    <h3 class="text-center">{{$data->filter->toko->nama_toko}}</h3>

    <h6 class="text-center">{{$data->filter->toko->alamat}}</h6>
    <br>
    <h2 class="text-center">{{ $data->title }}</h2>
    <h6 class="text-center">Laporan tanggal : {{
        $data->filter->date }}</h6>


    <div>
        <table class="table table-striped">


            <thead style="background-color: rgb(10, 94, 191)">

                <tr>
                    <th scope="col" style="color: white">NO</th>
                    <th scope="col" style="color: white">KETERANGAN</th>
                    <th scope="col" class="text-right" style="color: white">JUMLAH</th>

                </tr>

                @php

                if($data->hasil->penjualan){

                $penjualan = $data->hasil->penjualan->total;

                }else{

                $penjualan = '0';

                }



                if($data->hasil->beban){

                $beban = $data->hasil->beban->jumlah;

                }else{

                $beban = '0';

                }

                if($data->hasil->transaksi){

                $transaksi = $data->hasil->transaksi;

                }else{

                $transaksi = '0';

                }

                if($data->hasil->hutang){

                $hutang = $data->hasil->hutang;

                }else{

                $hutang = '0';

                }


                // if($data->hasil->hutangtotal){

                // $hutangtotal = $data->hasil->hutangtotal->x;

                // }else{

                // $hutangtotal = '0';

                // }



                if($data->hasil->penjualan_cash){

                $penjualan_cash = $data->hasil->penjualan_cash->total_cash;

                }else{

                $penjualan_cash = '0';

                }


                if($data->hasil->penjualan_noncash){

                $penjualan_noncash = $data->hasil->penjualan_noncash->total_hutang;

                }else{

                $penjualan_noncash = '0';

                }


                if($data->hasil->hutang_dibayar_hari_ini){

                $hutang_dibayar_hari_ini = $data->hasil->hutang_dibayar_hari_ini->total_bayar_hari_ini;

                }else{

                $hutang_dibayar_hari_ini = '0';

                }







                // if($data->hasil->modal){

                // $modal = $data->hasil->modal->modal;

                // }else{

                // $modal = '0';

                // }



                // if($data->hasil->laba){

                // $laba = $data->hasil->laba;

                // }else{

                // $laba = '0';

                // }

                @endphp
            </thead>
            <tbody>

                {{-- <tr class="item">

                    <td align="left" colspan="2">Total Penjualan</td>

                    <td style="text-align: right;">{{'Rp. '.number_format($penjualan)}}</td>

                </tr> --}}

                <tr class="item">

                    <td align="left" colspan="2">Total Transaksi</td>

                    <td style="text-align: right;">{{number_format($transaksi)}}</td>

                </tr>
                <tr class="item">

                    <td align="left" colspan="2">Total Transaksi Hutang</td>

                    <td style="text-align: right;">{{number_format($hutang)}}</td>

                </tr>

                <tr class="item">

                    <td align="left" colspan="2">Total Penjualan Lunas</td>

                    <td style="text-align: right;">{{'Rp. '.number_format($penjualan_cash)}}</td>

                </tr>

                <tr class="item">

                    <td align="left" colspan="2">Total Penjualan Belum Lunas</td>

                    <td style="text-align: right;">{{'Rp. '.number_format($penjualan_noncash)}}</td>

                </tr>


                <tr class="item">

                    <td align="left" colspan="2">Total Hutang yang Dibayar Hari Ini</td>

                    <td style="text-align: right;">{{'Rp. '.number_format($hutang_dibayar_hari_ini)}}</td>

                </tr>

                <tr class="item">

                    <td align="left" colspan="2">Total Beban</td>

                    <td style="text-align: right;">{{'Rp. '.number_format($beban)}}</td>

                </tr>

                {{-- <tr class="item">

                    <td align="left" colspan="2">Total Hutang</td>

                    <td style="text-align: right;">{{'Rp. '.number_format($hutangtotal)}}</td>

                </tr> --}}
                <tr class="item">

                    <td align="left" colspan="2">Total Pendapatan</td>

                    <td style="text-align: right;">{{'Rp.
                        '.number_format($penjualan_cash-$penjualan_noncash+$hutang_dibayar_hari_ini)}}</td>

                </tr>

            </tbody>
        </table>
    </div>
</div>

@endsection