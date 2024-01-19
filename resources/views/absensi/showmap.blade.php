<style>
  #map {
    height: 180px;
  }

</style>

{{$absen->lokasi_masuk}}
<div id="map"></div>
<script>
  var map = L.map('map').setView([51.505, -0.09], 13);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}.png', {
    maxZoom: 19
    , attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  }).addTo(map);

</script>
