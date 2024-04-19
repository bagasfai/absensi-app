<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Absensi') }}</title>

  {{-- <link rel="icon" href="https://mysds.satriadigitalsejahtera.co.id/assets/files/assets/images/logo.png"> --}}
  {{-- <link rel="icon" href="{{asset('assets/img/web-logo.png')}}"> --}}
  {{-- <link rel="icon" href="{{asset('assets/img/app-logo.jpg')}}"> --}}
  <link rel="icon" href="{{asset('assets/img/blm.jpg')}}">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <!-- Leaflet CSS and JS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <!-- Leaflet Geocoder Plugin with Nominatim -->
  <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
  <script src="https://nominatim.openstreetmap.org/js/nominatim-v3.3.0.js"></script>

  <link rel="stylesheet" href="{{asset('assets/css/style.css')}}" />


</head>
<body class="font-sans antialiased">
  <div class="min-h-screen">
    @include('layouts.navigation')

    <!-- Page Heading -->
    @if (isset($header))
    <header class="bg-gray-800 shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        {{ $header }}
      </div>
    </header>
    @endif

    @yield('header')

    <!-- Page Content -->
    <main>
      {{-- {{ $slot }} --}}
      @yield('content')
    </main>

    <!-- App Bottom Menu -->
    <div class="appBottomMenu">
      <a href="{{route('absen.dashboard')}}" class="item {{request()->is('dashboard') ? 'active' : ''}}">
        <div class="col">
          <ion-icon name="home-outline"></ion-icon>
          <strong>Home</strong>
        </div>
      </a>
      <a href="{{route('absen.histori')}}" class="item {{request()->is('absen/histori') ? 'active' : ''}}">
        <div class="col">
          <ion-icon name="document-text-outline" class="md hydrated" aria-label="histori outline"></ion-icon>
          <strong>Histori</strong>
        </div>
      </a>
      @if(isset($cek) && is_object($cek) && $cek->jam_keluar !== null && $selisihWaktuOut < 2) <!-- Content when $cek->jam_keluar is not null -->
        @elseif(request()->routeIs('absen.create'))
        <!-- Content when the current route is 'absen.dashboard' -->
        @elseif($selisihWaktuOut > 1)
        <a href="{{ route('absen.create') }}" class="item">
          <div class="col">
            <div class="action-button large">
              <ion-icon name="camera" role="img" class="md hydrated" aria-label="add outline"></ion-icon>
            </div>
          </div>
        </a>
        @else
        <!-- Content when $cek is not an object or $cek->jam_keluar is null -->
        <a href="{{ route('absen.create') }}" class="item">
          <div class="col">
            <div class="action-button large">
              <ion-icon name="camera" role="img" class="md hydrated" aria-label="add outline"></ion-icon>
            </div>
          </div>
        </a>
        @endif
        <a href="{{route('absen.izin')}}" class="item {{request()->is('absen/izin') ? 'active' : ''}}">
          <div class="col">
            <ion-icon name="calendar-outline" class="md hydrated" aria-label="izin outline"></ion-icon>
            <strong>Izin</strong>
          </div>
        </a>
        <a href="{{route('editprofile')}}" class="item {{request()->is('absen/editprofile') ? 'active' : ''}}">
          <div class="col">
            <ion-icon name="people-outline" role="img" class="md hydrated" aria-label="people outline"></ion-icon>
            <strong>Profile</strong>
          </div>
        </a>
    </div>
  </div>
</body>
<!--     Js Files     -->
<!-- Jquery -->
<script src="{{ asset('assets/js/lib/jquery-3.4.1.min.js')}}"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- Bootstrap-->
<script src="{{ asset('assets/js/lib/popper.min.js')}}"></script>
<script src="{{ asset('assets/js/lib/bootstrap.min.js')}}"></script>
<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<!-- Owl Carousel -->
<script src="{{ asset('assets/js/plugins/owl-carousel/owl.carousel.min.js') }} "></script>
<!-- jQuery Circle Progress -->
<script src="{{ asset('assets/js/plugins/jquery-circle-progress/circle-progress.min.js')}}"></script>
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
<!-- Base Js File -->
<script src="{{ asset('assets/js/base.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js" integrity="sha512-dQIiHSl2hr3NWKKLycPndtpbh5iaHLo6MwrXm7F0FM5e+kL2U16oE9uIwPHUl6fQBeCthiEuV/rzP3MiAB8Vfw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
  am4core.ready(function() {
    // Themes begin
    am4core.useTheme(am4themes_animated);
    // Themes end

    var chart = am4core.create("chartdiv", am4charts.PieChart3D);
    chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

    chart.legend = new am4charts.Legend();

    chart.data = [{
        country: "Hadir"
        , litres: 501.9
      , }
      , {
        country: "Sakit"
        , litres: 301.9
      , }
      , {
        country: "Izin"
        , litres: 201.1
      , }
      , {
        country: "Terlambat"
        , litres: 165.8
      , }
    , ];

    var series = chart.series.push(new am4charts.PieSeries3D());
    series.dataFields.value = "litres";
    series.dataFields.category = "country";
    series.alignLabels = false;
    series.labels.template.text = "{value.percent.formatNumber('#.0')}%";
    series.labels.template.radius = am4core.percent(-40);
    series.labels.template.fill = am4core.color("white");
    series.colors.list = [
      am4core.color("#1171ba")
      , am4core.color("#fca903")
      , am4core.color("#37db63")
      , am4core.color("#ba113b")
    , ];
  }); // end am4core.ready()

</script>
@stack('myscript')
</html>
