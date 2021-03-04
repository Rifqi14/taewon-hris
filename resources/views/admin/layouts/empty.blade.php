<!DOCTYPE html>
<html>
    <head>
		<title>@yield('title') - {{config('configs.app_name')}}</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="stylesheet" href="{{asset('adminlte/component/fontawesome-free/css/all.min.css')}}">
		<link rel="stylesheet" href="{{asset('adminlte/component/Ionicons/css/ionicons.min.css')}}">
		<link rel="stylesheet" href="{{asset('adminlte/css/adminlte.min.css')}}">
		@yield('stylesheets')
    </head>
    <body class="hold-transition @yield('class') text-sm">
		@yield('content')
		<script src="{{asset('adminlte/component/jquery/jquery.min.js')}}"></script>
		<script src="{{asset('adminlte/component/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
		@stack('scripts')
    </body>
</html>