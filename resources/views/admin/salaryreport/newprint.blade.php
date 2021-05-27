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
				border: 1px;
			}
		}

		body {
			font-family: Arial, Helvetica, sans-serif;
		}

		h3 {
			font-size: 24pt !important;
			margin: 0;
		}

		h5 {
			font-size: 12pt !important;
			margin: 0;
		}
		.text-header {
			font-size: 13pt !important;
			margin: 0;
		}
		.text-content {
			font-size: 10pt !important;
			margin: 0;
		}

		p {
			font-size: 12pt !important;
			margin: 1.5pt;
		}

		th {
			font-size: 12pt !important;
			letter-spacing: 1px;
		}
		.employee-table {
			width: 100%;
			margin-top: 16pt;
		}
        .employee-section {
			margin-bottom: 15pt;
		}
        .th-slip {
			width: 12%;
            text-align:center;
            height:2%;
		}
        .th-first {
			vertical-align: middle;
            text-align: center;
            width:10%;
		}
		.container {
			padding-top: 2%;
			padding-left: 5%;
			padding-right: 5%;
			padding-bottom: 2%;
		}

		.text-right {
			text-align: right !important
		}

		@page {
			size: 20.5cm 9.5cm;
		}

		tbody>tr:first-child>td {
			padding-top: 10px;
		}

		tbody>tr:last-child>td {
			padding-bottom: 10px;
		}
	</style>
</head>

