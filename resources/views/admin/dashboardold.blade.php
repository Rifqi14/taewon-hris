@extends('admin.layouts.panel')

@section('title', 'Dashboard')

@section('subtitle', 'Control Panel')

@section('stylesheets')
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('apex/apexcharts.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('adminlte/component/daterangepicker/daterangepicker.css') }}">
@endsection



@section('content')
<div class="container-dashboard p-4">
	<div class="row">
		<div class="col-md-4 col-lg-4 col-sm-12">
	        <div class="row">
	        	<div class="col">
	        		<div class="infobox-3 nunito">
	                    <div class="info-icon">
	                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-flag"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
	                    </div>
	                    <h5 class="info-heading text-right mb-0">40</h5>
	                    <p class="info-text text-right mb-2">Leave Approval</p>
	                    <a class="info-link mb-0" href="">See all leave approval <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></a>
	                </div>
	        	</div>
	        </div>
	        <div class="row">
	        	<div class="col">
	        		<div class="infobox-3 nunito">
	                    <div class="info-icon">
	                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
	                    </div>
	                    <h5 class="info-heading text-right mb-0">54</h5>
	                    <p class="info-text text-right mb-2">Attendance Approval</p>
	                    <a class="info-link mb-0" href="">See all attendace approval <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></a>
	                </div>
	        	</div>
	        </div>
	    </div>
	    <div class="col-md-4 col-lg-4 col-sm-12">
	    	<div class="card mt-4 card-dashboard nunito">
	            <div class="card-header bg-dark-green">
	                <h3 class="card-title">Contract Expired</h3>
	            </div>
	            <div class="card-body table-responsive p-0">
					<table class="table table-valign-middle">
						<tbody>
							<tr>
								<td width="70%" class="pl-3 pb-0">
									<dl>
										<dt>Dwi Yulianto</dt>
										<dd>( Manager Marketing )</dd>
									</dl>
								</td>
								<td width="30%" class="text-bold pr-0 text-secondary">
									20 Oct 2020
								</td>
							</tr>
							<tr>
								<td width="70%" class="pl-3 pb-0">
									<dl>
										<dt>Dwi Yulianto</dt>
										<dd>( Manager Marketing )</dd>
									</dl>
								</td>
								<td width="30%" class="text-bold pr-0 text-secondary">
									20 Oct 2020
								</td>
							</tr>
							<tr>
								<td width="70%" class="pl-3 pb-0">
									<dl>
										<dt>Dwi Yulianto</dt>
										<dd>( Manager Marketing )</dd>
									</dl>
								</td>
								<td width="30%" class="text-bold pr-0 text-secondary">
									20 Oct 2020
								</td>
							</tr>
							<tr>
								<td width="70%" class="pl-3 pb-0">
									<dl>
										<dt>Dwi Yulianto</dt>
										<dd>( Manager Marketing )</dd>
									</dl>
								</td>
								<td width="30%" class="text-bold pr-0 text-secondary">
									20 Oct 2020
								</td>
							</tr>
							<tr>
								<td width="70%" class="pl-3 pb-0">
									<dl>
										<dt>Dwi Yulianto</dt>
										<dd>( Manager Marketing )</dd>
									</dl>
								</td>
								<td width="30%" class="text-bold pr-0 text-secondary">
									20 Oct 2020
								</td>
							</tr>
							<tr>
								<td width="70%" class="pl-3 pb-0">
									<dl>
										<dt>Dwi Yulianto</dt>
										<dd>( Manager Marketing )</dd>
									</dl>
								</td>
								<td width="30%" class="text-bold pr-0 text-secondary">
									20 Oct 2020
								</td>
							</tr>
						</tbody>
					</table>
				</div>
	        </div>
	    </div>
	    <div class="col-md-4 col-lg-4 col-sm-12">
	    	<div class="card mt-4 nunito">
	            <div class="card-header bg-dark-green">
	                <h3 class="card-title">Workgroup Combination</h3>
	            </div>
	            <div class="card-body table-responsive p-0">
					<table class="table table-striped table-valign-middle scrollable">
						<thead>
							<tr>
								<th>Type</th>
								<th class="text-right">Total</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Outsourcing 1</td>
								<td class="text-right">1342</td>
							</tr>
							<tr>
								<td>Outsourcing 2</td>
								<td class="text-right">351</td>
							</tr>
							<tr>
								<td>Outsourcing 1</td>
								<td class="text-right">1342</td>
							</tr>
							<tr>
								<td>Outsourcing 2</td>
								<td class="text-right">351</td>
							</tr>
							<tr>
								<td>Outsourcing 1</td>
								<td class="text-right">1342</td>
							</tr>
							<tr>
								<td>Outsourcing 2</td>
								<td class="text-right">351</td>
							</tr>
						</tbody>
					</table>
				</div>
	        </div>
	    </div>
	</div>
	<div class="row">
		<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
			<div class="card nunito">
	            <div class="card-header bg-dark-green p-1">
	                <h3 class="card-title p-2">Attendance Summary</h3>
	                <div class="dropdown no-caret float-right">
			            <button class="btn btn-transparent-dark text-white btn-icon dropdown-toggle" id="dropdownMenuButton" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			                {{-- <i class="fas fa-ellipsis-v"></i> --}}
			            </button>
			            <div class="dropdown-menu dropdown-menu-right animated--fade-in-up p-2" aria-labelledby="dropdownMenuButton">
			            	<div class="form-group">
								<label for="exampleInputEmail1">Filter Date :</label>
								{{-- <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email"> --}}
			                	<input type="text" name="filterDate" class="datepicker text-right nunito form-control p-1 text-sm" /> 
							</div>
			            </div>
			        </div>
	            </div>
	            <div class="card-body" id="attendace-summary">
	            	<div id="chart" class="pt-1"></div>
	            </div>
	        </div>
		</div>
		<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
			<div class="card nunito">
	            <div class="card-header bg-dark-green">
	                <h3 class="card-title">Employee by departement</h3>
	            </div>
	            <div class="card-body table-responsive p-0">
	            	<table class="table table-striped table-valign-middle scrollable">
						<thead>
							<tr>
								<th>Departement</th>
								<th class="text-right">Total Employee</th>
							</tr>
						</thead>
						<tbody class="depart">
							<tr>
								<td>Accounting Management</td>
								<td class="text-right">231</td>
							</tr>
							<tr>
								<td>Carton Corrugator</td>
								<td class="text-right">112</td>
							</tr>
							<tr>
								<td>Carton Finish Team</td>
								<td class="text-right">86</td>
							</tr>
							<tr>
								<td>Driver</td>
								<td class="text-right">345</td>
							</tr>
							<tr>
								<td>General Management</td>
								<td class="text-right">176</td>
							</tr>
							<tr>
								<td>Human Management</td>
								<td class="text-right">351</td>
							</tr>
							<tr>
								<td>Inner Digital Team</td>
								<td class="text-right">129</td>
							</tr>
						</tbody>
					</table>
	            </div>
	        </div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('apex/apexcharts.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('adminlte/component/daterangepicker/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('adminlte/component/daterangepicker/daterangepicker.js') }}"></script>
<script type="text/javascript" src="{{ asset('adminlte/component/blockui/jquery.blockUI.js') }}"></script>
<script type="text/javascript">
	var options = {
		series: [44, 55, 35, 43],
		chart: {
			height: 350,
			type: 'pie',
			toolbar: {
	          show: false,
	        }
		},
		dataLabels: {
			enabled: true,
			style: {
				fontSize: "15px",
				fontFamily: "Nunito, sans-serif",
				fontWeight: "bold"
			}
        },
		labels: ['Present', 'Alpha', 'Leave', 'Off'],
		legend: {
			fontSize: '15px',
			markers: {
				width: 15,
				height: 15
			},
			itemMargin: {
				horizontal: 3
			},
		},
		responsive: [{
			breakpoint: 480,
			options: {
				chart: {
					width: '100%'
				},
				legend: {
					position: 'bottom'
				}
			}
		}],
	};

	var chart = new ApexCharts(document.querySelector("#chart"), options);
	chart.render();

	$('.datepicker').daterangepicker({
	    singleDatePicker: true,
	    locale: {
	      format: 'DD/MM/YYYY',
	      cancelLabel: 'Clear'
	    },
	});

	$('input[name="filterDate"]').on('apply.daterangepicker', function(ev, picker) {
		$('#dropdownMenuButton').trigger('click');
		blockMessage('#attendace-summary', "Please Wait Getting data  . . .", "#fff");
		setTimeout(() => {
			$('#attendace-summary').unblock();
		}, 2000);
	});

	function blockMessage(element,message,color){
		$(element).block({
	    	message: '<span class="text-semibold"><i class="icon-spinner4 spinner position-left"></i>&nbsp; '+message+'</span>',
	        overlayCSS: {
	            backgroundColor: color,
	            opacity: 0.8,
	            cursor: 'wait'
	        },
	        css: {
	            border: 0,
	            padding: '10px 15px',
	            color: '#fff',
	            width: 'auto',
	            '-webkit-border-radius': 2,
	            '-moz-border-radius': 2,
	            backgroundColor: '#333'
	        }
	    });
	}
</script>
@endsection