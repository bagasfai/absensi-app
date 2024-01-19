@if($histori->isEmpty())
<div class="alert alert-outline-warning">
  <p>Data belum ada.</p>
</div>
@endif

@foreach ($histori as $h)
<ul class="listview image-listview">
  <li>
    <div class="item">
      @php
      $path = Storage::url('uploads/absensi/'. $h->foto_masuk)
      @endphp
      <img src="{{ url($path) }}" alt="image" class="image">
      <div class="in">
        <div class="">
          <b>{{ date("d-m-Y", strtotime($h->tanggal)) }}</b>
        </div>
        <span class="badge bg-success">
          {{$h->jam_masuk}}
        </span>
        <span class="badge bg-danger">
          {{$h->jam_keluar ? $h->jam_keluar : 'Belum absen'}}
        </span>
      </div>
    </div>
  </li>
</ul>
@endforeach
