@extends('layouts.admin.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">
          Buat Quiz
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
                <div class="">
                  <a class="my-2 btn btn-warning" href="{{ route('quiz.index') }}">Kembali</a>
                </div>
                <div class="card">
                  <div class="card-body">
                    <form method="POST" action="{{ route('quiz.store') }}">
                      @csrf

                      <div class="form-group">
                        <div class="mb-3">
                          <label for="pertanyaan" class="form-label">Pertanyaan</label>
                          <textarea class="form-control @error('pertanyaan') is-invalid @enderror" name="pertanyaan" id="pertanyaan" rows="3">{{ old('pertanyaan') }}</textarea>
                        </div>
                        @error('pertanyaan')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                      </div>

                      <div class="form-group">
                        <div class="mb-3">
                          <label for="jadwal" class="form-label">Jadwal <small class="fs-6 text-secondary">*opsional</small></label>
                          <input type="date" class="form-control @error('jadwal') is-invalid @enderror" name="jadwal" id="jadwal" value="{{ old('jadwal') }}" />
                        </div>
                      </div>

                      <div class="form-group">
                        <div class="mb-3">
                          <label for="durasi_edit" class="form-label">Batas Waktu Edit</label>
                          <div class="d-flex">
                            <input type="number" class="form-control @error('durasi_edit') is-invalid @enderror" name="durasi_edit" id="durasi_edit" value="{{ old('durasi_edit') }}" placeholder="Masukkan batas waktu" />
                            <select class="form-select ms-2" name="edit_unit" id="edit_unit">
                              <option value="minutes" {{ old('edit_unit') == 'minutes' ? 'selected' : '' }}>Menit</option>
                              <option value="hours" {{ old('edit_unit') == 'hours' ? 'selected' : '' }}>Jam</option>
                            </select>
                          </div>
                          <small class="fs-6 text-secondary">Contoh: 1 jam 30 Menit = 90 menit.</small>
                        </div>

                        @error('durasi_edit')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                      </div>

                      <div class="form-group">
                        <div class="mb-3">
                          <label for="assignTo" class="form-label">Assign To <small class="fs-6 text-secondary">*opsional</small></label>
                          <select multiple name="assignTo[]" id="assignTo" class="form-select form-select-lg">
                            <option value="">Pilih</option>

                            <optgroup label="ROLES" class="fw-bold text-uppercase text-primary">
                              <option value="pmr">PMR</option>
                              <option value="wh">WAREHOUSE</option>
                            </optgroup>

                            @if(isset($users))
                            <optgroup label="USERS" class="fw-bold text-uppercase text-info">
                              @foreach($users as $user)
                              <option value="{{ $user->id }}">{{ $user->nama }}</option>
                              @endforeach
                            </optgroup>
                            @endif
                          </select>
                        </div>
                      </div>

                      <button type="submit" class="w-full btn btn-primary">Create</button>

                      @if ($errors->any())
                      <div class="mt-2 alert alert-danger">
                        <ul>
                          @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                          @endforeach
                        </ul>
                      </div>
                      @endif

                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('myscript')
<script>
  const multipleSelect = new Choices('#assignTo', {
    removeItemButton: true, // Allows removal of selected items
    searchEnabled: true, // Enables the search feature
  });

  fetch("{{ route('quiz.dates') }}")
    .then(response => response.json())
    .then(data => {
      const unavailableDates = data; // Array of dates that are unavailable
      const jadwalInput = document.getElementById("jadwal");

      jadwalInput.addEventListener("input", function() {
        const selectedDate = jadwalInput.value;

        if (unavailableDates.includes(selectedDate)) {
          // Use SweetAlert2 to show the error message
          Swal.fire({
            icon: 'error'
            , title: 'Tanggal tidak tersedia!'
            , text: 'Sudah ada quiz untuk hari itu!'
            , showConfirmButton: true
            , timer: 2000
          });

          jadwalInput.setCustomValidity("Sudah ada quiz untuk hari ini.");
        } else {
          jadwalInput.setCustomValidity(""); // Reset custom validation message
        }
      });

      // Disable unavailable dates in the calendar
      jadwalInput.addEventListener("focus", function() {
        const inputDate = new Date().toISOString().split("T")[0];
        jadwalInput.setAttribute("min", inputDate); // Disable past dates dynamically
      });
    })
    .catch(error => {
      console.error("Error fetching quiz dates:", error);
    });

</script>
@endpush
