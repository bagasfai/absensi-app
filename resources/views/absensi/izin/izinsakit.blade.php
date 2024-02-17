@extends('layouts.admin.tabler')
@section('content')

<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <!-- Page pre-title -->
        <h2 class="page-title">
          Pengajuan Izin / Sakit
        </h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <form action="{{route('absen.izinsakit')}}" method="GET">
                  <div class="row">
                    <div class="col-6">

                      <div class="input-icon mb-3">
                        <span class="input-icon-addon">
                          <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-week" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                            <path d="M16 3v4" />
                            <path d="M8 3v4" />
                            <path d="M4 11h16" />
                            <path d="M8 14v4" />
                            <path d="M12 14v4" />
                            <path d="M16 14v4" /></svg>
                        </span>
                        <input type="text" value="{{ Request('dari') }}" name="dari" id="dari" class="form-control" placeholder="Dari" fdprocessedid="9ar8xn" autocomplete="off">
                      </div>

                    </div>
                    <div class="col-6">

                      <div class="input-icon mb-3">
                        <span class="input-icon-addon">
                          <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-week" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                            <path d="M16 3v4" />
                            <path d="M8 3v4" />
                            <path d="M4 11h16" />
                            <path d="M8 14v4" />
                            <path d="M12 14v4" />
                            <path d="M16 14v4" /></svg>
                        </span>
                        <input type="text" value="{{ Request('sampai') }}" name="sampai" id="sampai" class="form-control" placeholder="Sampai" fdprocessedid="9ar8xn" autocomplete="off">
                      </div>

                    </div>
                  </div>

                  <div class="row pb-4">
                    <div class="col-12">
                      <div class="form-group">
                        <button class="btn btn-primary w-100" type="submit"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                            <path d="M21 21l-6 -6" /></svg> Cari</button>
                      </div>
                    </div>
                  </div>
                </form>

              </div>
            </div>

            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table table-bordered" id="dataTable">

                    <thead>
                      <tr>
                        <th>No.</th>
                        <th>Tanggal</th>
                        <th>Email</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Status Approved</th>
                        <th>Evident</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>

                    <tbody>
                      @foreach ($izinsakit as $d)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ date('d-m-Y', strtotime($d->tanggal_izin ))}}</td>
                        <td>{{ $d->email }}</td>
                        <td>{{ $d->nama }}</td>
                        <td> {{ $d->jabatan }} </td>
                        <td> {{ $d->status }} </td>
                        <td> {{ $d->keterangan }} </td>
                        <td>
                          @if($d->status_approved == 1)
                          <span class="badge bg-success text-white">Approved
                          </span>
                          @elseif($d->status_approved == 2)
                          <span class="badge bg-danger text-white">Rejected</span>
                          @else
                          <span class="badge bg-warning text-white">Waiting Approval</span>
                          @endif
                        </td>
                        <td>
                          <a href="#" class="btn btn-sm btn-info btn-detail" data-evident="{{ $d->evident }}">EVIDENT</a>
                        </td>
                        <td>
                          @if($d->status_approved == 0)
                          <a href="" class="btn btn-sm btn-primary btn-approve" id="" id_izin="{{ $d->id }}" status_izin="{{ $d->status }}" tanggal_izin="{{ $d->tanggal_izin }}" evident_izin="{{ $d->evident }}" nama_izin="{{ $d->nama }}" email_izin="{{ $d->email }}"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-external-link" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                              <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" />
                              <path d="M11 13l9 -9" />
                              <path d="M15 4h5v5" /></svg></a>
                          @else
                          <a href="{{route('absen.batalapprove', ['id' => $d->id])}}" class="btn btn-sm btn-danger"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-square-rounded-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                              <path d="M10 10l4 4m0 -4l-4 4" />
                              <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z" /></svg>
                            Batalkan</a>
                          @endif
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
      </div>
    </div>

  </div>
</div>

{{-- MODAL APPROVAL --}}
<div class="modal modal-blur fade" id="modal-izinsakit" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Izin / Sakit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{route('absen.action')}}" method="POST">
          @csrf
          <input type="hidden" name="id_izin_form" id="id_izin_form">
          <input type="hidden" name="status_izin_form" id="status_izin_form">
          <input type="hidden" name="evident_izin_form" id="evident_izin_form">
          <input type="hidden" name="nama_izin_form" id="nama_izin_form">
          <input type="hidden" name="email_izin_form" id="email_izin_form">
          <input type="hidden" name="tanggal_izin_form" id="tanggal_izin_form">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <select name="status_approved" id="status_approved" class="form-select">
                  <option value="1">Approved</option>
                  <option value="2">Rejected</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row mt-1">
            <div class="col-12">
              <div class="form-group">
                <button class="btn btn-primary w-100" type="submit">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-send" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M10 14l11 -11" />
                    <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" /></svg>
                  Submit
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- MODAL EVIDENT --}}
<div class="modal modal-blur fade" id="modal-izindetail" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Evident</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <img id="evidentImage" src="" alt="User Tidak Menginput Evident">
      </div>
    </div>
  </div>
</div>


@endsection

@push('myscript')
<script>
  $('#dataTable').DataTable({});

</script>
<script>
  $(document).on('click', '.btn-approve', function(e) {
    e.preventDefault();
    var id_izin = $(this).attr("id_izin");
    var status_izin = $(this).attr("status_izin");
    var tanggal_izin = $(this).attr("tanggal_izin");
    var evident_izin = $(this).attr("evident_izin");
    var nama_izin = $(this).attr("nama_izin");
    var email_izin = $(this).attr("email_izin");

    console.log(tanggal_izin);

    $("#id_izin_form").val(id_izin);
    $("#status_izin_form").val(status_izin);
    $("#tanggal_izin_form").val(tanggal_izin);
    $("#evident_izin_form").val(evident_izin);
    $("#nama_izin_form").val(nama_izin);
    $("#email_izin_form").val(email_izin);
    $('#modal-izinsakit').modal('show');
  });

  $(document).on('click', '.btn-detail', function(e) {
    e.preventDefault();
    var evident = $(this).data("evident");

    var fileExtension = evident.split('.').pop().toLowerCase();

    console.log(fileExtension);

    // Update the modal content with the new evident information
    if (fileExtension === 'pdf') {
      // Display PDF in an iframe
      $('#modal-izindetail .modal-body').html('<iframe src="' + "{{ asset('storage/uploads/izin/') }}" + '/' + evident + '" width="100%" height="500px"></iframe>');
    } else {
      // Display image
      $('#modal-izindetail .modal-body').html('<img id="evidentImage" src="' + "{{ asset('storage/uploads/izin/') }}" + '/' + evident + '" alt="User tidak menginput evident">');
    }

    // Update the modal content with the new evident information
    // $('#modal-izindetail img').attr("src", "{{ asset('storage/uploads/izin/') }}" + '/' + evident);
    $('#modal-izindetail').modal('show');
  });

  $("#dari, #sampai").datepicker({
    autoclose: true
    , todayHighlight: true
    , format: 'yyyy-mm-dd'
  });

</script>
@endpush
