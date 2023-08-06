@extends('laporan.api.layout.style')

@section('content')



<div>

    @if($data->filter->toko->logo != '-')
    <div class="container text-center"><img class="text-center" width="20%"
            src="data:image/png;base64,{{$data->filter->toko->logo}}" /></div>



    @else
    <div class="container text-center"><img class="text-center" width="20%"
            src="{{ asset('uploads/logo/default.png')}}" />
    </div>


    @endif
    <h3 class="text-center">{{$data->filter->toko->nama_toko}}</h3>

    <h6 class="text-center">{{$data->filter->toko->alamat}}</h6>
    <br>
    <h2 class="text-center">{{ $data->title }}</h2>
    <h6 class="text-center">Reversal tanggal : {{
        $data->filter->date }}</h6>


    <table class="table table-striped">

        <thead style="background-color: rgb(10, 94, 191)">

            <tr>
                <th scope="col" style="color: white">NO</th>
                <th scope="col" style="color: white">TANGGAL TRANSAKSI</th>
                <th scope="col" style="color: white">NAMA BARANG</th>
                <th scope="col" style="color: white" class="text-right">QTY</th>
                <th scope="col" style="color: white" class="text-right">DISKON</th>
                <th scope="col" style="color: white" class="text-right">TOTAL</th>

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

                <td align="left">{{ date("d-m-Y",strtotime($item->tgl_penjualan)) }}</td>

                <td align="left">{{$itemdetail->nama_brg}}</td>

                <td align="center">{{$itemdetail->qty}}</td>

                <td align="right">{{'Rp. '.number_format($itemdetail->diskon_brg)}}</td>

                <td align="right">{{'Rp. '.number_format($itemdetail->total)}}</td>

            </tr>

            @endforeach

            @endif

            @php



            //$totalSum += $item->total;

            @endphp

            @endforeach



            {{-- <tr class="item">

                <td align="right" colspan="5">Total</td>

                <td style="text-align: right;">{{number_format($totalSum)}}</td>

            </tr> --}}



            @endif
        </tbody>
    </table>

</div>

@endsection