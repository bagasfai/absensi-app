@foreach ($absen as $a)
@php
$fotoIn = Storage::url('uploads/absensi/' . $a->foto_masuk);
$fotoOut = Storage::url('uploads/absensi/' . $a->foto_keluar);
@endphp
<tr>
  <td class="border border-slate-400">{{$a->id}}</td>
  <td class="border border-slate-400">{{$a->nama}}</td>
  <td class="border border-slate-400">{{$a->jam_masuk}}</td>
  <td class="border border-slate-400">{{$a->lokasi_masuk}}</td>
  <td class="border border-slate-400">{{$a->laporan_masuk}}</td>
  <td class="border border-slate-400"><img src="{{url($fotoIn)}}" alt="" class="avatar"></td>
  <td class="border border-slate-400">{{$a->jam_keluar}}</td>
  <td class="border border-slate-400">{{$a->lokasi_keluar}}</td>
  <td class="border border-slate-400">{{$a->laporan_keluar}}</td>
  <td class="border border-slate-400"><img src="{{url($fotoOut)}}" alt="" class="avatar"></td>
  <td class="border border-slate-400">{{$a->tanggal}}</td>
</tr>
@endforeach
