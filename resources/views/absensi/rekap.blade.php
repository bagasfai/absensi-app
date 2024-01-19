@extends('layouts.admin.tabler')

@section('content')

<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <!-- Page pre-title -->
        <h2 class="page-title">
          Rekap Absen
        </h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-6">
                <div class="card">
                  <div class="card-body">
                    <form action="{{route('absen.cetakrekap')}}" method="POST" target="_blank">
                      @csrf

                      <div class="row">
                        <div class="col-12">
                          <div class="form-group">
                            <select name="bulan" id="bulan" class="form-select">
                              <option value="">Bulan</option>
                              @for($i=1;$i<=12; $i++) <option value="{{$i}}" {{ date("m") == $i ? 'selected' : '' }}>{{ $namabulan[$i] }}</option>
                                @endfor
                            </select>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-12 mt-2">
                          <div class="form-group">
                            <select name="tahun" id="tahun" class="form-select">
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

                      <div class="row">
                        <div class="col-6 mt-2">
                          <div class="form-group">
                            <button type="submit" class="btn btn-primary w-100" name="cetak"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                                <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                                <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>Cetak</button>
                          </div>
                        </div>

                        <div class="col-6 mt-2">
                          <div class="form-group">
                            <button type="submit" class="btn btn-success w-100" name="exportExcel"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                <path d="M7 11l5 5l5 -5" />
                                <path d="M12 4l0 12" /></svg>Export to Excel</button>
                          </div>
                        </div>
                      </div>

                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push("myscript")
<script>
  $("#frmLaporan").submit(function(e) {
    var bulan = $("#bulan").val();
    var tahun = $("#tahun").val();

    if (bulan == "") {
      Swal.fire({
        title: 'Warning!'
        , text: 'Bulan harus di pilih!'
        , icon: 'warning'
        , confirmButtonText: 'Ok'
      }).then((result) => {
        $("#bulan").focus();
      });
      return false;
    } else if (tahun == "") {
      Swal.fire({
        title: 'Warning!'
        , text: 'Tahun harus di pilih!'
        , icon: 'warning'
        , confirmButtonText: 'Ok'
      }).then((result) => {
        $("#tahun").focus();
      });
      return false;
    }

  })

</script>
@endpush
