@extends('layouts.admin.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">
          Quiz
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
                <div class="mb-3 input-icon">
                  <span class="input-icon-addon">
                    <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-month" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                      <path d="M16 3v4" />
                      <path d="M8 3v4" />
                      <path d="M4 11h16" />
                      <path d="M7 14h.013" />
                      <path d="M10.01 14h.005" />
                      <path d="M13.01 14h.005" />
                      <path d="M16.015 14h.005" />
                      <path d="M13.015 17h.005" />
                      <path d="M7.01 17h.005" />
                      <path d="M10.01 17h.005" /></svg>
                  </span>
                  <input type="text" value="{{date('Y-m-d')}}" name="tanggal" id="tanggal" class="form-control" placeholder="Tanggal Absensi" autocomplete="off">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <h4>Quiz : </h4>
                <p id="quiz"></p>
              </div>
            </div>

            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table w-full mb-2 border border-gray-800 rounded" id="dataTable">
                    <thead>
                      <tr>
                        <th class="px-4 py-2 text-center bg-gray-800 border">No. </th>
                        <th class="px-4 py-2 text-center bg-gray-800 border">Nama</th>
                        <th class="px-4 py-2 text-center bg-gray-800 border">Email</th>
                        <th class="px-4 py-2 text-center bg-gray-800 border">Jawaban</th>
                        <th class="px-4 py-2 text-center bg-gray-800 border">Gambar</th>
                      </tr>
                    </thead>
                    <tbody id="loadquiz">
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

  <!-- Modal -->
  <div class="modal fade" id="modalGambar" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitleId">
            Gambar
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <img id="modalImage" src="" alt="Image" class="img-fluid" />
          </div>
        </div>
      </div>
    </div>
  </div>


</div>
@endsection

@push('myscript')
<script>
  $("#tanggal").datepicker({
    autoclose: true
    , todayHighlight: true
    , format: 'yyyy-mm-dd'
  });

  function loadquiz() {
    var tanggal = $('#tanggal').val();

    $.ajax({
      type: 'POST'
      , url: "{{route('quiz.getquiz')}}"
      , data: {
        _token: "{{ csrf_token() }}"
        , tanggal: tanggal
      }
      , cache: false
      , dataType: "json"
      , success: function(data) {

        if (data.pertanyaan.pertanyaan) {
          $('#quiz').text(data.pertanyaan.pertanyaan);
        } else {
          $('#quiz').text('Tidak ada quiz untuk tanggal ini.');
        }

        if ($.fn.DataTable.isDataTable('#dataTable')) {
          $('#dataTable').DataTable().destroy();
        }
        $("#dataTable").DataTable({
          "data": data.quiz
          , "responsive": true
          , "columns": [{
              "data": "id"
            }
            , {
              "data": "user.nama"
            }
            , {
              "data": "user.email"
            }
            , {
              "data": "jawaban"
            }
            , {
              "data": "file"
              , "render": function(data, type, row) {
                return '<button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#modalGambar" onclick="setImage(\'/storage/uploads/quiz/' + data + '\')">' +
                  '<img src="/storage/uploads/quiz/' + data + '" class="avatar" />' +
                  '</button>';
              }
            }
          , ]
        });
      }
    })
  }

  function setImage(imageUrl) {
    // Set the src attribute of the modal's image
    document.getElementById('modalImage').src = imageUrl;
  }

  $('#tanggal').change(function() {
    loadquiz();
  });

  loadquiz();

</script>
@endpush
