@foreach ($absen as $a)
@php
$fotoIn = Storage::url('uploads/absensi/' . $a->foto_masuk);
$fotoOut = Storage::url('uploads/absensi/' . $a->foto_keluar);
@endphp
<tr>
  <td>{{ $loop->iteration }}</td>
  <td>{{$a->nama}}</td>
  <td>{{$a->jam_masuk}}</td>
  <td>{{$a->lokasi_masuk}}</td>
  <td>{{$a->laporan_masuk}}</td>
  <td>
    <img src="{{url($fotoIn)}}" class="avatar">
  </td>
  <td>{!!$a->jam_keluar !== null ? $a->jam_keluar : '<span class="badge bg-danger text-white">Belum absen.</span>'!!}</td>
  <td>{{$a->lokasi_keluar !== null ? $a->lokasi_keluar : '--'}}</td>
  <td>{{$a->laporan_keluar !== null ? $a->laporan_keluar : '--'}}</td>
  <td>
    @if($a->jam_keluar !== null)
    <img src="{{url($fotoOut)}}" class="avatar">
    @else
    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-hourglass-high" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
      <path d="M6.5 7h11" />
      <path d="M6 20v-2a6 6 0 1 1 12 0v2a1 1 0 0 1 -1 1h-10a1 1 0 0 1 -1 -1z" />
      <path d="M6 4v2a6 6 0 1 0 12 0v-2a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1z" /></svg>
    @endif
  </td>
</tr>
@endforeach
