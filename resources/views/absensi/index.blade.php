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
              </tr>
            </thead>
            <tbody>
              @foreach ($absen as $a)
              @php
              $pathMasuk = Storage::url('uploads/absensi/' . $a->foto_masuk);
              $pathKeluar = Storage::url('uploads/absensi/' . $a->foto_keluar);
              @endphp
              <tr>
                <td class="border border-slate-400">{{$a->id}}</td>
                <td class="border border-slate-400">{{$a->nama}}</td>
                <td class="border border-slate-400">{{$a->jam_masuk}}</td>
                <td class="border border-slate-400">{{$a->lokasi_masuk}}</td>
                <td class="border border-slate-400">{{$a->laporan_masuk}}</td>
                <td class="border border-slate-400"><img src="{{url($pathMasuk)}}" alt=""></td>
                <td class="border border-slate-400">{{$a->jam_keluar}}</td>
                <td class="border border-slate-400">{{$a->lokasi_keluar}}</td>
                <td class="border border-slate-400">{{$a->laporan_keluar}}</td>
                <td class="border border-slate-400"><img src="{{url($pathKeluar)}}" alt=""></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('myscript')
<script>
  $('#dataTable').DataTable({});

</script>
@endpush
