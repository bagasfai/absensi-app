@extends('layouts.app')

@section('content')

<x-slot name="header">
  <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
    {{ __('Dashboard') }}
  </h2>
</x-slot>

<!-- loader -->
<div id="loader">
  <div class="spinner-border text-primary" role="status"></div>
</div>
<!-- * loader -->

<!-- App Capsule -->
<div class="grid grid-rows-1 pb-5">
  <div class="pb-5 col">
    <div class="rows">
      <div class="col-12">
        <form method="post" action="{{ route('quiz.update_jawaban', ['id' => $quizAnswer->id]) }}" enctype="multipart/form-data">
          @csrf

          <div class="form-group">
            <div class="mb-1">
              <label for="pertanyaan" class="form-label">Pertanyaan</label>
              <textarea class="form-control" name="pertanyaan" id="pertanyaan" rows="3" readonly>{{ $quiz->pertanyaan }}</textarea>
            </div>

            <div class="mb-1">
              <label for="jawaban" class="form-label">Jawaban</label>
              <textarea class="form-control" name="jawaban" id="jawaban" rows="3">{{ $quizAnswer->jawaban }}</textarea>
            </div>

            <div class="mb-1">
              <label for="file" class="form-label">Upload File</label>
              <input type="file" class="form-control" name="file" id="file" placeholder="" aria-describedby="fileHelpId" />
              <small id="fileHelpId" class="form-text">File dalam bentuk jpg, jpeg, dan png</small>
            </div>

          </div>

          <button class="block w-full btn btn-primary" type="submit">
            <ion-icon name="download-outline"></ion-icon> Update
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
