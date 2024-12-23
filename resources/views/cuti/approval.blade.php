@extends('layouts.admin.tabler')
@section('content')

<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <!-- Page pre-title -->
        <h2 class="page-title">
          Pengajuan Cuti
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
                <form action="{{route('cuti.approval')}}" method="GET">
                  <div class="row">
                    <div class="col-6">

                      <div class="mb-3 input-icon">
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

                      <div class="mb-3 input-icon">
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

                  <div class="pb-4 row">
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
                        <th>Jenis Cuti</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th>Evident</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>

                    <tbody>
                      @foreach ($cuti as $c)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ date('d-m-Y', strtotime($c->tanggal_cuti ))}}</td>
                        <td>{{ $c->user->email }}</td>
                        <td>{{ $c->user->nama }}</td>
                        <td> {{ $c->user->jabatan }} </td>
                        <td> {{ $c->jenis_cuti }} </td>
                        <td> {{ $c->keterangan }} </td>
                        <td>
                          @if($c->status == 1)
                          <span class="text-white badge bg-success">Approved
                          </span>
                          @elseif($c->status == 2)
                          <span class="text-white badge bg-danger">Rejected</span>
                          @else
                          <span class="text-white badge bg-warning">Waiting Approval</span>
                          @endif
                        </td>
                        <td>
                          <a href="#" class="btn btn-sm btn-info btn-detail" data-evident="{{ $c->evident }}">EVIDENT</a>
                        </td>
                        <td>
                          @if($c->status == 0)
                          <a href="" class="btn btn-sm btn-primary btn-approve" id="" id_cuti="{{ $c->id }}" status_cuti="{{ $c->status }}" tanggal_cuti="{{ $c->tanggal_cuti }}" evident_cuti="{{ $c->evident }}" nama_cuti="{{ $c->user->nama }}" email_cuti="{{ $c->user->email }}"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-external-link" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                              <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" />
                              <path d="M11 13l9 -9" />
                              <path d="M15 4h5v5" /></svg></a>
                          @else
                          <a href="{{route('cuti.batalApprove', ['id' => $c->id])}}" class="btn btn-sm btn-danger"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-square-rounded-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
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
<div class="modal modal-blur fade" id="modal-cuti" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cuti</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{route('cuti.action')}}" method="POST">
          @csrf
          <input type="hidden" name="id_cuti_form" id="id_cuti_form">
          <input type="hidden" name="status_cuti_form" id="status_cuti_form">
          <input type="hidden" name="evident_cuti_form" id="evident_cuti_form">
          <input type="hidden" name="nama_cuti_form" id="nama_cuti_form">
          <input type="hidden" name="email_cuti_form" id="email_cuti_form">
          <input type="hidden" name="tanggal_cuti_form" id="tanggal_cuti_form">
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

          <div class="mt-1 row">
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
<div class="modal modal-blur fade" id="modal-cuti-detail" tabindex="-1" role="dialog" aria-hidden="true">
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
    var id_cuti = $(this).attr("id_cuti");
    var status_cuti = $(this).attr("status_cuti");
    var tanggal_cuti = $(this).attr("tanggal_cuti");
    var evident_cuti = $(this).attr("evident_cuti");
    var nama_cuti = $(this).attr("nama_cuti");
    var email_cuti = $(this).attr("email_cuti");

    console.log(tanggal_cuti);

    $("#id_cuti_form").val(id_cuti);
    $("#status_cuti_form").val(status_cuti);
    $("#tanggal_cuti_form").val(tanggal_cuti);
    $("#evident_cuti_form").val(evident_cuti);
    $("#nama_cuti_form").val(nama_cuti);
    $("#email_cuti_form").val(email_cuti);
    $('#modal-cuti').modal('show');
  });

  $(document).on('click', '.btn-detail', function(e) {
    e.preventDefault();
    var evident = $(this).data("evident");

    var fileExtension = evident.split('.').pop().toLowerCase();

    // Update the modal content with the new evident information
    if (fileExtension === 'pdf') {
      // Display PDF in an iframe
      $('#modal-cuti-detail .modal-body').html('<iframe src="' + "{{ asset('storage/uploads/cuti/') }}" + '/' + evident + '" width="100%" height="500px"></iframe>');
    } else {
      // Display image
      $('#modal-cuti-detail .modal-body').html('<img id="evidentImage" src="' + "{{ asset('storage/uploads/cuti/') }}" + '/' + evident + '" alt="User tidak menginput evident">');
    }

    // Update the modal content with the new evident information
    // $('#modal-cuti-detail img').attr("src", "{{ asset('storage/uploads/cuti/') }}" + '/' + evident);
    $('#modal-cuti-detail').modal('show');
  });

  $("#dari, #sampai").datepicker({
    autoclose: true
    , todayHighlight: true
    , format: 'yyyy-mm-dd'
  });

</script>
@endpush
