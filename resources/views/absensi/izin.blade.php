@extends('layouts.app')

@section('content')
<div class="grid grid-rows-1 p-1">
  <div class="cols">
    @php
    $messagesuccess = Session::get('success');
    $messageerror = Session::get('error');
    @endphp
    @if(Session::get('success'))
    <div class="alert alert-success">
      {{ $messagesuccess}}
    </div>
    @endif
    @if(Session::get('error'))
    <div class="alert alert-danger">
      {{ $messageerror}}
    </div>
    @endif
  </div>
</div>
<div class="grid grid-rows-1">
  @foreach ($dataizin as $d)
  <ul class="listview image-listview">
    <li>
      <div class="item">
        <div class="in">
          <div>
            <b>{{ date("d-m-Y", strtotime($d->tanggal_izin)) }} ({{ $d->status }})</b> <br>
            <small class="text-muted">{{ $d->keterangan }}</small>
          </div>
          @if($d->status_approved == 0)
          <span class="badge bg-warning">Waiting Approval</span>
          @elseif($d->status_approved == 1)
          <span class="badge bg-success">Approved</span>
          @elseif($d->status_approved == 2)
          <span class="badge bg-danger">Rejected</span>
          @endif
        </div>
      </div>
    </li>
  </ul>
  @endforeach
</div>
<div class="fab-button bottom-right pb-12">
  <a href="{{route('absen.buatizin')}}" class="fab">
    <ion-icon name="add-circle-outline"></ion-icon>
  </a>
</div>
@endsection
