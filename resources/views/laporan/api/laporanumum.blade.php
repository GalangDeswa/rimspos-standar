@extends('laporan.api.layout.style')
@section('content')
    
<main class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        
        @include('laporan.api.layout.kop')

        <tr class="information">
            <td colspan="3">
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
            <td colspan="3">
                <hr>
            </td>
        </tr>
        
        <tr class="heading">
            <td width="2%" style="text-align: center;vertical-align: middle;background: #334868;color: #FFF;">NO</td>
            <td width="30%" style="text-align: center;vertical-align: middle;background: #334868;color: #FFF;">KETERANGAN</td>
            <td width="30%" style="text-align: center;vertical-align: middle;background: #334868;color: #FFF;">AMOUNT</td>
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

            

            // if($data->hasil->modal){
            //     $modal = $data->hasil->modal->modal;
            // }else{
            //     $modal = '0';
            // }

            if($data->hasil->laba){
                $laba = $data->hasil->laba;
            }else{
                $laba = '0';
            }
        @endphp
        <tr class="item">
            <td align="center">1</td>
            <td align="left">Penjualan</td>
            <td align="right">{{number_format($penjualan)}}</td>
        </tr>
        <tr class="item">
            <td align="left" colspan="2">Penjualan Total</td>
            <td style="text-align: right;">{{number_format($penjualan)}}</td>
        </tr>

        <tr class="item">
            <td align="center">2</td>
            <td align="left">Beban</td>
            <td align="right">{{number_format($beban)}}</td>
        </tr>
        <tr class="item">
            <td align="left" colspan="2">Beban Total</td>
            <td style="text-align: right;">{{number_format($beban)}}</td>
        </tr>

        <tr class="item">
            <td align="left" colspan="2">Pendapatan Total</td>
            <td style="text-align: right;">{{number_format($penjualan-$beban)}}</td>
        </tr>

        

        {{-- <tr class="item">
            <td align="center">3</td>
            <td align="left">Modal</td>
            <td align="right">{{number_format($modal)}}</td>
        </tr> --}}

        <tr class="item">
            <td align="left" colspan="2">Laba/Rugi Total</td>
            <td style="text-align: right;">{{number_format($laba)}}</td>
        </tr>
           

    </table>
</main>
@endsection