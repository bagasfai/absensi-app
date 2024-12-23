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
    <form action="{{ route('cuti.store') }}" method="POST" id="formizin" enctype="multipart/form-data">
      @csrf

      <div class="form-group">
        <input type="text" class="block w-full text-center text-gray-900 border-0 rounded-md shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 focus:border-blue-500 sm:text-sm sm:leading-6 datepicker" placeholder="Tanggal Cuti" name="tanggal_cuti" id="tanggal_cuti" autocomplete="off" value="{{ old('tanggal_cuti') }}">
      </div>

      <div class="form-group">
        <select id="jenis_cuti" name="jenis_cuti" class="block w-full border rounded-md focus:outline-none focus:border-blue-500">
          <option value="" {{ old('jenis_cuti') == '' ? 'selected' : '' }}>Status</option>
          <option value="Cuti Tahunan" {{ old('jenis_cuti') == 'Cuti Tahunan' ? 'selected' : '' }}>Cuti Tahunan</option>
          <option value="Cuti Acara Penting" {{ old('jenis_cuti') == 'Cuti Acara Penting' ? 'selected' : '' }}>Cuti Acara Penting</option>
        </select>

      </div>

      <div class="form-group">
        <div class=>
          <textarea id="keterangan" name="keterangan" rows="3" class="block w-full h-32 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 focus:border-blue-500 sm:text-sm sm:leading-6" placeholder="Keterangan">{{ old('keterangan') }}</textarea>
        </div>
      </div>

      @if ($errors->any())
      <div class="mb-2 alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      <div class="form-group">
        <button class="w-full rounded-md btn btn-primary">Kirim</button>
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
      var tanggal_cuti = $("#tanggal_cuti").val();
      var jenis_cuti = $("#jenis_cuti").val();
      var keterangan = $("#keterangan").val();

      if (tanggal_cuti == "") {
        Swal.fire({
          title: 'Oops.'
          , text: 'Tanggal harus diisi.'
          , icon: 'warning'
        , })
        return false;
      } else if (jenis_cuti == "") {
        Swal.fire({
          title: 'Oops.'
          , text: 'Jenis Cuti harus dipilih.'
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

    $("#tanggal_cuti").change(function() {
      var tanggal_cuti = $(this).val();

      $.ajax({
        type: 'POST'
        , url: "{{route('absen.cekizin')}}"
        , data: {
          _token: "{{ csrf_token() }}"
          , tanggal_cuti: tanggal_cuti
        }
        , cache: false
        , success: function(respond) {
          if (respond == 1) {
            Swal.fire({
              title: 'Oops.'
              , text: 'Sudah melakukan input pada tanggal tersebut.'
              , icon: 'warning'
            }).then((result) => {
              $('#tanggal_cuti').val("");
            })
          }
        }
      })
    });
  });

</script>
@endpush
