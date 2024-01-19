@extends('layouts.app')

@section('content')
<x-slot name="header">
  <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    {{ __('Dashboard') }}
  </h2>
</x-slot>

<div class="py-12">
  <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

      <div class="p-6 text-gray-900 dark:text-gray-100">
        <h1 class="text-lg font-medium text-gray-400">Absen</h1>

        <div class="">
          @if($errors->any())
          <ul>
            @foreach ($errors->all() as $error)
            <li>{{$errors}}</li>
            @endforeach
          </ul>
          @endif
        </div>

        <div class="border-b border-gray-900/10 pb-12">
          <form action="{{route('absen.absenKeluar')}}" method="POST">
            @csrf
            @method('post')

            @foreach ($absen as $a)
            <div class="grid grid-cols-2 justify-content-center gap-2">
              <div class="pb-4">
                <label for="perner" class="text-lg font-medium leading-6 text-gray-400">Perner</label>
                <div class="w-full rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600">
                  <input type="text" name="perner" id="perner" autocomplete="perner" class="w-full border-0 bg-transparent py-1.5 pl-1 text-gray-400 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" placeholder="Perner" value="{{$a->perner}}" readonly>
                </div>
              </div>

              <div class="pb-4">
                <label for="nama" class="text-lg font-medium leading-6 text-gray-400">Nama</label>
                <div class="w-full rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600">
                  <input type="text" name="nama" id="nama" autocomplete="nama" class="w-full border-0 bg-transparent py-1.5 pl-1 text-gray-400 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" placeholder="Nama" value="{{$a->name}}" readonly>
                </div>
              </div>
            </div>
            @endforeach

            <div class="col-span-full">
              <label for="laporan" class="block text-sm font-medium leading-6 text-gray-400">Laporan</label>
              <div class="mt-2">
                <textarea id="laporan" name="laporan" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-400 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
              </div>
            </div>

            <div class="pb-4">
              <label for="bukti">Upload Foto</label>
              <input type="file" accept="image/*" capture="camera" name="bukti" id="cameraInput">
            </div>

            <div class="">
              <input type="submit" value="Confirm">
            </div>

            <div class="">
              <button type="button" class="btn btn-red" onclick="getCurrentLocation()">getCurrentLocation</button>
              <div id="map" class="w-full h-64">
              </div>
              <input type="hidden" name="posisi_absen" id="posisi_absen" value="">
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  var map = L.map('map').setView([-0.7893, 113.9213], 5); // Centered on Indonesia

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

    // Use Nominatim API to get the address
    var url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=18&addressdetails=1`;

    fetch(url)
      .then(response => response.json())
      .then(data => {
        var address = data.display_name || 'No address found';
        // alert("Location successfully retrieved:\n" + address);

        // Set the map view to the current location
        map.setView([latitude, longitude], 18);

        // Set the value of 'posisi_absen'
        document.getElementById("posisi_absen").value = address;
      })
      .catch(error => {
        console.error('Error fetching address:', error);
      });
  }

  // Call getCurrentLocation() or initialize map with default coordinates as needed
  getCurrentLocation();

  const cameraInput = document.getElementById('cameraInput');
  cameraInput.addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (file) {
      // You can preview the image or perform other actions if needed
      console.log('Image selected:', file);
    }
  });

</script>
@endsection
