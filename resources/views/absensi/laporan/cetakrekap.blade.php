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
      font-size: 10px
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
<body class="A4 landscape">

  <!-- Each sheet element should have the class "sheet" -->
  <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
  <section class="sheet padding-10mm">

    <table style="width: 100%">
      <tr>
        <td style="width:30px;">
          <img src="https://mysds.satriadigitalsejahtera.co.id/assets/files/assets/images/logo.png" width="120" height="70" alt="">
          {{-- <img src="{{asset('assets/img/web-logo.png')}}" width="120" height="70" alt=""> --}}
          {{-- <img src="{{asset('assets/img/app-logo.jpg')}}" width="120" height="70" alt=""> --}}
          {{-- <img src="{{asset('assets/img/blm.jpg')}}" width="120" height="70" alt=""> --}}
        </td>
        <td>
          <span id="title">LAPORAN ABSENSI KARYAWAN <br>
            PERIODE {{ strtoupper($namabulan[$bulans]) }} {{ $tahun }} <br>
            PT Satria Digital Sejahtera
            {{-- PT Ainiyah Indomitra Sejahtera --}}
            {{-- PT Astama Cahaya Karya --}}
            {{-- PT Berkah Laju Mitra --}}
          </span>

        </td>
      </tr>
    </table>

    <table class="tablepresensi">

      <tr>
        <th rowspan="3">NIK</th>
        <th rowspan="3">Email</th>
        <th rowspan="3">Nama</th>
        <th rowspan="3">Jabatan</th>
        <th colspan="{{$totalDays * 3}}">Tanggal</th>
        <th rowspan="3">TM</th>
        <th rowspan="3">TK</th>
        <th rowspan="3">TI</th>
        <th rowspan="3">TS</th>
        <th rowspan="3">TA</th>
        <th rowspan="3">TH</th>
        <th rowspan="3">TJK</th>
      </tr>

      {{-- Tanggal --}}
      <tr>
        <?php 
        for($i=1; $i<=$totalDays; $i++) {
        ?>
        <th colspan="3">{{$i}}</th>
        <?php
        }
        ?>
      </tr>
      <tr>
        <?php 
        for($i=1; $i<=$totalDays; $i++) {
        ?>
        <th>Masuk</th>
        <th>Keluar</th>
        <th>Jam Kerja</th>
        <?php
        }
        ?>
      </tr>

      @foreach ($result as $res => $d)
      <tr>
        <td>{{$d['perner']}}</td>
        <td>{{$d['email']}}</td>
        <td>{{$d['nama']}}</td>
        <td>{{$d['jabatan']}}</td>

        <?php 
        $totalhadir = 0;
        $totalmasuk = 0;
        $totalkeluar = 0;
        $totalizin = 0;
        $totalsakit = 0;
        $totalalpha = 0;

        for($i=1; $i <= $totalDays; $i++) {
            $dayKey = (string)$i; // Convert $i to string to match keys

            $tgl = "tgl_" . $dayKey;
            $thour = "total_hours_" . $dayKey;
            $tmin = "total_minutes_" . $dayKey;
            $hadir = ['', '']; // Default empty array for Masuk and Keluar

            if($d[$thour] !== null && $d[$thour] > 0) {
              $total_time = $d[$thour] . " Jam " . $d[$tmin] . " Menit";
            } else {
              $total_time = '-';
            }

            if (isset($d[$tgl])) { // Check if the value is set
                if (!empty($d[$tgl])) { // Check if the value is not empty
                    // If the value is not null or empty, process it
                    // Check if $d->$tgl contains '-' to determine if it's concatenated
                    if (strpos($d[$tgl], '-') !== false) {
                        $hadir = explode("-", $d[$tgl]);
                        // If it's concatenated, count as Masuk and Keluar
                        if($hadir[0] != '') {
                            $totalmasuk++;
                            $totalhadir++;
                        }

                        if($hadir[1] != '') {
                            $totalkeluar++;
                        }

                    } else {
                        // If it's a single value, count based on its type
                        switch ($d[$tgl]) {
                            case 'I':
                                $hadir[0] = $d[$tgl];
                                $hadir[1] = $d[$tgl];
                                $totalizin++;
                                $totalhadir++;
                                break;
                            case 'S':
                                $hadir[0] = $d[$tgl];
                                $hadir[1] = $d[$tgl];
                                $totalsakit++;
                                $totalhadir++;
                                break;
                            case 'LIBUR':
                                $hadir[0] = $d[$tgl];
                                $hadir[1] = $d[$tgl];
                                break;
                            default:
                                $totalalpha++; // Increment totalalpha for unspecified cases
                                break;
                        }
                    }
                } else {
                    // If the value is empty, increment totalalpha
                    $totalalpha++;
                }
            } else {
                // If the value is not set, increment totalalpha
                $totalalpha++;
            }
            ?>
        <td class="text-center">{{ $hadir[0] }}</td>
        <td class="text-center">{{ $hadir[1] }}</td>
        <td class="text-center">{{ $total_time }}</td>
        <?php
        }
        ?>
        <td class="text-center">{{ $totalmasuk }}</td>
        <td class="text-center">{{ $totalkeluar }}</td>
        <td class="text-center">{{ $totalizin }}</td>
        <td class="text-center">{{ $totalsakit }}</td>
        <td class="text-center">{{ $totalalpha }}</td>
        <td class="text-center">{{ $totalhadir }}</td>
        <td>{{$d['total_hours_month'] . ' Jam ' . $d['total_minutes_month'] . " Menit"}}</td>

      </tr>
      @endforeach

    </table>

    <table width="100%" style="margin-top: 100px">
      <tr>
        <td></td>
        <td style="text-align: center;">Tangerang, {{date('d-m-Y')}}</td>
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
