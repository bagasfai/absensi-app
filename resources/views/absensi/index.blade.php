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
    <div class="w-full">
      <div class="mx-auto bg-white rounded-md overflow-hidden shadow-md">
        <div class="row">
          @if(session()->has('success'))
          <div class="">
            {{session('success')}}
          </div>
          @endif
        </div>
        <div class="p-4">
          <form action="{{ route('absen.setPeriode') }}" method="POST">
            <div class="row">
              @csrf
              <label for="tanggal" class="col-form-label">Periode</label>
              <div class="col-2">
                <div class="input-icon mb-3">
                  <input type="month" name="tanggal" id="tanggal" class="form-control" placeholder="Tanggal Absensi" autocomplete="off" value="{{ session('periode') ? session('periode') : date('Y-m') }}">
                </div>
              </div>
              <div class="col-sm-2">
                <button type="submit" class="btn btn-primary">SET</button>
              </div>
            </div>
          </form>

          <div class="table-responsive">

            <table border="1" class="w-full border border-gray-800 rounded mb-2" id="dataTable">
              <thead>
                <tr>
                  <th class="border border-slate-400">ID</th>
                  <th class="border border-slate-400">Nama</th>
                  <th class="border border-slate-400">Absen Masuk</th>
                  <th class="border border-slate-400">Posisi Masuk</th>
                  <th class="border border-slate-400">Laporan Masuk</th>
                  <th class="border border-slate-400">Foto Masuk</th>
                  <th class="border border-slate-400">Absen Keluar</th>
                  <th class="border border-slate-400">Posisi Keluar</th>
                  <th class="border border-slate-400">Laporan Keluar</th>
                  <th class="border border-slate-400">Foto Keluar</th>
                  <th class="border border-slate-400">Tanggal</th>
                </tr>
              </thead>
              <tbody id="loadabsen">
                @foreach ($absen as $a)
                @php
                $fotoIn = Storage::url('uploads/absensi/' . $a->foto_masuk);
                $fotoOut = Storage::url('uploads/absensi/' . $a->foto_keluar);
                @endphp
                <tr>
                  <td class="border border-slate-400">{{$a->id}}</td>
                  <td class="border border-slate-400">{{$a->nama}}</td>
                  <td class="border border-slate-400">{{$a->jam_masuk}}</td>
                  <td class="border border-slate-400">{{$a->lokasi_masuk}}</td>
                  <td class="border border-slate-400">{{$a->laporan_masuk}}</td>
                  <td class="border border-slate-400"><img src="{{url($fotoIn)}}" alt="" class="avatar"></td>
                  <td class="border border-slate-400">{{$a->jam_keluar}}</td>
                  <td class="border border-slate-400">{{$a->lokasi_keluar}}</td>
                  <td class="border border-slate-400">{{$a->laporan_keluar}}</td>
                  <td class="border border-slate-400"><img src="{{url($fotoOut)}}" alt="" class="avatar"></td>
                  <td class="border border-slate-400">{{$a->tanggal}}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('myscript')
<script>
  $('#dataTable').DataTable({});

  function loadabsensi() {
    var tanggal = $('#tanggal').val();
    console.log(tanggal);

    $.ajax({
      type: 'POST'
      , url: "{{route('absen.getRekapAbsen')}}"
      , data: {
        _token: "{{ csrf_token() }}"
        , tanggal: tanggal
      }
      , cache: false
      , success: function(respond) {
        $("#loadabsen").html(respond);
      }
    })
  }

  // $('#tanggal').change(function() {
  //   loadabsensi();
  // });

  // loadabsensi();

</script>
@endpush
