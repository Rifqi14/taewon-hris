<!DOCTYPE html>
<html>

<head>
	<title></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha2/css/bootstrap.min.css" rel="stylesheet">
	<style>
		*{
			font-family: sans-serif;
		}

		.in p{
			text-align: center;
			font-weight: bold;
			background-color: #ccc;
			margin: 0;
		}

		table{
			border-collapse:collapse;
			font-size:8pt;
		}
		table tr td{
			padding: 1px;
		}
		table tr th{
			padding: 1px;
		}
		.all{
			font-size:8pt;
		}	

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
		.return-page {
			page-break-after: always;
		}

		@page {
			size: 13cm 20cm;
			/* size: A2; */
			/* width: 9.5cm;
			height: 13.5cm; */
		}
	</style>
</head>

<body>
	{{-- @foreach($employees as $employee)
	<div class="card">
		<div class="sub-card">
			<div class="logo">
				<img src="{{asset(config('configs.app_logo'))}}" alt="">
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
	@endforeach --}}
	@foreach($thrReports as $thrReport)
		<table width="100%" border="0">
			<tr>
				<td style="text-align:center;font-weight: bold;font-size: 13pt;">SLIP THR</td><br>
			</tr>
			<tr>
				<td style="text-align:center;">(Periode : {{ date("d F Y", strtotime($thrReport->year.'-'.$thrReport->month.'-'.date('d'))) }})</td>
			</tr>
		</table>
		<br>
		<table width="100%" border="0">
			<tr>
				<td width="30" style="font-weight: bold;">NAMA</td>
				<td width="10">:</td>
				<td style="" width="300">{{ $thrReport->employee->name}}</td>
				<td width="30" style="font-weight: bold;">TGL SK</td>
				<td width="">:</td>
				<td align="right" width="100" style="">{{ date("Y-m-d") }}</td>
				<td> </td>
			</tr>
			<tr>
				<td valign="top" style="font-weight: bold;">NIK</td>
				<td valign="top" style="">:</td>
				<td valign="top" style="">{{ $thrReport->employee->nid}}</td>
				<td valign="top" style="font-weight: bold;">BAGIAN</td>
				<td valign="top" style="">:</td>
				<td align="right" valign="top" style="">{{ $thrReport->employee->department->name}}</td>
				<td> </td>
			</tr>
		</table>
 
		<table class="all" width="100%" border="0">
			<tr>
				<th align="left" style="border:1px solid #000;">Masa Kerja</th>
				<th align="left" style="border:1px solid #000;">Gaji Pokok</th>
				<th align="left" style="border:1px solid #000;">Tunj Jabatan</th>
				<th align="left" style="border:1px solid #000;">Tunj Sel</th>
				<th align="left" style="border:1px solid #000;">Tunj Ms Kerja</th>
				<th align="left" style="border:1px solid #000;">Jumlah</th>
			</tr>
			@php $total = 0; @endphp
			@foreach($thrReport->thrdetail as $key => $item)
			@php $total= $total + $item->total; @endphp
			<tr>
				<td align="right" style="border:1px solid #000">{{ $thrReport->period}}</td>
				<td align="right" style="border:1px solid #000">{{ ($item->description == "Basic Salary" ? number_format("$item->total", 0,',','.') : "-") }}</td>
				<td align="right" style="border:1px solid #000">{{ ($item->description == "Tunjangan Jabatan" ? number_format("$item->total", 0,',','.') : "-") }}</td>
				<td align="right" style="border:1px solid #000">{{ ($item->description == "Tunjangan Sel" ? number_format("$item->total", 0,',','.') : "-") }}</td>
				<td align="right" style="border:1px solid #000">{{ ($item->description == "Tunjangan Masa Kerja" ? number_format("$item->total", 0,',','.') : "-") }}</td>
				<td align="right" style="border:1px solid #000">{{number_format("$total", 0,',','.')}}</td>
			</tr>
			@endforeach
		</table>
		<br>
		<table class="all" width="100%" border="0">
			<tr>
				<th align="center" style="border:1px solid #000">Thr</th>
				<th align="center" style="border:1px solid #000">Kebijakan</th>
				<th align="center" style="border:1px solid #000">Pph21</th>
				<th align="center" style="border:1px solid #000">Grand Total</th>
			</tr>

			@php $grandTotal = 0; @endphp
			@foreach($thrReport->thrdetail as $key => $item)
			@php $grandTotal = $grandTotal + $item->total; @endphp
			<tr>
				<td align="right" style="border:1px solid #000">{{ ($item->description == "THR" ? number_format("$item->total", 0,',','.') : "-") }}</td>
				<td align="right" style="border:1px solid #000">{{ ($item->description == "Kebijakan" ? number_format("$item->total", 0,',','.') : "-") }}</td>
				<td align="right" style="border:1px solid #000">{{ ($item->description == "PPh 21" ? number_format("$item->total", 0,',','.') : "-") }}</td>
				<td align="right" style="border:1px solid #000">{{number_format("$grandTotal", 0,',','.')}}</td>
			</tr>
			@endforeach
		</table>
		<div class="return-page"></div>
	@endforeach
</body>
<script src="{{asset('adminlte/component/jquery/jquery.min.js')}}"></script>
<script type="text/javascript">
	$(window).on('load', function() {
    window.print()
  });
</script>

</html>