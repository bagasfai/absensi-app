@extends('layouts.app')

{{-- @section('header')
<div class="appHeader bg-primary text-light">
  <div class="left">
    <a href="#" class="headerButton goBack">
      <ion-icon name="chevron-back-outline"></ion-icon>
    </a>
  </div>
  <div class="pageTitle">Edit Profile</div>
  <div class="right"></div>
</div>
@endsection --}}

@section('content')
<div class="grid grid-rows-1 p-1">
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
<form action="{{ route('updateprofile', ['email' => $karyawan->email]) }}" method="POST" enctype="multipart/form-data">
  @csrf
  <div class="col">
    <div class="form-group boxed">
      <div class="input-wrapper">
        <input type="text" class="form-control" value="{{ $karyawan->nama }}" name="nama_lengkap" placeholder="Nama Lengkap" autocomplete="off">
      </div>
    </div>
    <div class="form-group boxed">
      <div class="input-wrapper">
        <input type="text" class="form-control" value="{{ $karyawan->email }}" name="email" placeholder="Email" autocomplete="off">
      </div>
    </div>
    <div class="form-group boxed">
      <div class="input-wrapper">
        <input type="password" class="form-control" name="password" placeholder="Password" autocomplete="off">
      </div>
    </div>
    <div class="form-group boxed">
      <div class="input-wrapper">
        <input type="id_telegram" class="form-control" value="{{ $karyawan->id_telegram }}" name="id_telegram" placeholder="ID Telegram" autocomplete="off">
      </div>
    </div>
    <div class="custom-file-upload" id="fileUpload1">
      <input type="file" name="foto" id="fileuploadInput" accept=".png, .jpg, .jpeg">
      <label for="fileuploadInput">
        <span>
          <strong>
            <ion-icon name="cloud-upload-outline" role="img" class="md hydrated" aria-label="cloud upload outline"></ion-icon>
            <i>Tap to Upload</i>
          </strong>
        </span>
      </label>
    </div>
    <small class="text-red-600">*Kosongkan jika tidak ingin di ubah</small>
    <div class="form-group boxed">
      <div class="input-wrapper">
        <button type="submit" class="btn btn-primary btn-block">
          <ion-icon name="refresh-outline"></ion-icon>
          Update
        </button>
      </div>
    </div>
  </div>
</form>
@endsection
