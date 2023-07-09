@extends('laporan.api.layout.style')

@section('content')



<div>

    <div class="row mb-3 align-self-center">
        <div class="col-12">
            <table>
                <tr class="mr-2">

                    @if($data->filter->toko->logo != '')

                    <td rowspan="4" width="80px" style="text-align:center;vertical-align: middle;"><img
                            style="display: block;margin:-32px;" width="100%"
                            src="{{ asset($data->filter->toko->logo)}}" /></td>

                    @else

                    <td rowspan="4" width="80px" style="text-align:center;vertical-align: middle;"><img
                            style="display: block;margin:-32px;" width="100%" src="{{ asset('LOGO.png')}}" /></td>

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
    <h6 class="text-center">Tanggal Penjualan : {{
        $data->filter->date }}</h6>



    <table class="table table-striped">

        <thead style="background-color: rgb(10, 94, 191)">

            <tr>
                <th scope="col" style="color: white">NO</th>
                <th scope="col" style="color: white">NAMA BARANG</th>
                <th scope="col" style="color: white">QTY</th>
                <th scope="col" style="color: white">DISKON</th>

                <th scope="col" class="text-right" style="color: white">TOTAL</th>

            </tr>


            @php

            $no = 1;

            $totalSum = 0;

            @endphp





        </thead>
        <tbody>
            @if(count($data->hasil->penjualan) > 0)

            @foreach($data->hasil->penjualan as $item)

            @if($item->detail)

            @foreach($item->detail as $itemdetail)

            <tr class="item">

                <td align="center">{{ $no++ }}</td>

                <td align="left">{{$itemdetail->nama_brg}}</td>

                <td align="center">{{$itemdetail->qty}}</td>

                <td align="right">{{'Rp. '. number_format($itemdetail->diskon_brg) }}</td>

                <td align="right">{{'Rp. '.number_format(($itemdetail->harga_brg *
                    $itemdetail->qty)-$itemdetail->diskon_brg)}}
                </td>

            </tr>

            @endforeach

            @endif

            @php



            $totalSum += $item->total;

            @endphp

            @endforeach



            <tr class="item">

                <td align="right" colspan="4">Total</td>

                <td style="text-align: right;">{{'Rp. '.number_format($totalSum)}}</td>

            </tr>



            @endif
        </tbody>
    </table>

</div>

@endsection