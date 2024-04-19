@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <!-- Page pre-title -->
        <h2 class="page-title">
          Data Karyawan
        </h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class=" w-full">
      <div class="mx-auto bg-white rounded-md overflow-hidden shadow-md">
        <div class="row">
          <div class="col-12">
            @if (Session::get('success'))
            <div class="alert alert-success">
              {{Session::get('success')}}
            </div>
            @endif

            @if(Session::get('warning'))
            <div class="alert alert-warning">
              {{Session::get('warning')}}
            </div>
            @endif
          </div>
        </div>
        <div class="grid grid-rows-1 px-4 pt-2 text-white">
          <a href="#" class="btn btn-primary" id="btn-add">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M12 5l0 14" />
              <path d="M5 12l14 0" /></svg>
            Tambah Data
          </a>
        </div>
        <div class="p-4">
          <form action="{{route('user.index')}}" method="GET">
          </form>
          <table class="w-full border border-gray-800 rounded mb-2" id="dataTable">
            <thead>
              <tr>
                <th class="text-center border px-4 py-2 bg-gray-800">No. </th>
                <th class="text-center border px-4 py-2 bg-gray-800">Nama</th>
                <th class="text-center border px-4 py-2 bg-gray-800">Email</th>
                <th class="text-center border px-4 py-2 bg-gray-800">Jabatan</th>
                <th class="text-center border px-4 py-2 bg-gray-800">Foto</th>
                <th class="text-center border px-4 py-2 bg-gray-800">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @php
              $no = 1;
              @endphp
              @foreach ($user as $u)
              @php
              $path = Storage::url('uploads/karyawan/' . $u->foto);
              @endphp
              <tr>
                <td class="border text-center">{{$no++}}</td>
                <td class="border px-2">{{$u->nama}}</td>
                <td class="border px-2">{{$u->email}}</td>
                <td class="border text-center">{{$u->jabatan}}</td>
                <td class="border text-center">
                  @if(empty($u->foto))
                  {{-- <img src="https://mysds.satriadigitalsejahtera.co.id/assets/files/assets/images/logo.png" class="avatar" alt="Foto User"> --}}
                  {{-- <img src="{{asset('assets/img/web-logo.png')}}" class="avatar" alt="Foto User"> --}}
                  {{-- <img src="{{asset('assets/img/app-logo.jpg')}}" class="avatar" alt="Foto User"> --}}
                  <img src="{{asset('assets/img/blm.jpg')}}" class="avatar" alt="Foto User">
                  @else
                  <img src="{{url($path)}}" class="avatar" alt="Foto User">
                  @endif
                </td>
                <td class="border">
                  <div class="btn-group">
                    <a href="#" class="edit btn btn-info btn-sm" email="{{$u->email}}">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                        <path d="M16 5l3 3" /></svg></a>
                    <form action="{{route('user.delete', ['email' => $u->email])}}" method="POST" style="margin-left: 5px;">
                      @csrf
                      <a class="btn btn-danger btn-sm delete-confirm"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                          <path d="M4 7l16 0" />
                          <path d="M10 11l0 6" />
                          <path d="M14 11l0 6" />
                          <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                          <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg></a>
                    </form>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="modal-input" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Data Karyawan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{route('user.store')}}" method="post" id="form-input" enctype="multipart/form-data">
          @csrf

          <div class="row">
            <div class="col-12">
              <div class="input-icon mb-3">
                <span class="input-icon-addon">
                  <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                  </svg>
                </span>
                <input type="text" value="" name="nama" id="nama" class="form-control" placeholder="Nama Lengkap" fdprocessedid="9ar8xn">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="mb-3">
                <select class="form-select" fdprocessedid="ukk3eh" name="jabatan" id="jabatan">
                  <option value="">Jabatan</option>
                  @foreach ($jabatan as $j)
                  <option value="{{$j->jabatan}}">{{$j->jabatan}}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="input-icon mb-3">
                <span class="input-icon-addon">
                  <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mail" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                    <path d="M3 7l9 6l9 -6" /></svg>
                </span>
                <input type="email" name="email" id="email" value="" class="form-control" placeholder="Email" fdprocessedid="9ar8xn">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="input-icon mb-3">
                <span class="input-icon-addon">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-telegram" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M15 10l-4 4l6 6l4 -16l-18 7l4 2l2 6l3 -4" /></svg>
                </span>
                <input type="number" nama="id_telegram" id="id_telegram" class="form-control" placeholder="ID Telegram" fdprocessedid="9ar8xn">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="mb-3">
                <input type="file" name="foto" id="foto" class="form-control">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <button class="btn btn-primary w-100">Simpan</button>
              </div>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="modal-edit" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Data Karyawan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="loadeditform">

      </div>
    </div>
  </div>
</div>

@endsection

@push('myscript')
<script>
  $(document).ready(function() {
    // Initialize DataTable with event delegation
    var dataTable = $('#dataTable').DataTable();

    // Show modal on add button click
    $("#btn-add").click(function() {
      $("#modal-input").modal("show");
    });

    // Handle edit button click using event delegation
    $('#dataTable').on('click', '.edit', function() {
      var email = $(this).attr('email');
      console.log(email);
      $.ajax({
        type: 'POST'
        , url: "{{route('user.edit')}}"
        , cache: false
        , data: {
          _token: "{{ csrf_token(); }}"
          , email: email
        }
        , success: function(respond) {
          $("#loadeditform").html(respond);
        }
      });
      $("#modal-edit").modal("show");
    });

    // Handle delete button click using event delegation
    $('#dataTable').on('click', '.delete-confirm', function(e) {
      var form = $(this).closest('form');
      e.preventDefault();
      Swal.fire({
        title: 'Apakah anda yakin ingin menghapus data ini?'
        , text: 'Data yang dihapus tidak bisa dikembalikan!'
        , icon: 'warning'
        , showCancelButton: true
        , cancelButtonText: 'Batal'
        , cancelButtonColor: '#d33'
        , confirmButtonColor: '#3085d6'
        , confirmButtonText: 'Hapus.'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
          Swal.fire('Terhapus!', 'Data berhasil dihapus', 'success');
        }
      });
    });

    // Validate form input before submission
    $("#form-input").submit(function() {
      var nama = $("#nama").val();
      var jabatan = $("#jabatan").val();
      var email = $("#email").val();
      var password = $("#password").val();

      if (nama == "") {
        showAlert('Nama Lengkap harus diisi.');
        $("#nama").focus();
        return false;
      } else if (jabatan == "") {
        showAlert('Jabatan harus dipilih.');
        $("#jabatan").focus();
        return false;
      } else if (email == "") {
        showAlert('Email harus diisi.');
        $("#email").focus();
        return false;
      } else if (password == "") {
        showAlert('Password harus diisi.');
        $("#password").focus();
        return false;
      }
    });

    // Helper function to show a warning alert
    function showAlert(message) {
      Swal.fire({
        title: 'Warning!'
        , text: message
        , icon: 'warning'
        , confirmButtonText: 'Ok'
      });
    }
  });

</script>
@endpush
