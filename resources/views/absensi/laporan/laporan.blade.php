@extends('layouts.admin.tabler')

@section('content')

<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <!-- Page pre-title -->
        <h2 class="page-title">
          Laporan Absen
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
              <div class="col-12">
                <div class="card">
                  <div class="card-body">
                    <form action="{{route('absen.cetaklaporan')}}" id="frmLaporan" method="POST" target="_blank">
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
                        <div class="col-12 mt-2">
                          <div class="form-group">
                            <select name="email" id="email" class="form-select">
                              <option value="">Karyawan</option>
                              @foreach ($user as $u)
                              <option value="{{$u->email}}">{{$u->nama}}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-4 mt-2">
                          <div class="form-group">
                            <button class="btn btn-primary w-100" name="preview" id="preview"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>Preview</button>
                          </div>
                        </div>

                        <div class="col-4 mt-2">
                          <div class="form-group">
                            <button type="submit" class="btn btn-primary w-100" name="cetak"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                                <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                                <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>Cetak</button>
                          </div>
                        </div>

                        <div class="col-4 mt-2">
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

                    <div class="mt-4">

                      <div id="previewData" style="display: none;">
                        <h3>Preview Data:</h3>
                        <table class="table" id="dataTable">
                          <thead>
                            <tr>
                              <th>Tanggal</th>
                              <th>Jam Masuk</th>
                              <th>Laporan Masuk</th>
                              <th>Foto Masuk</th>
                              <th>Jam Keluar</th>
                              <th>Laporan Keluar</th>
                              <th>Foto Keluar</th>
                              <th>Total Jam Kerja</th>
                              <!-- Add more table headers if needed -->
                            </tr>
                          </thead>
                          <tbody id="previewTableBody">
                            <!-- Preview data will be inserted here -->
                          </tbody>
                        </table>
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
  </div>
</div>

@endsection

@push("myscript")
<script>
  $(document).ready(function() {
    // Initialize DataTable instance when the page loads
    let dataTableInstance = $('#dataTable').DataTable();

    $('#preview').on('click', function(e) {
      e.preventDefault();
      let email = $('#email').val();
      let bulan = $('#bulan').val();
      let tahun = $('#tahun').val();

      console.log('Email: ', email, 'Bulan', bulan, 'Tahun: ', tahun);

      // Check if email, bulan, and tahun are selected
      if (bulan == "" || tahun == "" || email == "") {
        Swal.fire({
          title: 'Warning!'
          , text: 'Email, Bulan, Tahun harus diisi!'
          , icon: 'warning'
          , confirmButtonText: 'Ok'
        }).then((result) => {
          $("#bulan").focus();
        });
        return false;
      }

      fetchPreviewData(email, bulan, tahun);
    });

    function fetchPreviewData(email, bulan, tahun) {
      $.ajax({
        url: "{{ route('absen.previewlaporan') }}"
        , type: 'POST'
        , data: {
          _token: "{{ csrf_token() }}"
          , email: email
          , bulan: bulan
          , tahun: tahun
        }
        , dataType: 'json'
        , success: function(data) {
          let previewTableBody = [];
          $.each(data, function(index, row) {
            previewTableBody.push([
              row.tanggal
              , row.jam_masuk
              , row.laporan_masuk
              , `<img src="uploads/absensi/${row.foto_masuk}" alt="Foto Masuk">`
              , row.jam_keluar
              , row.laporan_keluar
              , `<img src="uploads/absensi/${row.foto_keluar}" alt="Foto Keluar">`
              , row.total_time
            ]);
          });

          // Clear the existing DataTable rows and add the new rows
          dataTableInstance.clear().rows.add(previewTableBody).draw();

          $('#previewData').show();
        }
        , error: function(xhr, status, error) {
          console.error('Error fetching preview data:', error);
        }
      });
    }


  });

  $("#frmLaporan").submit(function(e) {
    let bulan = $("#bulan").val();
    let tahun = $("#tahun").val();
    let email = $("#email").val();

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
    } else if (email == "") {
      Swal.fire({
        title: 'Warning!'
        , text: 'Email harus diisi!'
        , icon: 'warning'
        , confirmButtonText: 'Ok'
      }).then((result) => {
        $("#email").focus();
      });
      return false;
    }

  })

</script>
@endpush
