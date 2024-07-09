@extends('layouts.admin.tabler')
@section('content')

<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <!-- Page pre-title -->
        <h2 class="page-title">
          Monitoring Absensi
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
                <div class="input-icon mb-3">
                  <span class="input-icon-addon">
                    <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-month" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                      <path d="M16 3v4" />
                      <path d="M8 3v4" />
                      <path d="M4 11h16" />
                      <path d="M7 14h.013" />
                      <path d="M10.01 14h.005" />
                      <path d="M13.01 14h.005" />
                      <path d="M16.015 14h.005" />
                      <path d="M13.015 17h.005" />
                      <path d="M7.01 17h.005" />
                      <path d="M10.01 17h.005" /></svg>
                  </span>
                  <input type="text" value="{{date('Y-m-d')}}" name="tanggal" id="tanggal" class="form-control" placeholder="Tanggal Absensi" autocomplete="off">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table table-striped table-hover" id="dataTable">
                    <thead>
                      <tr>
                        <th>No.</th>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Absen Masuk</th>
                        <th>Posisi Absen</th>
                        <th>Laporan Masuk</th>
                        <th>Foto Masuk</th>
                        <th>Absen Keluar</th>
                        <th>Posisi Keluar</th>
                        <th>Laporan Keluar</th>
                        <th>Foto Keluar</th>
                      </tr>
                    </thead>

                    <tbody id="loadabsen">
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

<div class="modal modal-blur fade" id="show-map" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Lokasi Absen User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="load-map">

      </div>
    </div>
  </div>
</div>

@endsection
@push('myscript')
<script>
  $("#tanggal").datepicker({
    autoclose: true
    , todayHighlight: true
    , format: 'yyyy-mm-dd'
  });

  function loadabsensi() {
    var tanggal = $('#tanggal').val();

    $.ajax({
      type: 'POST'
      , url: "{{route('absen.getabsen')}}"
      , data: {
        _token: "{{ csrf_token() }}"
        , tanggal: tanggal
      }
      , cache: false
      , dataType: "json"
      , success: function(data) {
        console.log(data.absens);
        if ($.fn.DataTable.isDataTable('#dataTable')) {
          $('#dataTable').DataTable().destroy();
        }
        $("#dataTable").DataTable({
          "data": data.absens
          , "responsive": true
          , "columns": [{
              "data": "id"
            }
            , {
              "data": "nama"
            }
            , {
              "data": "tanggal"
            }
            , {
              "data": "jam_masuk"
            }
            , {
              "data": "lokasi_masuk"
            }
            , {
              "data": "laporan_masuk"
            }
            , {
              "data": "foto_masuk"
              , "render": function(data, type, row) {
                return '<img src="/storage/uploads/absensi/' + data + '" width="50" height="50" class="avatar">';
              }
            }
            , {
              "data": "jam_keluar"
            }
            , {
              "data": "lokasi_keluar"
            }
            , {
              "data": "laporan_keluar"
            }
            , {
              "data": "foto_keluar"
              , "render": function(data, type, row) {
                return '<img src="/storage/uploads/absensi/' + data + '" width="50" height="50" class="avatar">';
              }
            }
          , ]
        });
      }
    })
  }

  $('#tanggal').change(function() {
    loadabsensi();
  });

  loadabsensi();

</script>
@endpush
