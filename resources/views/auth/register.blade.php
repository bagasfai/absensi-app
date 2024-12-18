<x-guest-layout>
  <form method="POST" action="{{ route('register') }}">
    @csrf

    @if ($errors->any())
    <div>
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <!-- Perner -->
    <div class="mt-4">
      <x-input-label for="perner" :value="__('Perner')" />
      <x-text-input id="perner" class="block w-full mt-1" type="text" name="perner" :value="old('perner')" autofocus autocomplete="off" />
      <small class="text-red-400">*kosongkan jika tidak ada</small>
      <x-input-error :messages="$errors->get('perner')" class="mt-2" />
    </div>

    <!-- Name -->
    <div class="mt-4">
      <x-input-label for="nama" :value="__('Nama')" />
      <x-text-input id="nama" class="block w-full mt-1" type="text" name="nama" :value="old('nama')" required autofocus autocomplete="off" />
      <x-input-error :messages="$errors->get('nama')" class="mt-2" />
    </div>

    <!-- Telegram -->
    <div class="mt-4">
      <x-input-label for="id_telegram" :value="__('ID Telegram')" />
      <x-text-input id="id_telegram" class="block w-full mt-1" type="text" name="id_telegram" :value="old('id_telegram')" required autofocus autocomplete="off" />
      <x-input-error :messages="$errors->get('id_telegram')" class="mt-2" />
    </div>

    <!-- Jabatan -->
    <div class="mt-2">
      <label for="jabatan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jabatan</label>
      <select id="jabatan" name="jabatan" autocomplete="jabatan" class="block w-full rounded-md border-0 py-1.5 text-gray-900 bg-slate-300 dark:text-white dark:bg-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
        <option value="">Pilih Jabatan</option>
        @foreach($jabatans as $jabatan)
        <option value="{{ $jabatan->jabatan }}">{{ ucwords(strtolower($jabatan->jabatan)) }}</option>
        @endforeach
      </select>
    </div>

    <!-- Email Address -->
    <div class="mt-4">
      <x-input-label for="email" :value="__('Email')" />
      <x-text-input id="email" class="block w-full mt-1" type="email" name="email" :value="old('email')" required autocomplete="username" />
      <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <!-- Password -->
    <div class="mt-4">
      <x-input-label for="password" :value="__('Password')" />

      <x-text-input id="password" class="block w-full mt-1" type="password" name="password" required autocomplete="new-password" />

      <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <!-- Confirm Password -->
    <div class="mt-4">
      <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

      <x-text-input id="password_confirmation" class="block w-full mt-1" type="password" name="password_confirmation" required autocomplete="new-password" />

      <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
    </div>

    <div class="flex items-center justify-end mt-4">
      <a class="text-sm text-gray-600 underline rounded-md dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
        {{ __('Already registered?') }}
      </a>

      <x-primary-button class="ms-4">
        {{ __('Register') }}
      </x-primary-button>
    </div>
  </form>
</x-guest-layout>
