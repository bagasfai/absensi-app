@extends('layouts.app')

@section('content')
<div class="grid grid-rows-1 p-1">
  <div class="col">
    <div class="rows">
      <div class="col-12">
        <div class="form-group">
          <select name="bulan" id="bulan" class="form-control">
            <option value="">Bulan</option>
            @for($i=1;$i<=12; $i++) <option value="{{$i}}" {{ date("m") == $i ? 'selected' : '' }}>{{ $namabulan[$i] }}</option>
              @endfor
          </select>
        </div>
      </div>
    </div>
    <div class="rows">
      <div class="col-12">
        <div class="form-group">
          <select name="tahun" id="tahun" class="form-control">
            <option value="">Tahun</option>
            @php
            $tahunmulai = 2023;
            $tahunsekarang = date("Y");
            @endphp
            @for($tahun = $tahunmulai; $tahun <= $tahunsekarang; $tahun++) <option value="{{$tahun}}" {{ date("Y") == $tahun ? 'selected' : '' }}>{{$tahun}}</option> @endfor
          </select>
        </div>
      </div>
    </div>
    <div class="rows block">
      <div class="rows">
        <div class="form-group">
          <button class="btn btn-primary block w-full" id="getdata">
            <ion-icon name="search-outline"></ion-icon> Search
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="grid grid-rows1">
  <div class="cols" id="showhistori">
  </div>
</div>
@endsection

@push('myscript')
<script>
  $(document).ready(function() {
    $("#getdata").click(function(e) {
      var bulan = $("#bulan").val();
      var tahun = $("#tahun").val();

      $.ajax({
        type: 'POST'
        , url: '/gethistori'
        , data: {
          _token: "{{ csrf_token() }}"
          , bulan: bulan
          , tahun: tahun,

        }
        , cache: false
        , success: function(respond) {
          $("#showhistori").html(respond);
        }
      })
    });
  });

</script>
@endpush
