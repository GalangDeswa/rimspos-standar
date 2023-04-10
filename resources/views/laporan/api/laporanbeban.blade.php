@extends('laporan.api.layout.style')
@section('content')
    
<main class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        
        @include('laporan.api.layout.kop')

        <tr class="information">
            <td colspan="6">
                <table>
                    <tr>
                        <td>
                            {{ $data->title }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="6">
                <hr>
            </td>
        </tr>
        <tr class="heading">
            <td colspan="6" style="text-align: left;vertical-align: middle;">Tanggal Penjualan: {{ $data->filter->date }}</td>
        </tr>
        <tr class="heading">
            <td width="2%" style="text-align: center;vertical-align: middle;background: #334868;color: #FFF;">NO</td>
            <td width="30%" style="text-align: left;vertical-align: middle;background: #334868;color: #FFF;">TANGGAL</td>
            <td width="30%" style="text-align: left;vertical-align: middle;background: #334868;color: #FFF;">BEBAN</td>
            <td width="30%" style="text-align: left;vertical-align: middle;background: #334868;color: #FFF;">KETERANGAN</td>
            <td width="30%" style="text-align: right;vertical-align: middle;background: #334868;color: #FFF;">JUMLAH</td>
            <td width="30%" style="text-align: left;vertical-align: middle;background: #334868;color: #FFF;">CREATED BY</td>
        </tr>

        @php
            $no = 0;
            $totalSum = 0;
        @endphp

        @if(count($data->hasil->bebandata) > 0)
            @foreach($data->hasil->bebandata as $item)
                
                <tr class="item">
                    <td align="center">{{ $no++ }}</td>
                    <td align="left">{{ date("d-m-Y",strtotime($item->tgl)) }}</td>
                    <td align="left">{{$item->nama}}</td>
                    <td align="left">{{$item->keterangan}}</td>
                    <td align="right">{{number_format($item->jumlah)}}</td>
                    <td align="left">{{ ($item->kasir) ? $item->kasir->name : "" }}</td>
                </tr>
            @php

                $totalSum += $item->jumlah;
            @endphp
            @endforeach

            <tr class="item">
                <td align="right" colspan="4">Total</td>
                <td style="text-align: right;">{{number_format($totalSum)}}</td>
            </tr>

        @endif


        
           

    </table>
</main>
@endsection