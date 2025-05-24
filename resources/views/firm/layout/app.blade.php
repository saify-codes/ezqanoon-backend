<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="description" content="Responsive Laravel Admin Dashboard Template based on Bootstrap 5">
	<meta name="author" content="NobleUI">
	<meta name="keywords" content="nobleui, bootstrap, bootstrap 5, bootstrap5, admin, dashboard, template, responsive, css, sass, html, laravel, theme, front-end, ui kit, web">

  <title>{{env('APP_NAME', 'app')}}</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
  <!-- End fonts -->
  
  <!-- CSRF Token -->
  <meta name="_token" content="{{ csrf_token() }}">
  
  <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}">

  <!-- plugin css -->
  <link href="{{ asset('assets/fonts/feather-font/css/iconfont.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/tostify/tostify.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/intlTelInput/intlTelInput.min.css') }}" rel="stylesheet" />
  <!-- end plugin css -->

  @stack('plugin-styles')

  <!-- common css -->
  <link href="{{ asset('css/app.css?version=' . env('VERSION')) }}" rel="stylesheet" />
  <link href="{{ asset('css/animations.css') }}" rel="stylesheet" />
  <!-- end common css -->

  @stack('style')
</head>
<body data-base-url="{{url('/')}}">

  <script src="{{ asset('assets/js/spinner.js') }}"></script>

  <div class="main-wrapper" id="app">
    @include('firm.layout.partials.sidebar')
    <div class="page-wrapper">
      @include('firm.layout.partials.header')
      <div class="page-content">

        @if (!Auth::guard('firm')->user()->is_profile_completed && !Request::is('firm/profile'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Action required</strong> Please complete your profile to get listed
            <a class="text-decoration-underline" href="{{route('firm.profile')}}">click here to goto profile page</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
          </div>
        @endif

        {{$slot}}
      </div>
      @include('firm.layout.partials.footer')
    </div>
  </div>

    <!-- base js -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('assets/plugins/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/tostify/tostify.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/tostify/tostify.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/intlTelInput/intlTelInput.min.js') }}"></script>

    <!-- end base js -->

    <!-- plugin js -->
    @stack('plugin-scripts')
    <!-- end plugin js -->

    <!-- common js -->
    <script src="{{ asset('assets/js/template.js') }}"></script>
    <script src="{{ asset('assets/js/firm-notification.js') }}"></script>
    <!-- end common js -->

    @stack('custom-scripts')

    @if (session('subscription_success'))
      <script>
        Swal.fire({
          title: 'Success',
          text: '{{ session('subscription_success') }}',
          icon: 'success'
        });
      </script>
    @endif
</body>
</html>