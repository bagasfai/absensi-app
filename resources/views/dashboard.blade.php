@extends('layouts.app')

@section('content')

<x-slot name="header">
  <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    {{ __('Dashboard') }}
  </h2>
</x-slot>

<!-- loader -->
<div id="loader">
  <div class="spinner-border text-primary" role="status"></div>
</div>
<!-- * loader -->

<!-- App Capsule -->
<div id="" class="pb-1">
  <!-- appCapsule -->
  <div class="section mb-4" id="user-section">
    <div id="user-detail">
      <div class="avatar">
        @if(!empty(auth()->user()->foto))
        @php
        $path = Storage::url('uploads/karyawan/'. auth()->user()->foto)
        @endphp
        @else
        @php
        // Default image path
        // $path = 'https://mysds.satriadigitalsejahtera.co.id/assets/files/assets/images/logo.png';
        // $path = asset('assets/img/web-logo.png');
        // $path = asset('assets/img/app-logo.jpg');
        $path = asset('assets/img/blm.jpg');
        @endphp
        @endif
        <img src="{{ $path }}" alt="avatar" class="imaged w-24 h-16 bg-white" />
      </div>
      <div id="user-info">
        <h2 id="user-name">{{ auth()->user()->nama}}</h2>
        <span id="user-role">{{ auth()->user()->jabatan }}</span>
      </div>
    </div>
  </div>

  <div class="section mb-4" id="">
    <!-- menu-section -->
    <div class="card">
      <div class="card-body text-center">
        <div class="list-menu">
          <div class="item-menu text-center">
            <div class="menu-icon">
              <a href="{{route('editprofile')}}" class="green" style="font-size: 40px">
                <ion-icon name="person-sharp"></ion-icon>
              </a>
            </div>
            <div class="menu-name">
              <span class="text-center">Profil</span>
              </a>
            </div>
          </div>
          <div class="item-menu text-center">
            <div class="menu-icon">
              <a href="{{route('absen.izin')}}" class="danger" style="font-size: 40px">
                <ion-icon name="calendar-number"></ion-icon>
              </a>
            </div>
            <div class="menu-name">
              <span class="text-center">Cuti</span>
            </div>
          </div>
          <div class="item-menu text-center">
            <div class="menu-icon">
              <a href="{{route('absen.histori')}}" class="warning" style="font-size: 40px">
                <ion-icon name="document-text"></ion-icon>
              </a>
            </div>
            <div class="menu-name">
              <span class="text-center">Histori</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="section mb-4" id="">
    <!-- presence-section -->
    <div class="">
      <div class="row">
        <div class="col-6 mb-4">
          <div class="card gradasigreen">
            <div class="card-body">
              <div class="presencecontent">
                <div class="iconpresence">
                  @if($latestEntry !== null && $selisihWaktu < 15) @php $path=Storage::url('/uploads/absensi/' . $latestEntry->foto_masuk);
                    @endphp
                    <img src="{{url ($path)}}" alt="" class="w-12 rounded">
                    @else
                    <ion-icon name="camera"></ion-icon>
                    @endif
                </div>
                <div class="presencedetail">
                  <h4 class="presencetitle lg:pb-1">Masuk</h4>
                  <span>{{$latestEntry !== null && $selisihWaktu < 15 ? $latestEntry->jam_masuk : 'Belum absen.'}}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-6">
          <div class="card gradasired">
            <div class="card-body">
              <div class="presencecontent">
                <div class="iconpresence">
                  @if($latestEntry !== null && $latestEntry->jam_keluar !== null && $selisihWaktu < 15) @php $path=Storage::url('/uploads/absensi/' . $latestEntry->foto_keluar);
                    @endphp
                    <img src="{{url ($path)}}" alt="" class="w-12 rounded">
                    @else
                    <ion-icon name="camera"></ion-icon>
                    @endif
                </div>
                <div class="presencedetail">
                  <h4 class="presencetitle lg:pb-1">Keluar</h4>
                  <span>{{$latestEntry !== null && $latestEntry->jam_keluar !== null && $selisihWaktu < 15 ? $latestEntry->jam_keluar : 'Belum absen.'}}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="" id="rekap-absen">
      <h3>Rekap Absen Bulan {{ $namaBulan[$bulan] }} Tahun {{ $tahun }}</h3>
      <div class="mx-auto max-w-screen-lg grid grid-cols-3 gap-2">

        <div class="flex flex-col justify-center items-center max-w-sm h-16 rounded overflow-hidden shadow-lg bg-white py-1 relative">
          <small class="absolute top-1 right-4 badge badge-danger">{{ $rekapAbsen->jumlah_hadir }}</small>
          <ion-icon name="accessibility-outline" class="text-center text-2xl text-blue-500 bg-transparent border-transparent"></ion-icon>
          <span class="text-xs">Hadir</span>
        </div>

        <div class="flex flex-col justify-center items-center max-w-sm h-16 rounded overflow-hidden shadow-lg bg-white py-1 relative">
          <small class="absolute top-1 right-4 badge badge-danger">{{$rekapIzin->jumlah_izin ? $rekapIzin->jumlah_izin : 0}}</small>
          <ion-icon name="newspaper-outline" class="text-center text-2xl text-green-500 bg-transparent border-transparent"></ion-icon>
          <span class="text-xs">Izin</span>
        </div>

        <div class="flex flex-col justify-center items-center max-w-sm h-16 rounded overflow-hidden shadow-lg bg-white py-1 relative">
          <small class="absolute top-1 right-4 badge badge-danger">{{$rekapIzin->jumlah_sakit ? $rekapIzin->jumlah_sakit : 0}}</small>
          <ion-icon name="medkit-outline" class="text-center text-2xl text-yellow-500 bg-transparent border-transparent"></ion-icon>
          <span class="text-xs">Sakit</span>
        </div>

        {{-- <div class="flex flex-col justify-center items-center max-w-sm h-16 rounded overflow-hidden shadow-lg bg-white py-1 relative">
          <small class="absolute top-1 right-4 badge badge-danger">0</small>
          <ion-icon name="alarm-outline" class="text-center text-2xl text-red-500 bg-transparent border-transparent"></ion-icon>
          <span class="text-xs">Alpha</span>
        </div> --}}
      </div>
    </div>

    <div class="presencetab mt-2">
      <div class="tab-pane fade show active" id="pilled" role="tabpanel">
        <ul class="nav nav-tabs style1" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#home" role="tab">
              Bulan Ini
            </a>
          </li>
          @if(auth()->user()->jabatan === 'SUPERADMIN' || auth()->user()->jabatan == 'TEAM WAGNER')
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#daftar" role="tab">
              Daftar Hadir
            </a>
          </li>
          @endif
        </ul>
      </div>
      <div class="tab-content mt-2" style="margin-bottom: 100px">
        <div class="tab-pane fade show active" id="home" role="tabpanel">
          <ul class="listview image-listview">
            @foreach ($absenBulan as $bulan )
            @php
            $path = Storage::url('uploads/absensi/' . $bulan->foto_masuk);
            @endphp
            <li>
              <div class="item">
                <div class="icon-box bg-primary">
                  <img src="{{ url($path)}}" alt="" class="w-12 rounded">
                </div>
                <div class="in">
                  <div>{{ date('d-m-Y', strtotime($bulan->tanggal)) }}</div>
                  <div class="grid grid-cols-1">
                    <span class="badge badge-success">{{$bulan->jam_masuk}}</span>
                    <span class="badge badge-danger">{{$bulan !== null && $bulan->jam_keluar !== null ? $bulan->jam_keluar : 'Belum absen.'}}</span>
                  </div>
                </div>
              </div>
            </li>
            @endforeach
          </ul>
        </div>

        <div class="tab-pane fade" id="daftar" role="tabpanel">
          <ul class="listview image-listview">
            @foreach ($daftarHadir as $daftar )
            @php
            $path = Storage::url('uploads/absensi/' . $daftar->foto_masuk);
            @endphp
            <li>
              <div class="item">
                <div class="icon-box bg-primary">
                  <img src="{{ url($path)}}" alt="" class="w-12 rounded">
                </div>
                <div class="in">
                  <div>{{$daftar->nama}}</div>
                </div>
                <span class="badge badge-success">{{$daftar->jam_masuk}}</span>
              </div>
            </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- * App Capsule -->


@endsection
