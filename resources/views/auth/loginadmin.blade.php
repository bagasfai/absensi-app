<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-beta20
* @link https://tabler.io
* Copyright 2018-2023 The Tabler Authors
* Copyright 2018-2023 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Sign in with illustration - Tabler - Premium and Open Source dashboard template with responsive and high quality UI.</title>
  <!-- CSS files -->
  <link href="{{asset('tabler/dist/css/tabler.min.css?1692870487')}}" rel="stylesheet" />
  <link href="{{asset('tabler/dist/css/tabler-flags.min.css?1692870487')}}" rel="stylesheet" />
  <link href="{{asset('tabler/dist/css/tabler-payments.min.css?1692870487')}}" rel="stylesheet" />
  <link href="{{asset('tabler/dist/css/tabler-vendors.min.css?1692870487')}}" rel="stylesheet" />
  <link href="{{asset('tabler/dist/css/demo.min.css?1692870487')}}" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>

  {{-- <link rel="icon" href="https://mysds.satriadigitalsejahtera.co.id/assets/files/assets/images/logo.png"> --}}
  {{-- <link rel="icon" href="{{asset('assets/img/web-logo.png')}}"> --}}
  {{-- <link rel="icon" href="{{asset('assets/img/app-logo.jpg')}}"> --}}
  <link rel="icon" href="{{asset('assets/img/blm.jpg')}}">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    @import url('https://rsms.me/inter/inter.css');

    :root {
      --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
    }

    body {
      font-feature-settings: "cv03", "cv04", "cv11";
    }

  </style>
</head>
<body class=" d-flex flex-column">

  <script src="{{asset('tabler/dist/js/demo-theme.min.js?1692870487')}}"></script>
  <div class="page page-center">
    <div class="container container-normal py-4">
      <div class="row align-items-center g-4">
        <div class="col-lg">
          <div class="container-tight">
            <div class="text-center mb-4">
              <a href="." class="navbar-brand navbar-brand-autodark"><img src="./static/logo.svg" height="36" alt=""></a>
            </div>
            <a href="#">
              {{-- <img src="https://mysds.satriadigitalsejahtera.co.id/assets/files/assets/images/logo.png" alt="" height="200" width="200"> --}}
              {{-- <img src="{{asset('assets/img/web-logo.png')}}" alt="" height="200" width="200"> --}}
              {{-- <img src="{{asset('assets/img/app-logo.jpg')}}" alt="" height="200" width="200"> --}}
              <img src="{{asset('assets/img/blm.jpg')}}" alt="" height="200" width="200">
            </a>
            <div class="card card-md">
              <div class="card-body">
                <h2 class="h2 text-center mb-4">Login to your account</h2>
                <form action="{{route('loginadmin')}}" method="post" autocomplete="off">
                  @csrf
                  <div class="mb-3">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                  </div>
                  <div class="mb-2">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                  </div>
                  <div class="mb-2">
                    <label class="form-check">
                      <input type="checkbox" class="form-check-input" />
                      <span class="form-check-label">Remember me on this device</span>
                    </label>
                  </div>
                  <div class="form-footer">
                    <button class="w-full text-center inline-flex items-center px-4 py-2 bg-gray-950 dark:bg-gray-950 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-800 focus:bg-gray-700 dark:focus:bg-gray-800 active:bg-gray-900 dark:active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                      {{ __('Log in') }}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg d-none d-lg-block">
          <img src="{{asset('tabler/static/illustrations/undraw_secure_login_pdn4.svg')}}" height="300" class="d-block mx-auto" alt="">
        </div>
      </div>
    </div>
  </div>
  <!-- Libs JS -->
  <!-- Tabler Core -->
  <script src="{{asset('tabler/dist/js/tabler.min.js?1692870487')}}" defer></script>
  <script src="{{asset('tabler/dist/js/demo.min.js?1692870487')}}" defer></script>
</body>
</html>
