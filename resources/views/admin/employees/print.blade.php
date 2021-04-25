<!DOCTYPE html>
<html>

<head>
	<title></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha2/css/bootstrap.min.css" rel="stylesheet">
	<style>
		@media print {
			body {
				margin: 0;
				/* height: 9.5cm; */
				/* width: 20.5cm; */
				/* height: 85.60mm;
				width: 53.98mm; */
				border: 1px;
			}
		}

		body {
			font-family: Arial, Helvetica, sans-serif;
		}

		@page {
			size: 13cm 18cm;
			/* size: A2; */
			/* width: 9.5cm;
			height: 13.5cm; */
		}
		.card{
			width: 100%;
			border: 1px solid #dc3545;
			min-height: 200px;
			border-radius: 20px;
		}
		.card .sub-card{
			width: 80%;
			margin-left: 10%;
			margin-top: 10px;
			margin-bottom: 10px;
		}
		.card .sub-card .logo{
			width: 80%;
			margin-left: 10%;
			margin-top: 30px;
			border-radius: 10px;
		}
		.card .sub-card .logo img{
			width: 100%;
			border-radius: 10px;
		}
		.card .sub-card .picture{
			width: 80%;
			margin-left: 10%;
			margin-top: 20px;
			text-align: center;
		}
		.card .sub-card .picture img{
			width: 100%;
		}
		.card .sub-card .data-diri{
			width: 80%;
			margin-left: 10%;
			margin-top: 20px;
			text-align: center;
			margin-bottom: 30px;
		}
		.card .sub-card .data-diri .name{
			font-size: 28px;
			font-weight: bold;
			color:#dc3545;
		}
		.card .sub-card .data-diri .nik{
			margin-top: -10px;
		}
		.card .sub-card .data-diri .department{
			font-size: 28px;
			font-weight: bold;
		}
		.card .sub-card .data-diri .workgroup{
			font-size: 28px;
			font-weight: bold;
		}
	</style>
</head>

<body>
	@foreach($employees as $employee)
	<div class="card">
		<div class="sub-card">
			<div class="logo">
				<img src="{{asset('img/logo-perusahaan.png')}}" alt="">
			</div>
			<div class="picture">
				@if($employee->photo == 'img/no-image.png')
				<img src="{{asset($employee->photo)}}" alt="">
				@else
				<img src="{{asset('img/no-image.png')}}" alt="">
				@endif
			</div>
			<div class="data-diri">
				<div class="name">{{ $employee->name }}</div>
				<div class="nik"><small>{{ $employee->nik}}</small></div>
				<div class="department">{{$employee->department->name}}</div>
				<div class="workgroup">{{$employee->workgroup->name}}</div>
			</div>
		</div>
	</div>
	@endforeach
</body>
<script src="{{asset('adminlte/component/jquery/jquery.min.js')}}"></script>
<script type="text/javascript">
	$(window).on('load', function() {
    window.print()
  });
</script>

</html>