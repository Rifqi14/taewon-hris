<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style>
		body {
			font-family: Arial, Helvetica, sans-serif;
			/*color: #17d06f;*/
		}

		h3 {
			font-size: 24pt !important;
			margin: 0;
		}

		h5 {
			font-size: 11pt !important;
			margin: 0;
			letter-spacing: 0;
			font-weight: 600;
			text-transform: uppercase;
		}

		p {
			font-size: 12pt !important;
			margin: 1.5pt;
		}

		hr {
			border: none; 
			height: 2pt;
			background: black; 
		}
		
		@page {
			size: A4 landscape;
			margin: 0;
		}

		@media print{
			* {
				-webkit-print-color-adjust:exact !important;
				color-adjust: exact !important;
			}
		}

		.table {
			border-spacing: 0;
			border-collapse: collapse;
			/*border: 1px solid #000;*/
		}

		.main-table {
			margin-right: 3rem;
    		margin-left: 3rem;
		}

		/*.main-table tbody:before {
		    content: "@";
		    display: block;
		    line-height: 10px;
		    text-indent: -99999px;
		}*/

		.main-table tr > th {
			border: 1px solid #06623b;
		    border-bottom: none;
		    /*border-right: none;*/
		    padding: 0.4em;
		    font-size: 10pt;
		    background-color: #28a745;
		    color: white;
		}

		.main-table tr > td {
			margin-top: 5px;
			border: 1px solid #000;
			/*border-bottom: none;*/
			/*border-right: none;*/
			font-size: 10pt;
			padding: 0.5em;
		}

		.left {
			flex: 0.4;
			margin-left: 3rem;
		}

		.right {
			flex: 1;
			margin-left: 1.2rem;
			margin-right: 3rem;
		}

		.info-table tr > th {
			/* border: 1px solid #06623b; */
		    border-bottom: none;
		    /* border-right: none; */
		    padding: 0.4em;
		    font-size: 9pt;
		    /* background-color: #28a745; */
		    /* color: white; */
		    text-transform: uppercase;
		}

		.info-table tr > td {
			margin-top: 5px;
		    border: 1px solid #000;
		    /* border-bottom: none; */
		    /* border-right: none; */
		    font-size: 10pt;
		    padding: 0.2em;
		    font-weight: 500;
		}

		.info-table tr.checking > td {
			margin-top: 5px;
		    border: 1px solid #000;
		    font-size: 9pt;
		    padding: 0.2em;
		    font-weight: 500;
		}

		.info-table tr.value > td {
			height: 3rem;
		}

		.table-header-info {
			text-transform: uppercase;
		}

		.table-header-info tr {
			width: 10%;
		}

		.table-header-info td {
			font-size: 10pt !important;
			font-weight: 500;
			line-height: 15px;
		}

		.table-header-info  tr td:first-child {
			width: 35%;
			font-size: 10pt !important;
			font-weight: 500;
			line-height: 15px;
		}

		.table-header-info tr td:nth-child(2) {
			width: 3%;
			text-align: left;
			font-size: 10pt !important;
			font-weight: 500;
			line-height: 15px;
		}

		.table-header-info  tr td:last-child {
			width: 62%;
			text-align: left;
			font-size: 10pt !important;
			font-weight: 500;
			line-height: 15px;
		}

		.flexbox {
			display: flex;
			flex-direction: column;
			flex-wrap: nowrap;
			height: 100%;
		}

		.flexbox-row {
			display: flex;
		}

		.space-between {
			align-content: space-between;
		}

		.f-1 {
			flex: 1;
		}

		.m-0 {
			margin:0 !important;
		}

		.ml-1 {
			margin-left: 1em;
		}

		.mt-2 {
			margin-top: 1.25em;
		}

		.mt-3 {
			margin-top: 3rem;
		}

		.container {
			padding: 8px;
		}

		.img-logo {
			width: 50px;
			height: 50px;
		}

		.info-report {
			font-size: 9pt;
			font-weight: 600;
			/*color: #28a745;*/
			display: flex;
		}

		.info-report > ul {
			flex: 1;
		}
	</style>