<body>
	@foreach ($salaries as $salary)
	@php
	$gross = 0;
	$deduction = 0;
	@endphp
	@endforeach
	<div class="container-fluid pl-5 pr-5" >
		{{-- <div class="logo"> --}}
		{{-- <img class="img-logo" src="{{asset('img/logo.png')}}"> --}}
		{{-- <h3 style="text-align: center;">PAY SLIP</h3> --}}
		{{-- </div> --}}
		{{-- <hr> --}}
		{{-- <h5 class="text-right">{{ changeDateFormat('F Y', $salary->period) }}</h5> --}}
		<div class="employee-section">
			<table class="employee-table" style="width:100%; font-size: 50px;">
				<tbody>
					<tr>
						<th class="text-header" style="width: 5%">Nama</th>
						<th class="text-header" style="width: 25%">: {{$salary->employee->name}}</th>
						<th class="text-header" style="width: 5%">Periode</th>
						<th class="text-header" style="width: 25%">: {{date('Y-m', strtotime($salary->period))}} Bulan</th>

					</tr>
					<tr>
						<th class="text-header" style="width: 5%">No.Peg</th>
						<th class="text-header" style="width: 25%">: 070296</th>
						<th class="text-header" style="width: 5%">Bagian</th>
						<th class="text-header" style="width: 25%">: {{ $salary->employee->title->name }}</th>
					</tr>
					
				</tbody>
			</table>
		</div>
        <table class="table table-bordered p-0">
                <tr>
                    <th rowspan="2" class="th-first p-0 text-content">Month</th>
                    <th class="th-slip p-0 text-content">Gaji Pokok</th>
                    <th class="th-slip p-0 text-content">{{$coordinate12->name}}</th>
                    <th class="th-slip p-0 text-content">{{$coordinate13 ? $coordinate13->name : 'Kosong'}}</th>
                    <th class="th-slip p-0 text-content">{{$coordinate14 ? $coordinate14->name : 'Kosong'}}</th>
                    <th rowspan="2" style="width:24%; text-align:center; height:2%;" class="p-0 text-content"></th>
                    {{-- <th rowspan="2">v</th> --}}
                    <th class="th-slip p-0 text-content">Jumlah</th>
                </tr>
                <tr>
                    {{-- <td rowspan="1">Month</td> --}}
                    <td class="p-0 text-content" style="text-align: right;">{{ number_format($salary->gross_salary, 0, '.', ',') }}</td>
                    <td class="p-0 text-content" style="text-align: right;">{{number_format($coordinate12value, 0, '.', ',')}}</td>
					<td class="p-0 text-content" style="text-align: right;">{{number_format($coordinate13value, 0, '.', ',')}}</td>
                    <td class="p-0 text-content" style="text-align: right;">{{number_format($coordinate14value, 0, '.', ',')}}</td>
                    <td class="p-0 text-content" style="text-align: right;">5.144.844</td>
                </tr>
        </table>
        <table class="table table-bordered">
                <tr>
                    <th rowspan="2" class="th-first p-0 text-content">Perincian lembur</th>
                    <td class="th-slip p-0 text-content">150%</td>
                    <td class="th-slip p-0 text-content">200%</td>
                    <td class="th-slip p-0 text-content">300%</td>
                    <td class="th-slip p-0 text-content">400%</td>
                    <th class="th-slip p-0 text-content">Total Jam</th>
                    <th class="th-slip p-0 text-content">Price Lmbr</th>
                    <th class="th-slip p-0 text-content">Tot Rp</th>
                </tr>
                <tr>
                    {{-- <td rowspan="1">Month</td> --}}
                    <td class="p-0 text-right text-content">22.0</td>
                    <td class="p-0 text-right text-content" >44.0</td>
                    <td class="p-0 text-right text-content" ></td>
                    <td class="p-0 text-right text-content" ></td>
                    <td class="p-0 text-right text-content" >121.0</td>
                    <td class="p-0 text-right text-content">29.739</td>
                    <td class="p-0 text-right text-content" >3.598,419</td>
                </tr>
        </table>
        <table class="table table-bordered">
                <tr>
                    <th rowspan="2" class="th-first p-0 text-content">Rincian hari kerja</th>
                    <th class="th-slip p-0 text-content">kerja aktif</th>
                    <th class="th-slip p-0 text-content">Libur</th>
                    <th class="th-slip p-0 text-content">{{$coordinate33 ? $coordinate33->leave_name : 'Kosong'}}</th>
                    <th class="th-slip p-0 text-content">{{$coordinate34 ? $coordinate34->leave_name : 'Kosong'}}</th>
                    <th class="th-slip p-0 text-content">{{$coordinate35 ? $coordinate35->leave_name : 'Kosong'}}</th>
                    <th class="th-slip p-0 text-content">{{$coordinate36 ? $coordinate36->leave_name : 'Kosong'}}</th>
                    <th class="th-slip p-0 text-content">Tot hari</th>
                </tr>
                <tr>
                    {{-- <td rowspan="1">Month</td> --}}
                    <td class="p-0 text-right text-content">25</td>
                    <td class="p-0 text-right text-content">6</td>
                    <td class="p-0 text-right text-content">{{number_format($coordinate43value, 0, '.', ',')}}</td>
                    <td class="p-0 text-right text-content">{{number_format($coordinate44value, 0, '.', ',')}}</td>
                    <td class="p-0 text-right text-content">{{number_format($coordinate45value, 0, '.', ',')}}</td>
                    <td class="p-0 text-right text-content">{{number_format($coordinate46value, 0, '.', ',')}}</td>
                    <td class="p-0 text-right text-content">31</td>
                </tr>
        </table>
        <h5><b>PENDAPATAN</b></h5>
        <table class="table table-bordered">
                <tr>
                    <th rowspan="2" class="th-first p-0 text-content">Pendapatan</th>
                    <th class="th-slip p-0 text-content">G.Bulan</th>
                    <th class="th-slip p-0 text-content">Uang Lmbr</th>
                    <th class="th-slip p-0 text-content">{{$coordinate43 ? $coordinate43->name : 'Kosong'}}</th>
                    <th class="th-slip p-0 text-content">{{$coordinate44 ? $coordinate44->name : 'Kosong'}}</th>
                    <th class="th-slip p-0 text-content">{{$coordinate45 ? $coordinate45->name : 'Kosong'}}</th>
                    <th class="th-slip p-0 text-content">{{$coordinate46 ? $coordinate46->name : 'Kosong'}}</th>
                    <th class="th-slip p-0 text-content">Jumlah</th>
                </tr>
                <tr>
                    {{-- <td rowspan="1">Month</td> --}}
                    <td class="p-0 text-right text-content">25</td>
                    <td class="p-0 text-right text-content">6</td>
                    <td class="p-0 text-right text-content"></td>
                    <td class="p-0 text-right text-content"></td>
                    <td class="p-0 text-right text-content"></td>
                    <td class="p-0 text-right text-content"></td>
                    <td class="p-0 text-right text-content">31</td>
                </tr>
                <tr>
                    <th rowspan="2" class="p-0 text-content" style="vertical-align: middle; text-align: center;">Potongan</th>
                    <th class="th-slip p-0 text-content">{{$coordinate51 ? $coordinate51->leave_name : 'Kosong'}}</th>
                    <th class="th-slip p-0 text-content">{{$coordinate52 ? $coordinate52->leave_name : 'Kosong'}}</th>
                    <th class="th-slip p-0 text-content">{{$coordinate53 ? $coordinate53->leave_name : 'Kosong'}}</th>
                    <th class="th-slip p-0 text-content">{{$coordinate54 ? $coordinate54->name : 'Kosong'}}</th>
                    <th class="th-slip p-0 text-content">{{$coordinate55 ? $coordinate56->name : 'Kosong'}}</th>
                    <th class="th-slip p-0 text-content">Pinjaman ACC</th>
                    <th class="th-slip p-0 text-content">Jumlah</th>
                </tr>
                <tr>
                    {{-- <td rowspan="1">Month</td> --}}
                    <td class="p-0 text-right text-content" >1</td>
                    <td class="p-0 text-right text-content" >1</td>
                    <td class="p-0 text-right text-content" >1</td>
                    <td class="p-0 text-right text-content" >{{number_format($coordinate54value, 0, '.', ',')}}</td>
                    <td class="p-0 text-right text-content" >{{number_format($coordinate55value, 0, '.', ',')}}</td>
                    <td class="p-0 text-right text-content" >1</td>
                    <td class="p-0 text-right text-content" >1</td>
                </tr>
                <tr>
                    <th colspan="8" class="p-0 text-right text-content">Gaji Bersih</th>
                </tr>
                 <tr>
                    <td colspan="8" class="p-0 text-right text-content">8.504,529</td>
                </tr>
        </table>
		{{-- <table class="gross-table" style="padding-top: 20pt">
			<thead>
				<tr>
					<td colspan="2" style="padding-right: 20pt;" class="header-slip">PAY DETAIL</td>
				</tr>
			</thead>
			<tbody>
				@foreach ($salary->salarydetail as $key => $item)
				@if ($item->type == 1)
				@php $gross = $gross+1; @endphp
				<tr>
					<td>{{ $item->description }}</td>
					<td style="padding-right: 20pt;" class="amount">{{ number_format($item->total, 0, '.', ',') }}</td>
				</tr>
				@endif
				@endforeach

				@if($deduction > $gross)
				@for($x=0; $x < $deduction->$gross; $x++) <tr>
						<td class="description">&nbsp;</td>
						<td class="description">&nbsp;</td>
					</tr>
					@endfor
					@endif
			</tbody>
			<tfoot>
				<tr>
					<td class="header-slip text-total">Gross Salary</td>
					<td style="padding-right: 20pt;" class="header-slip net-salary">{{ number_format($salary->gross_salary, 0, '.', ',') }}</td>
				</tr>
				<tr>
					<td class="net-total">NET SALARY</td>
					<td style="padding-right: 20pt;" class="net-amount">{{ number_format($salary->net_salary, 0, '.', ',') }}</td>
				</tr>
				<tr>
					<td rowspan="2" colspan="2" class="company-logo"></td>
				</tr>
			</tfoot>
		</table>
		<table class="deduction-table" style="padding-top: 20pt">
			<thead>
				<tr>
					<td colspan="2" style="padding-left: 20pt;" class="header-slip">DEDUCTION</td>
				</tr>
			</thead>
			<tbody>
				@foreach ($salary->salarydetail as $key => $item)
				@if ($item->type == 0)
				<tr>
					<td style="padding-left: 20pt;">{{ $item->description }}</td>
					<td class="amount description">{{ number_format($item->total, 0, '.', ',') }}</td>
				</tr>
				@endif
				@endforeach

				@if($gross > $deduction)
				@for($x=0; $x < $gross-$deduction; $x++) <tr>
					<td class="description">&nbsp;</td>
					<td class="description">&nbsp;</td>
					</tr>
					@endfor
					@endif
			</tbody>
			<tfoot>
				<tr>
					<td style="padding-left: 20pt;" class="header-slip text-total">Deduction Total</td>
					<td class="header-slip deduction-salary">{{ number_format($salary->deduction, 0, '.', ',') }}</td>
				</tr>
			</tfoot>
		</table> --}}
	</div>
	<div class="return-page"></div>
	{{-- @endforeach --}}
</body>
<script src="{{asset('adminlte/component/jquery/jquery.min.js')}}"></script>
<script type="text/javascript">
	$(window).on('load', function() {
    window.print()
  });
</script>

</html>