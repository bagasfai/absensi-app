<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>A4</title>

  <!-- Normalize or reset CSS with your favorite library -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

  <!-- Load paper.css for happy printing -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

  <!-- Set page size here: A5, A4 or A3 -->
  <!-- Set also "landscape" if you need -->
  <style>
    @page {
      size: A4
    }

    #title {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 18px;
      font-weight: bold;
    }

    .tabeldatakaryawan {
      margin-top: 40px;
    }

    .tabeldatakaryawan tr td {
      padding: 5px;
    }

    .tablepresensi {
      width: 100%;
      margin-top: 20px;
      border-collapse: collapse;
    }

    .tablepresensi tr th {
      border: 1px solid #131212;
      padding: 8px;
      background: #dbdbdb;
    }

    .tablepresensi tr td {
      border: 1px solid #131212;
      padding: 5px;
      font-size: 12px;
    }

    .foto {
      width: 40px;
      height: 50px;
    }

  </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="A4">

  <!-- Each sheet element should have the class "sheet" -->
  <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
  <section class="sheet padding-10mm">

    <table style="width: 100%">
      <tr>
        <td style="width:30px;">
          {{-- <img src="https://mysds.satriadigitalsejahtera.co.id/assets/files/assets/images/logo.png" width="120" height="70" alt=""> --}}
          {{-- <img src="{{asset('assets/img/web-logo.png')}}" width="120" height="70" alt=""> --}}
          {{-- <img src="{{asset('assets/img/app-logo.jpg')}}" width="120" height="70" alt=""> --}}
          <img src="{{asset('assets/img/blm.jpg')}}" width="120" height="70" alt="">
        </td>
        <td>
          <span id="title">LAPORAN ABSENSI KARYAWAN <br>
            PERIODE {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }} <br>
            {{-- PT Satria Digital Sejahtera --}}
            {{-- PT Astama Cahaya Karya --}}
            PT Berkah Laju Mitra
          </span>

        </td>
      </tr>
    </table>

    <table class="tabeldatakaryawan">
      <tr>
        <td rowspan="4">
          @php
          $path = Storage::url('uploads/karyawan/'. $user->foto);
          @endphp
          <img src="{{ url($path) }}" alt="" width="150px" height="150px">
        </td>
      </tr>
      <tr>
        <td>Email</td>
        <td>:</td>
        <td>{{$user->email}}</td>
      </tr>

      <tr>
        <td>Nama</td>
        <td>:</td>
        <td>{{$user->nama}}</td>
      </tr>

      <tr>
        <td>Jabatan</td>
        <td>:</td>
        <td>{{$user->jabatan}}</td>
      </tr>
    </table>

    <table class="tablepresensi">
      <tr>
        <th>No.</th>
        <th>Tanggal</th>
        <th>Jam Masuk</th>
        <th>Laporan Masuk</th>
        <th>Jam Keluar</th>
        <th>Laporan Keluar</th>
      </tr>
      @foreach ($absen as $a)
      <tr>
        <td>{{$loop->iteration}}</td>
        <td> {{date('d-m-Y', strtotime($a->tanggal))}} </td>
        <td>{{$a->jam_masuk}}</td>
        <td>{{$a->laporan_masuk}}</td>
        <td>{{$a->jam_keluar !== null ? $a->jam_keluar : 'Tidak Absen.'}}</td>
        <td>{{$a->laporan_keluar !== null ? $a->laporan_keluar : 'Tidak Absen.'}}</td>
      </tr>
      @endforeach
    </table>

    <table width="100%" style="margin-top: 100px">
      <tr>
        <td colspan="2" style="text-align: right;">Tangerang, {{date('d-m-Y')}}</td>
      </tr>

      <tr>

        <td style="text-align: center; vertical-align: bottom;" height="100px">
          <u>Nama HRD</u> <br>
          <i><b>HRD Manager</b></i>
        </td>

        <td style="text-align: center; vertical-align: bottom;">
          <u>Nama Direksi</u> <br>
          <i><b>Direksi</b></i>
        </td>

      </tr>
    </table>

  </section>

</body>

</html>
