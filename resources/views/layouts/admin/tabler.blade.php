<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Dashboard Admin</title>
  {{-- <link rel="icon" href="https://mysds.satriadigitalsejahtera.co.id/assets/files/assets/images/logo.png"> --}}
  {{-- <link rel="icon" href="{{asset('assets/img/web-logo.png')}}"> --}}
  {{-- <link rel="icon" href="{{asset('assets/img/app-logo.jpg')}}"> --}}
  <link rel="icon" href="{{asset('assets/img/blm.jpg')}}">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />

  <!-- CSS files -->
  <link href="{{asset('tabler/dist/css/tabler.min.css?1692870487')}}" rel="stylesheet" />
  <link href="{{asset('tabler/dist/css/tabler-flags.min.css?1692870487')}}" rel="stylesheet" />
  <link href="{{asset('tabler/dist/css/tabler-payments.min.css?1692870487')}}" rel="stylesheet" />
  <link href="{{asset('tabler/dist/css/tabler-vendors.min.css?1692870487')}}" rel="stylesheet" />
  <link href="{{asset('tabler/dist/css/demo.min.css?1692870487')}}" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" />

  <!-- Leaflet CSS and JS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  {{-- <link rel="stylesheet" href="{{asset('assets/css/style.css')}}" /> --}}

  {{-- <script src="https://cdn.tailwindcss.com"></script> --}}



  <!-- Scripts -->
  {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
  <style>
    @import url('https://rsms.me/inter/inter.css');

    :root {
      --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
    }

    body {
      font-feature-settings: "cv03", "cv04", "cv11";
    }

  </style>
</head>
<body>
  <script src="{{asset('tabler/dist/js/demo-theme.min.js?1692870487"')}}"></script>
  <div class=" page">
    <!-- Sidebar -->
    @include('layouts.admin.sidebar')
    <!-- Navbar -->
    @include('layouts.admin.header')
    <div class="page-wrapper">
      @yield('content')
      @include('layouts.admin.footer')
    </div>
  </div>

</body>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<!-- Libs JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
<script src="{{asset('tabler/dist/libs/apexcharts/dist/apexcharts.min.js?1692870487')}}" defer></script>
<script src="{{asset('tabler/dist/libs/jsvectormap/dist/js/jsvectormap.min.js?1692870487')}}" defer></script>
<script src="{{asset('tabler/dist/libs/jsvectormap/dist/maps/world.js?1692870487')}}" defer></script>
<script src="{{asset('tabler/dist/libs/jsvectormap/dist/maps/world-merc.js?1692870487')}}" defer></script>
<!-- Tabler Core -->
<script src="{{asset('tabler/dist/js/tabler.min.js?1692870487')}}" defer></script>
<script src="{{asset('tabler/dist/js/demo.min.js?1692870487')}}" defer></script>
<!-- datepicker -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<!-- Leaflet Geocoder Plugin with Nominatim -->
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script src="https://nominatim.openstreetmap.org/js/nominatim-v3.3.0.js"></script>


@stack('myscript')
</html>
