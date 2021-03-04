<!DOCTYPE html>
<html>

<head>
	<title></title>
	<style>
		@media print {
			body {
				margin: 0;
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

		p {
			font-size: 12pt !important;
			margin: 1.5pt;
		}

		td,
		th {
			font-size: 12pt !important;
			letter-spacing: 1px;
			/* line-height: 20pt; */
		}

		hr {
			border: none;
			height: 2pt;
			background: black;
		}

		.employee-table {
			width: 100%;
			margin-top: 16pt;
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

		.header-slip {
			font-weight: normal !important;
			letter-spacing: 2pt;
			border-top: 2pt solid black;
			border-bottom: 2pt solid black;
			padding: 5pt 0pt 5pt 0pt;
		}

		.text-total {
			letter-spacing: 0pt;
			padding-top: 5pt;
			padding-bottom: 5pt;
			font-weight: bold !important;
		}

		.net-salary {
			letter-spacing: 0pt;
			font-weight: bold !important;
			text-align: right;
		}

		.deduction-salary {
			letter-spacing: 0pt;
			font-weight: bold !important;
			text-align: right;
		}

		.amount {
			text-align: right;
		}

		.net-total {
			font-weight: bold;
			height: 20pt;
		}

		.net-amount {
			font-weight: bold;
			text-align: right;
			height: 20pt;
			vertical-align: middle;
		}

		.company-logo {
			width: 50%;
			padding-top: 50pt;
		}

		.logo {
			margin-right: 200px;
		}

		.img1 {
			width: 50px;
			height: 50px;
		}

		.company-logo img {
			max-width: 50%;
		}

		.date,
		.signature {
			height: 20pt;
			text-align: right;
			font-weight: bold;
		}

		.signature {
			height: 130px;
			vertical-align: bottom;
		}

		.slip-table {
			height: 100%;
		}

		.gross-table {
			width: 50%;
			float: left;
			border-spacing: 0pt;
		}

		.deduction-table {
			width: 50%;
			float: right;
			border-spacing: 0pt;
		}

		.img-logo {
			height: 70px;
			width: 70px;
			position: relative;
			margin: 0 auto;
			display: block;
			bottom: 20px;
		}

		.firstColumn {
			padding-top: 10px;
		}

		.return-page {
			page-break-after: always;
		}

		@page {
			size: a4 landscape;
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

	@foreach ($salary->salarydetail as $item)
	@if ($item->type == 0)
	@php $deduction = $deduction+1; @endphp
	@endif
	@endforeach
	<div class="container">
		{{-- <div class="logo"> --}}
		<img class="img-logo" src="{{asset('img/logo.png')}}">
		<h3 style="text-align: center;">PAY SLIP</h3>
		{{-- </div> --}}
		<hr>
		<h5 class="text-right">{{ changeDateFormat('F Y', $salary->period) }}</h5>
		<div class="employee-section">
			<table class="employee-table">
				<tbody>
					<tr>
						<td style="width: 5%">Name</td>
						<td style="width: 25%">: {{ $salary->employee->name }}</td>
						<td style="width: 5%">NPWP</td>
						<td style="width: 25%">: {{ $salary->employee->npwp }}</td>

					</tr>
					<tr>
						<td style="width: 5%">NIK</td>
						<td style="width: 25%">: {{ $salary->employee->nid }}</td>
						<td style="width: 5%">Status</td>
						<td style="width: 25%">: {{ $salary->employee->workgroup->name }}</td>
					</tr>
					<tr>
						{{-- <td style="width: 5%">Join Date</td>
						<td style="width: 25%">: {{ $salary->employee->join_date}}</td> --}}
						<td style="width: 5%">Department</td>
						<td style="width: 25%">: {{ $salary->employee->department->name }}</td>
						<td style="width: 5%">Position</td>
						<td style="width: 25%">: {{ $salary->employee->title->name }}</td>
					</tr>
				</tbody>
			</table>
		</div>
		<table class="gross-table" style="padding-top: 20pt">
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
		</table>
	</div>
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