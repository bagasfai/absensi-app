<form action="{{ route('user.update', ['email' => $user->email]) }}" method="post" id="form-input" enctype="multipart/form-data">
  @csrf

  {{-- Nama Lengkap --}}
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

  {{-- Password --}}
  <div class="row">
    <div class="col-12">
      <div class="input-icon mb-3">
        <span class="input-icon-addon">
          <!-- Download SVG icon from http://tabler-icons.io/i/user -->
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-password-user" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M12 17v4" />
            <path d="M10 20l4 -2" />
            <path d="M10 18l4 2" />
            <path d="M5 17v4" />
            <path d="M3 20l4 -2" />
            <path d="M3 18l4 2" />
            <path d="M19 17v4" />
            <path d="M17 20l4 -2" />
            <path d="M17 18l4 2" />
            <path d="M9 6a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
            <path d="M7 14a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2" /></svg>
        </span>
        <input type="password" name="password" id="password" class="form-control" placeholder="Password" fdprocessedid="9ar8xn">
      </div>
    </div>
  </div>

  {{-- Jabatan --}}
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

  {{-- Email --}}
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


  <small>*kosongkan jika tidak ingin diubah</small>

  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <button class="btn btn-primary w-100">Simpan</button>
      </div>
    </div>
  </div>

</form>
