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
		td, th {
			font-size: 12pt !important;
			margin: 100px;
			border: 1px solid black;
			padding: 0 5px 0 5px;
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
			padding-left: 1%;
			padding-right: 1%;
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
		.logo{
			margin-right: 200px;
		}

		.img1{
			width: 50px;
			height: 50px;
		}

		.company-logo img {
			max-width: 50%;
		}
		.date, .signature {
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
			width: 100%;
			float: left;
			border-spacing: 0pt;
		}
		.deduction-table {
			width: 50%;
			float: right;
			border-spacing: 0pt;
		}
		.img-logo{
			height: 50px;
			width: 50px;
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
			size: legal landscape;
		}
		tbody > tr:first-child > td {
			padding-top: 10px;
		}
		tbody > tr:last-child > td {
			padding-bottom: 10px;
		}
	</style>
</head>
<body>
	<div class="container">
		{{-- <div class="logo"> --}}
			<img class="img-logo" src="{{asset('img/logo.png')}}" >
			<h3 style="text-align: center;">REIMBURSEMENT</h3>
			<table class="gross-table" style="padding-top: 20pt;">
				<thead>
					<tr>
						<th style="text-align: left">Slip Date</th>
						<th style="text-align: left">Date</th>
						<th style="text-align: left">Police No</th>
						<th style="text-align: left">Driver</th>
						<th style="text-align: left">Destination</th>
						<th style="text-align: left">Departure</th>
						<th style="text-align: left">Arrival</th>
						<th style="text-align: right">KM Departure</th>
						<th style="text-align: right">KM Arrival</th>
						<th style="text-align: right">Distance</th>
						<th style="text-align: right">Total Money</th>
						<th style="text-align: right">Fuel</th>
						<th style="text-align: right">Toll</th>
						<th style="text-align: right">Parking</th>
						<th style="text-align: right">Etc</th>
						<th style="text-align: right">Cash</th>
						<th style="text-align: right">Makan</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($reimbursements as $key => $reimbursement)
					@php
							$cash = $reimbursement->uang_cash && $key == 0 ? $reimbursement->uang_cash : 0;
							$makan = $reimbursement->uang_makan && $key == 0 ? $reimbursement->uang_makan : 0;
							$total = $reimbursement->oil + $reimbursement->toll + $reimbursement->parkir + $reimbursement->etc + $cash + $makan;
					@endphp
					<tr>
						<td>{{ $reimbursement->date }}</td>
						<td>{{ $reimbursement->date_driver }}</td>
						<td>{{ $reimbursement->no_mobil }}</td>
						<td>{{ $reimbursement->supir }}</td>
						<td>{{ $reimbursement->tujuan }}</td>
						<td>{{ $reimbursement->jam_berangkat }}</td>
						<td>{{ $reimbursement->jam_tiba }}</td>
						<td style="text-align: right">{{ $reimbursement->km_berangkat }}</td>
						<td style="text-align: right">{{ $reimbursement->km_tiba }}</td>
						<td style="text-align: right">{{ $reimbursement->km_tiba - $reimbursement->km_berangkat }}</td>
						<td style="text-align: right">{{ $total ? $total : 0 }}</td>
						<td style="text-align: right">{{ $reimbursement->oil ? $reimbursement->oil : 0 }}</td>
						<td style="text-align: right">{{ $reimbursement->toll ? $reimbursement->toll : 0 }}</td>
						<td style="text-align: right">{{ $reimbursement->parkir ? $reimbursement->parkir : 0 }}</td>
						<td style="text-align: right">{{ $reimbursement->etc ? $reimbursement->etc : 0 }}</td>
						<td style="text-align: right">{{ $reimbursement->uang_cash ? $reimbursement->uang_cash : 0 }}</td>
						<td style="text-align: right">{{ $reimbursement->uang_makan ? $reimbursement->uang_makan : 0 }}</td>
					</tr>
					@endforeach
				</tbody>
		</table>
  </div>
  <div class="return-page"></div>
</body>
<script src="{{asset('adminlte/component/jquery/jquery.min.js')}}"></script>
<script type="text/javascript">
  $(window).on('load', function() {
    window.print()
  });
</script>
</html>