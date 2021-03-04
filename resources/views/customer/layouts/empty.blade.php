<!DOCTYPE html>
<html>
    <head>
		<title>@yield('title') - {{config('configs.app_name')}}</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
		<link href="{{asset('assets/css/font-awesome.min.css')}}" rel="stylesheet">
		<link href="{{asset('assets/css/plugins/textSpinners/spinners.css')}}" rel="stylesheet">
		<link href="{{asset('assets/css/plugins/gritter/jquery.gritter.min.css')}}" rel="stylesheet">
		<link href="{{asset('assets/css/animate.css')}}" rel="stylesheet">
		<link href="{{asset('assets/css/style.css')}}" rel="stylesheet">
		@yield('stylesheets')
    </head>
    <body class="gray-bg">
		@yield('content')
		<script src="{{asset('assets/js/jquery-3.1.1.min.js')}}"></script>
		<script src="{{asset('assets/js/popper.min.js')}}"></script>
		<script src="{{asset('assets/js/bootstrap.js')}}"></script>
		<script src="{{asset('assets/js/inspinia.js')}}"></script>
		<script src="{{asset('assets/js/library.js')}}"></script>
		<script src="{{asset('assets/js/plugins/pace/pace.min.js')}}"></script>
		<script src="{{asset('assets/js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>
		<script src="{{asset('assets/js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
		<script src="{{asset('assets/js/plugins/blockui/jquery.blockUI.js')}}"></script>
        <script src="{{asset('assets/js/plugins/gritter/jquery.gritter.min.js')}}"></script>
		@stack('scripts')
    </body>
</html>