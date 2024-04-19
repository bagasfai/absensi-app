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
</head>
<body class="font-sans text-gray-900 antialiased">
  <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
    <div>
      <a href="/">
        {{-- <img src="https://mysds.satriadigitalsejahtera.co.id/assets/files/assets/images/logo.png" alt="" height="200" width="200"> --}}
        {{-- <img src="{{asset('assets/img/web-logo.png')}}" alt="" height="200" width="200"> --}}
        {{-- <img src="{{asset('assets/img/app-logo.jpg')}}" alt="" height="200" width="200"> --}}
        <img src="{{asset('assets/img/blm.jpg')}}" alt="" height="200" width="200">
      </a>
    </div>

    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
      {{ $slot }}
    </div>
  </div>
</body>
</html>
