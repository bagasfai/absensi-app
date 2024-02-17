@extends('layouts.app')

@section('header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
<style>
  .datepicker-modal {
    max-height: 460px !important;
  }

  .datepicker-table td.is-selected,
  .datepicker-date-display {
    background-color: #10151c !important;
  }

  .datepicker-table td.on-select {
    background-color: #10151c !important;
  }

  .datepicker-table td.is-today {
    color: #2563eb !important;
  }

</style>
@endsection

@section('content')
<div class="grid grid-rows-1 p-1">
  <div class="cols">
    <form action="{{ route('absen.storeizin') }}" method="POST" id="formizin" enctype="multipart/form-data">
      @csrf

      <div class="form-group">
        <input type="text" class="block w-full rounded-md border-0 text-center text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 focus:border-blue-500 sm:text-sm sm:leading-6 datepicker" placeholder="Tanggal Izin" name="tanggal_izin" id="tanggal_izin" autocomplete="off">
      </div>

      <div class="form-group">
        <select id="status" name="status" class="block w-full border rounded-md focus:outline-none focus:border-blue-500">
          <option value="">Status</option>
          <option value="IZIN">Izin</option>
          <option value="SAKIT">Sakit</option>
        </select>
      </div>

      <div class="form-group">
        <div class=>
          <textarea id="keterangan" name="keterangan" rows="3" class="block w-full h-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 focus:border-blue-500 sm:text-sm sm:leading-6" placeholder="Keterangan"></textarea>
        </div>
      </div>

      <div class="custom-file-upload form-group" id="fileUpload1">
        <input type="file" name="foto" id="fileizin" accept=".png, .jpg, .jpeg, .pdf">
        <label for="fileizin">
          <span>
            <strong>
              <ion-icon name="cloud-upload-outline" role="img" class="md hydrated" aria-label="cloud upload outline"></ion-icon>
              <i>Tap to Upload</i>
            </strong>
          </span>
        </label>
      </div>

      <div class="form-group">
        <button class="btn btn-primary w-full rounded-md">Kirim</button>
      </div>

    </form>
  </div>
</div>
@endsection

@push('myscript')
<script>
  var currYear = (new Date()).getFullYear();

  $(document).ready(function() {
    $(".datepicker").datepicker({
      format: "yyyy-mm-dd"
    });

    $("#formizin").submit(function() {
      var tanggal_izin = $("#tanggal_izin").val();
      var status = $("#status").val();
      var keterangan = $("#keterangan").val();

      if (tanggal_izin == "") {
        Swal.fire({
          title: 'Oops.'
          , text: 'Tanggal harus diisi.'
          , icon: 'warning'
        , })
        return false;
      } else if (status == "") {
        Swal.fire({
          title: 'Oops.'
          , text: 'Status harus dipilih.'
          , icon: 'warning'
        , })
        return false;
      } else if (keterangan == "") {
        Swal.fire({
          title: 'Oops.'
          , text: 'Keterangan harus diisi.'
          , icon: 'warning'
        , })
        return false;
      }
    });

    $("#tanggal_izin").change(function() {
      var tanggal_izin = $(this).val();

      $.ajax({
        type: 'POST'
        , url: "{{route('absen.cekizin')}}"
        , data: {
          _token: "{{ csrf_token() }}"
          , tanggal_izin: tanggal_izin
        }
        , cache: false
        , success: function(respond) {
          if (respond == 1) {
            Swal.fire({
              title: 'Oops.'
              , text: 'Sudah melakukan input pada tanggal tersebut.'
              , icon: 'warning'
            }).then((result) => {
              $('#tanggal_izin').val("");
            })
          }
        }
      })
    });
  });

</script>
@endpush