</head>
<body onload="window.print();">
	<div class="container">
		<div class="flexbox space-between">
			<div class="flexbox-row">
				<img class="img-logo" src="{{asset('img/logo.png')}}" >
				<div class="flexbox ml-1 f-1">
					<h5>PT. Bosung Indonesia</h5>
					<font style="font-size: 10pt; margin-top: 0.35em">Jl. Raya Rajeg Desa Sindang Sari Kec. Pasar Kemis</font>
					<font style="font-size: 10pt; margin-top: 0.35em">Tangerang</font>
					<div class="flexbox-row">
						<font style="font-size: 10pt; margin-top: 0.35em">Telp&nbsp;:</font>
						<font style="font-size: 10pt; margin-top: 0.35em">&nbsp;(021) 5935-1001 (Hunting)</font>
					</div>
					<div class="flexbox-row">
						<font style="font-size: 10pt; margin-top: 0.35em">Fax&nbsp;:</font>
						<font style="font-size: 10pt; margin-top: 0.35em">&nbsp;(021) 5935-0022, 0033</font>
					</div>
				</div>
				<div class="flexbox f-1" style="align-self: center; margin-top: 1rem;">
					<h2 style="margin-left: 3.5em; margin-bottom: 0; margin-top: 0;">SURAT JALAN</h2>
					<font style="margin-left: 5.3em; font-size: 12pt; font-weight: 600; margin-top: 0.35em;">NO&nbsp;:&nbsp;{{ $deliveryorder->do_number }}</font>
				</div>
				<div class="flexbox f-1" style="align-items: flex-end; padding-right: 3rem;">
					<table class="table-header-info">
						<tr>
							<td>Tgl</td>
							<td>:</td>
							<td>{{ date_format(date_create($deliveryorder->date), 'd-M-Y') }}</td>
						</tr>
						<tr>
							<td>No Polisi</td>
							<td>:</td>
							<td>{{ $deliveryorder->police_no }}</td>
						</tr>
						<tr>
							<td>Driver</td>
							<td>:</td>
							<td>{{ @$deliveryorder->driver->name }}</td>
						</tr>
						<tr>
							<td>Kepada Yth</td>
							<td>:</td>
							<td>{{ $deliveryorder->destination }}</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class="flexbox mt-3">
			<table class="table main-table">
				<thead>
					<tr>
						<th width="10%">PO NO</th>
						<th width="40%" align="left">Item Name</th>
						<th width="10%">Size</th>
						<th width="10%">QTY</th>
						<th width="20%">Order No</th>
						<th width="10%">Remarks</th>
					</tr>
				</thead>
				<tbody>
					@foreach($deliveryorder->deliveryorderdetail as $items)
						<tr>
							<td align="center">{{ $items->po_number }}</td>
							<td align="left">{{ $items->item_name }}</td>
							<td align="center">{{ $items->size }}</td>
							<td align="center">{{ $items->qty }}</td>
							<td align="center">{{ $deliveryorder->do_number }}</td>
							<td align="center">{{ $items->remarks }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		<div class="flexbox mt-3">
			<div class="flexbox-row">
				<table class="table info-table left">
					<tr class="checking">
						<td align="center" colspan="5">PT BOSUNG INDONESIA<br/>TANGERANG - INDONESIA</td>
					</tr>
					<tr class="checking">
						<td align="center" colspan="5">BAGIAN SECURITY</td>
					</tr>
					<tr class="checking">
						<td rowspan="2" align="center" width="20%">Tanggal</td>
						<td colspan="3" align="center">Jam</td>
						<td rowspan="2" align="center" width="20%">Paraf</td>
					</tr class="checking">
					<tr>
						<td align="center" width="20%">Berangkat</td>
						<td align="center" width="20%">Tiba</td>
						<td align="center" width="20%">Keluar</td>
					</tr>
					<tr class="value">
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</table>
				<div class="right">
					<table class="table info-table">
						<thead>
							<tr>	
								<th>Lembar</th>
								<th width="1%">:</th>
								<th>1. Penagihan</th>
								<th>2. Customer</th>
								<th>3. Administrasi</th>
								<th>4. Marketing</th>
								<th>5. Arsip</th>
								<th>6. Security</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="2" align="center">Diterima</td>
								<td align="center">Pengemudi</td>
								<td align="center">Kontrol</td>
								<td align="center">Hitung</td>
								<td align="center">Muat</td>
								<td align="center">Kepala Gudang</td>
								<td align="center">Manajer</td>
							</tr>
							<tr class="value">
								<td colspan="2"></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						</tbody>
					</table>
					<div class="info-report">
						<ul>
							<li>Barang telah diterima dengan cukup dan kondisi baik</li>
							<li>Barang yang sudah diterima tidak bisa dikembalikan</li>
							<li>Complain lebih dari 1 minggu setelah barang diterima tidak akan diproses</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>