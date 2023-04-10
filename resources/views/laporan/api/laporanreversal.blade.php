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
            <td colspan="6" style="text-align: left;vertical-align: middle;">Tanggal Reversal: {{ $data->filter->date }}</td>
        </tr>
        <tr class="heading">
            <td width="2%" style="text-align: center;vertical-align: middle;background: #334868;color: #FFF;">NO</td>
            <td width="30%" style="text-align: left;vertical-align: middle;background: #334868;color: #FFF;">TANGGAL TRANSAKSI</td>
            <td width="30%" style="text-align: left;vertical-align: middle;background: #334868;color: #FFF;">NAMA BARANG</td>
            <td width="30%" style="text-align: center;vertical-align: middle;background: #334868;color: #FFF;">QTY</td>
            <td width="30%" style="text-align: right;vertical-align: middle;background: #334868;color: #FFF;">DISKON</td>
            <td width="30%" style="text-align: right;vertical-align: middle;background: #334868;color: #FFF;">TOTAL</td>
        </tr>

        @php
            $no = 0;
            $totalSum = 0;
        @endphp

        @if(count($data->hasil->penjualan) > 0)
            @foreach($data->hasil->penjualan as $item)
                @if($item->detail)
                    @foreach($item->detail as $itemdetail)
                        <tr class="item">
                            <td align="center">{{ $no++ }}</td>
                            <td align="left">{{ date("d-m-Y",strtotime($item->tgl_penjualan)) }}</td>
                            <td align="left">{{$itemdetail->nama_brg}}</td>
                            <td align="center">{{$itemdetail->qty}}</td>
                            <td align="right">{{$itemdetail->diskon_brg}}</td>
                            <td align="right">{{number_format($itemdetail->total)}}</td>
                        </tr>
                    @endforeach
                @endif
            @php

                $totalSum += $item->total;
            @endphp
            @endforeach

            <tr class="item">
                <td align="right" colspan="5">Total</td>
                <td style="text-align: right;">{{number_format($totalSum)}}</td>
            </tr>

        @endif


        
           

    </table>
</main>
@endsection