<tr class="top">

  <td colspan="12">

    <table width="100%" style="border-bottom: 1px solid #000000">

      <tr>

        @if($data->filter->toko->logo != null)

        <td rowspan="4" width="80px" style="text-align:center;vertical-align: middle;"><img
            style="display: block;margin:-32px;" width="100%" src="{{ asset($data->filter->toko->logo)}}" /></td>

        @else

        <td rowspan="4" width="80px" style="text-align:center;vertical-align: middle;"><img
            style="display: block;margin:-32px;" width="100%" src="{{asset('uploads/logo/default.png')}}" /></td>

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

  </td>


</tr>