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
              <div class="col-12">
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
                        <div class="table-responsive">
                          <table class="table table-bordered" id="dataTable">
                            <thead>
                              <tr>
                                <th rowspan="3" class="text-center align-middle">Email</th>
                                <th rowspan="3" class="text-center align-middle">Nama</th>
                                <th class="text-center tanggal">Tanggal</th>
                                <th rowspan="3">TM</th>
                                <th rowspan="3">TK</th>
                                <th rowspan="3">TI</th>
                                <th rowspan="3">TS</th>
                                <th rowspan="3">TA</th>
                                <th rowspan="3">TH</th>
                                <th rowspan="3">TJK</th>
                              </tr>
                              <tr id="dateHeaders">
                                <!-- Date headers will be dynamically inserted here -->
                              </tr>
                              <tr id="dateHeaders2">
                                <!-- Date headers will be dynamically inserted here -->
                              </tr>
                            </thead>
                            <tbody id="previewTableBody">
                              <!-- Preview data will be dynamically inserted here -->
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
</div>

@endsection

@push("myscript")
<script>
  $(document).ready(function() {
    $('#preview').on('click', function(e) {
      e.preventDefault();
      let bulan = $('#bulan').val();
      let tahun = $('#tahun').val();
      let totalDays = getTotalDaysInMonth(tahun, bulan);
      let total = totalDays * 3;

      $('#dataTable .tanggal').attr('colspan', total);

      // Check if bulan, and tahun are selected
      if (bulan == "" || tahun == "") {
        Swal.fire({
          title: 'Warning!'
          , text: 'Bulan dan Tahun harus diisi!'
          , icon: 'warning'
          , confirmButtonText: 'Ok'
        }).then((result) => {
          $("#bulan").focus();
        });
        return false;
      }

      $.ajax({
        url: "{{ route('absen.previewrekap') }}"
        , type: 'POST'
        , data: {
          _token: "{{ csrf_token() }}"
          , bulan: bulan
          , tahun: tahun
        }
        , dataType: 'json'
        , success: function(data) {
          let previewTableBody = $('#previewTableBody');
          let dateHeadersRow = $('#dateHeaders');
          let dateHeadersRow2 = $('#dateHeaders2');

          // Clear previous data
          previewTableBody.empty();
          dateHeadersRow.empty();
          dateHeadersRow2.empty();

          // Append date headers
          for (let i = 1; i <= totalDays; i++) {
            dateHeadersRow.append(
              `<th colspan="3" class="text-center">${i}</th>`
            );
            dateHeadersRow2.append(
              `<th>Masuk</th>
              <th>Keluar</th>
              <th>Jam Kerja</th>`
            );
          }

          // Append table data
          $.each(data, function(index, employee) {
            let html = `<tr>
                        <td>${employee.email}</td>
                        <td>${employee.nama}</td>`;

            let totalhadir = 0; // Initialize total attendance counter
            let totalmasuk = 0;
            let totalkeluar = 0;
            let totalizin = 0;
            let totalsakit = 0;
            let totalalpha = 0;
            let total_time_month = employee.total_hours_month + " Jam " + employee.total_minutes_month + " Menit";

            for (let i = 1; i <= totalDays; i++) {
              let tgl = "tgl_" + i;
              let thour = "total_hours_" + i;
              let tmin = "total_minutes_" + i;
              let hadir = ['', ''];
              let tanggal = employee[tgl];
              let total_time;

              if (employee[thour] !== null && employee[thour] > 0) {
                total_time = employee[thour] + " Jam " + employee[tmin] + " Menit";
              } else {
                total_time = '-';
              }

              let cekConcat;

              if (tanggal !== undefined && tanggal !== null) {
                cekConcat = tanggal.indexOf('-');
              } else {
                cekConcat = -1; // Set cekConcat to -1 if tanggal is undefined or null
              }

              if (tanggal !== undefined && tanggal !== null) {
                // Check if $d->tgl contains '-' to determine if it's concatenated
                if (cekConcat != -1) {
                  hadir = tanggal.split("-");
                  // If it's concatenated, count as Masuk and Keluar
                  if (hadir[0] != '') {
                    totalmasuk++;
                    totalhadir++;
                  }

                  if (hadir[1] != '') {
                    totalkeluar++;
                  }
                } else {
                  // If it's a single value, count based on its type
                  switch (tanggal) {
                    case 'I':
                      hadir[0] = tanggal;
                      hadir[1] = tanggal;
                      totalizin++;
                      totalhadir++;
                      break;
                    case 'S':
                      hadir[0] = tanggal;
                      hadir[1] = tanggal;
                      totalsakit++;
                      totalhadir++;
                      break;
                    case 'LIBUR':
                      hadir[0] = tanggal;
                      hadir[1] = tanggal;
                      total_time = 'LIBUR'
                      break;
                    default:
                      totalalpha++;
                      break;
                  }
                }
              }

              html += `<td>${hadir[0]}</td>`;
              html += `<td>${hadir[1]}</td>`;
              html += `<td>${total_time}</td>`;
            }
            html +=
              `
            <td>${totalmasuk}</td>
            <td>${totalkeluar}</td>
            <td>${totalizin}</td>
            <td>${totalsakit}</td>
            <td>${totalalpha}</td>
            <td>${totalhadir}</td>
            <td>${total_time_month}</td>
            
            </tr>`;
            previewTableBody.append(html);
          });

          $('#previewData').show();
        }
        , error: function(xhr, status, error) {
          console.error('Error fetching preview data:', error);
        }
      });

      // fetchPreviewData(bulan, tahun);
    });

    function getTotalDaysInMonth(year, month) {
      // Months in JavaScript are 0-based (January is 0, February is 1, etc.)
      // So, we subtract 1 from the month number provided by the user
      // Then, we create a new Date object for the first day of the next month
      // and set its day to 0, which gives us the last day of the current month
      return new Date(year, month, 0).getDate();
    }
  });



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
