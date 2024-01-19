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
        </td>
        <td>
          <span id="title">LAPORAN ABSENSI KARYAWAN <br>
            PERIODE {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }} <br>
            PT Satria Digital Sejahtera
          </span>

        </td>
      </tr>
    </table>

    <table class="tablepresensi">

      <tr>
        <th rowspan="2">Email</th>
        <th rowspan="2">Nama</th>
        <th colspan="31">Tanggal</th>
        <th rowspan="2">TH</th>
      </tr>

      <tr>
        <?php 
        for($i=1; $i<=31; $i++) {
        ?>
        <th>{{$i}}</th>
        <?php
        }
        ?>
      </tr>

      @foreach ($rekap as $d)
      <tr>
        <td>{{$d->email}}</td>
        <td>{{$d->nama}}</td>

        <?php 
        $totalhadir = 0;
        for($i=1; $i<=31; $i++) {
          $tgl = "tgl_".$i;

          if (empty($d->$tgl)) {
            $hadir = ['', ''];
            $totalhadir += 0;
          } else {
            $hadir = explode("-", $d->$tgl);
            $totalhadir += 1;
          }
        ?>
        <td>
          {{$hadir[0]}}
          {{$hadir[1]}}
        </td>
        <?php
        }
        ?>
        <td>{{ $totalhadir }}</td>

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
