@extends('layouts.app')

@section('content')
<h1>Edit Absen</h1>
<div class="">
  @if($errors->any())
  <ul>
    @foreach ($errors->all() as $error)
    <li>{{$errors}}</li>
    @endforeach
  </ul>
  @endif
</div>
<form action="{{route('absen.update', ['absen' => $absen])}}" method="post">
  @csrf
  @method('PUT')

  <div class="p-1 bg-gray-500">
    <label for="perner">Perner</label>
    <input type="text" name="perner" placeholder="Perner" value="{{$absen->perner}}">
  </div>

  <div class="form-group">
    <label for="nama">Nama</label>
    <input type="text" name="nama" placeholder="Nama" value="{{$absen->nama}}">
  </div>

  <div class="form-group">
    <label for="">Status</label>
    <ul>
      <li>
        <input type="radio" name="status" placeholder="" value="HADIR">
        Hadir
      </li>
      <li>
        <input type="radio" name="status" placeholder="" value="TIDAK HADIR">
        Tidak Hadir
      </li>
      <li>
        <input type="radio" name="status" placeholder="" value="IZIN">
        Izin
      </li>
      <li>
        <input type="radio" name="status" placeholder="" value="SAKIT">
        Sakit
      </li>
    </ul>
  </div>

  <div class="form-group">
    <label for="keterangan">Keterangan</label>
    <input type="text" name="keterangan" placeholder="Keterangan" value="{{$absen->keterangan}}">
  </div>

  <div class="form-group">
    <input type="submit" value="Update">
  </div>

  <div class="form-group">
    <button type="button" class="btn btn-red" onclick="getCurrentLocation()">getCurrentLocation</button>
    <div id="map" style="height: 400px; width:100%;">
    </div>
    <input type="hidden" name="posisi_absen" id="posisi_absen" value="">
  </div>

</form>

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

</script>

@endsection
