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
                <div class="card">
                  <div class="card-body">

                    <a class="mb-3 btn btn-primary" href="{{route('quiz.create')}}" role="button">Create Quiz</a>

                    <div class="table-responsive">
                      <table class="table w-full mb-2 border border-gray-800 rounded" id="dataTable">
                        <thead>
                          <tr>
                            <th class="px-4 py-2 text-center bg-gray-800 border">No. </th>
                            <th class="px-4 py-2 text-center bg-gray-800 border">Soal</th>
                            <th class="px-4 py-2 text-center bg-gray-800 border">Scheduled At</th>
                            <th class="px-4 py-2 text-center bg-gray-800 border">Durasi Edit</th>
                            <th class="px-4 py-2 text-center bg-gray-800 border">Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          @php
                          $no = 1;
                          @endphp
                          @foreach ($quiz as $q)
                          <tr>
                            <td class="px-4 py-2 bg-gray-200 border">{{ $no++ }}</td>
                            <td class="px-4 py-2 bg-gray-200 border">{{ $q->pertanyaan }}</td>
                            <td class="px-4 py-2 bg-gray-200 border">{{ $q->jadwal }}</td>
                            <td class="px-4 py-2 bg-gray-200 border text-nowrap">{{ $q->durasi_edit }} <small class="fs-6 text-secondary">(menit)</small></td>
                            <td class="px-4 py-2 bg-gray-200 border">
                              <div class="gap-2 d-flex justify-content-center align-items-center">
                                <a href="{{ route('quiz.edit', ['id' => $q->id]) }}" class="btn btn-success">Edit</a>
                                {{-- <button class="btn btn-danger">Delete</button> --}}
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
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
