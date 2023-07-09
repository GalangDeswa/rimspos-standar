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
    <h6 class="text-center">Beban tanggal : {{
        $data->filter->date }}</h6>



    <table class="table table-striped">

        <thead style="background-color: rgb(10, 94, 191)">

            <tr>
                <th scope="col" style="color: white">NO</th>
                <th scope="col" style="color: white">TANGGAL</th>
                <th scope="col" style="color: white">BEBAN</th>
                <th scope="col" style="color: white">KETERANGAN</th>
                <th scope="col" class="text-right" style="color: white">JUMLAH</th>
                <th scope="col" style="color: white">DIBUAT OLEH</th>

            </tr>

            @php

            $no = 1;

            $totalSum = 0;

            @endphp


        </thead>
        <tbody>
            @if(count($data->hasil->bebandata) > 0)

            @foreach($data->hasil->bebandata as $item)



            <tr class="item">

                <td align="center">{{ $no++ }}</td>

                <td align="left">{{ date("d-m-Y",strtotime($item->tgl)) }}</td>

                <td align="left">{{$item->nama}}</td>

                <td align="left">{{$item->keterangan}}</td>

                <td align="right">{{'Rp. '.number_format($item->jumlah)}}</td>

                <td align="left">{{ ($item->kasir) ? $item->kasir->name : "" }}</td>

            </tr>

            @php



            $totalSum += $item->jumlah;

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