@extends('layouts.app')

@section('header')
<style>
  .webcam-capture,
  .webcam-capture video {
    width: 100% !important;
    height: 40vh !important;
    margin: 0;
    border-radius: 4px;
  }

</style>
@endsection

@section('content')
<div class="">
  <div class="mx-auto sm:px-4 lg:px-6">
    <div class="vh-100 bg-gray-900 dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg">
      <div class="grid grid-rows-1 py-1 px-3">
        <input type="hidden" name="lokasi" id="lokasi">
        <div class="webcam-capture"></div>
      </div>
      <div class="grid grid-rows-1 pb-3 px-3 items-center justify-center">
        <div id="canvasContainer" class="grid grid-cols-3"></div>
      </div>
      <div class="grid grid-rows-1 pb-1 px-3">
        <label for="laporan" class="block text-sm font-medium leading-6 text-white">Laporan</label>
        <div>
          <textarea id="laporan" name="laporan" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
        </div>
      </div>
      <div class="grid grid-rows-1 pb-1 px-3">
        <div class="" id="map"></div>
      </div>
      <div class="grid grid-rows-1 px-3" style="padding-bottom: 100px;">
        @if($cek)
        @if($selisihWaktu < 15) <button id="takeAbsenKeluar" onclick="submitAbsen('Keluar')" class="bg-red-500 hover:bg-red-700 font-bold py-2 px-4 rounded text-white text-center pb-10">
          <ion-icon name="camera-outline" class="text-center"></ion-icon>
          Absen Keluar
          </button>
          @else
          <button id="takeAbsenMasuk" onclick="submitAbsen('Masuk')" class="bg-cyan-500 hover:bg-cyan-700 font-bold py-2 px-4 rounded text-white text-center pb-10">
            <ion-icon name="camera-outline" class="text-center"></ion-icon>
            Absen Masuk
          </button>
          @endif
          @else
          <button id="takeAbsenMasuk" onclick="submitAbsen('Masuk')" class="bg-cyan-500 hover:bg-cyan-700 font-bold py-2 px-4 rounded text-white text-center pb-10">
            <ion-icon name="camera-outline" class="text-center"></ion-icon>
            Absen Masuk
          </button>
          @endif
      </div>
    </div>
  </div>
</div>

<script>
  var map = L.map('map').setView([-0.7893, 113.9213], 5); // Centered on Indonesia
  var lokasi = document.getElementById('lokasi');

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);


  function getCurrentLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(showPosition);
    } else {
      alert("Geolocation is not supported by this browser.");
    }
  }

  function showPosition(position) {
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;

    // set the marker on the map
    var marker = L.marker([latitude, longitude]).addTo(map);

    var circle = L.circle([latitude, longitude], {
      color: 'red'
      , fillColor: '#f03'
      , fillOpacity: '0.5'
      , radius: 100
    , }).addTo(map);

    // Use Nominatim API to get the address
    var url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=18&addressdetails=1`;

    fetch(url)
      .then(response => response.json())
      .then(data => {
        var address = data.display_name || 'No address found';
        // alert("Location successfully retrieved:\n" + address);

        // Set the map view to the current location
        map.setView([latitude, longitude], 17);

        // Set the value of 'posisi_absen'
        lokasi.value = address;
      })
      .catch(error => {
        console.error('Error fetching address:', error);
      });
  }

  // Call getCurrentLocation() or initialize map with default coordinates as needed
  getCurrentLocation();

</script>

@push('myscript')
<script>
  Webcam.set({
    height: 480
    , width: 640
    , image_format: 'jpeg'
    , jpeg_quality: 80
    , flip_horiz: true
  });

  Webcam.attach('.webcam-capture');

  $("#takeAbsenMasuk").click(function(e) {
    const jenisAbsen = 'masuk';
    Webcam.snap(function(uri) {
      image = uri;
    });

    var lokasi = $("#lokasi").val();
    console.log(lokasi);
    var laporan = $("#laporan").val();

    if (laporan == "") {
      Swal.fire({
        title: 'Oops.'
        , text: 'Laporan harus diisi.'
        , icon: 'warning'
      , })
      return false;
    }

    $('#takeAbsenMasuk').attr('disabled', true);

    $.ajax({
      type: 'POST'
      , url: "{{route('absen.store')}}"
      , data: {
        _token: "{{ csrf_token() }}"
        , image: image
        , lokasi: lokasi
        , laporan: laporan
        , jenis_absen: jenisAbsen
      , }
      , cache: false
      , success: function(respond) {
        var status = respond.split("|");
        if (status[0] == "success") {
          Swal.fire({
            title: 'Berhasil!'
            , text: status[1]
            , icon: 'success'
            , confirmButtonText: 'OK'
          , })
          setTimeout("location.href='{{route('absen.dashboard')}}'", 2000)
        } else {
          Swal.fire({
            title: 'Error!'
            , text: 'Maaf, absen tidak berhasil.'
            , icon: 'error'
            , confirmButtonText: 'OK'
          , })
        }
      }
    });
  });

  $("#takeAbsenKeluar").click(function(e) {
    const jenisAbsen = 'keluar';
    Webcam.snap(function(uri) {
      image = uri;
    });

    var lokasi = $("#lokasi").val();
    console.log(lokasi);
    var laporan = $("#laporan").val();

    if (laporan == "") {
      Swal.fire({
        title: 'Oops.'
        , text: 'Laporan harus diisi.'
        , icon: 'warning'
      , })
      return false;
    }
    $('#takeAbsenKeluar').attr('disabled', true);


    $.ajax({
      type: 'POST'
      , url: "{{route('absen.store')}}"
      , data: {
        _token: "{{ csrf_token() }}"
        , image: image
        , lokasi: lokasi
        , laporan: laporan
        , jenis_absen: jenisAbsen
      , }
      , cache: false
      , success: function(respond) {
        var status = respond.split("|");
        if (status[0] == "success") {
          Swal.fire({
            title: 'Berhasil!'
            , text: status[1]
            , icon: 'success'
            , confirmButtonText: 'OK'
          , })
          setTimeout("location.href='{{route('absen.dashboard')}}'", 2000)
        } else {
          Swal.fire({
            title: 'Error!'
            , text: 'Maaf, absen tidak berhasil.'
            , icon: 'error'
            , confirmButtonText: 'OK'
          , })
        }
      }
    });
  });

  function submitAbsen(type) {
    // Disable the button to prevent multiple clicks
    // document.getElementById(`takeAbsen${type}`).disabled = true;

    // You can also submit the form or perform other actions here
    // For example, if you have a form, you can use form.submit();
    // Make sure to handle the server-side processing accordingly
  }

</script>
@endpush
@endsection
