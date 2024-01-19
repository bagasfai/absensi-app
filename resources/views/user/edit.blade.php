<form action="{{ route('user.update', ['email' => $user->email]) }}" method="post" id="form-input" enctype="multipart/form-data">
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
        <input type="text" value="{{$user->nama}}" name="nama" id="nama" class="form-control" placeholder="Nama Lengkap" fdprocessedid="9ar8xn">
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="mb-3">
        <select class="form-select" fdprocessedid="ukk3eh" name="jabatan" id="jabatan">
          <option value="">Jabatan</option>
          @foreach ($jabatan as $j)
          <option {{ $user->jabatan == $j->jabatan ? 'selected' : '' }} value="{{ $j->jabatan }}">{{$j->jabatan}}</option>
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
        <input type="email" name="email" id="email" value="{{$user->email}}" class="form-control" placeholder="Email" fdprocessedid="9ar8xn">
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="mb-3">
        <input type="file" name="foto" id="foto" class="form-control">
        <input type="hidden" name="old_foto" id="foto" class="form-control" value="{{$user->foto}}">
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
