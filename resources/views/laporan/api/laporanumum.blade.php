@extends('laporan.api.layout.style')

@section('content')



<div>

    <div class="row mb-3 align-self-center">
        <div class="col-12">
            <table>
                <tr class="mr-2">

                    @if($data->filter->toko->logo != '-')

                    <td rowspan="4" width="80px" style="text-align:center;vertical-align: middle;"><img
                            style="display: block;margin:-32px;" width="100%"
                            src="data:image/png;base64,{{$data->filter->toko->logo}}" /></td>

                    @else

                    <td rowspan="4" width="80px" style="text-align:center;vertical-align: middle;"><img
                            style="display: block;margin:-32px;" width="100%"
                            src="{{ asset('uploads/logo/default.png')}}" /></td>

                    @endif



                    <td style="text-align:left;">{{ $data->filter->toko->nama_toko }}</td>

                </tr>

                <tr>

                    <td style="text-align:left;">Alamat: {{ $data->filter->toko->alamat }}</td>

                </tr>

                <tr>

                    <td style="text-align:left;">Email: {{ $data->filter->toko->email }}</td>

                </tr>
            </table>
        </div>

    </div>

    <h3 class="text-center">{{ $data->title }}</h3>
    <h6 class="text-center">Laporan tanggal : {{
        $data->filter->date }}</h6>



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


            if($data->hasil->hutangtotal){

            $hutangtotal = $data->hasil->hutangtotal->x;

            }else{

            $hutangtotal = '0';

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

            <tr class="item">

                <td align="left" colspan="2">Penjualan Total</td>

                <td style="text-align: right;">{{'Rp. '.number_format($penjualan)}}</td>

            </tr>

            <tr class="item">

                <td align="left" colspan="2">Beban Total</td>

                <td style="text-align: right;">{{'Rp. '.number_format($beban)}}</td>

            </tr>
            <tr class="item">

                <td align="left" colspan="2">Transaksi Total</td>

                <td style="text-align: right;">{{number_format($transaksi)}}</td>

            </tr>
            <tr class="item">

                <td align="left" colspan="2">Transaksi hutang</td>

                <td style="text-align: right;">{{number_format($hutang)}}</td>

            </tr>
            <tr class="item">

                <td align="left" colspan="2">Hutang total</td>

                <td style="text-align: right;">{{'Rp. '.number_format($hutangtotal)}}</td>

            </tr>
            <tr class="item">

                <td align="left" colspan="2">Pendapatan Total</td>

                <td style="text-align: right;">{{'Rp. '.number_format($penjualan-$beban-$hutangtotal)}}</td>

            </tr>

        </tbody>
    </table>

</div>

@endsection