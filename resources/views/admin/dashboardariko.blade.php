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
<div class="container-dashboard pl-4 pr-4">
	<div class="row">
		<div class="col-md-6 col-lg-4 col-sm-12">
			<div class="infobox-3 nunito">
				<h5 class="text-center info-heading-count m-0">400</h5>
				<div class="text-center">Yesterday Attendance Count</div>
				<div class="row mt-3 dashed-top">
					<div class="col pr-2 pl-0 pt-2 dashed-right">
						<h5 class="text-center info-plus m-0">+25</h5>
						<div class="text-center text-xs">2 Days Before Attendance</div>
					</div>
					<div class="col pr-0 pl-2 pt-2">
						<h5 class="text-center info-min m-0">-25</h5>
						<div class="text-center text-xs">2 Days Before Attendance</div>
					</div>
				</div>
			</div>
			<div class="row mt-30">
				<div class="col">
					<div class="infobox-3 nunito pt-0 pl-0 pr-0 pb-3">
						<div id="chartDonut"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6 col-lg-8 col-sm-12">
			<div class="infobox-3 nunito">
				<h5 class="text-center mt-3 custom-title">Yesterday Attendance Report by Departement</h5>
				<div id="chart"></div>
			</div>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-md-6 col-sm-12 col-lg-3">
    		<div class="infobox-3 nunito">
                <div class="info-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <h5 class="info-heading text-right mb-0">52</h5>
                <p class="info-text text-right mb-3">Attendance Approval</p>
                <a class="info-link mb-0 text-xs" href="">See all leave approval <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></a>
            </div>
    	</div>
    	<div class="col-md-6 col-sm-12 col-lg-3">
    		<div class="infobox-3 nunito">
                <div class="info-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-flag"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
                </div>
                <h5 class="info-heading text-right mb-0">4</h5>
                <p class="info-text text-right mb-3">Leave Approval</p>
                <a class="info-link mb-0 text-xs" href="">See all leave approval <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></a>
            </div>
    	</div>
    	<div class="col-md-6 col-sm-12 col-lg-3">
    		<div class="infobox-3 nunito">
                <div class="info-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </div>
                <h5 class="info-heading text-right mb-0">10</h5>
                <p class="info-text text-right mb-3">Contract Expired Soon</p>
                <a class="info-link mb-0 text-xs" href="">See all leave approval <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></a>
            </div>
    	</div>
    	<div class="col-md-6 col-sm-12 col-lg-3">
    		<div class="infobox-3 nunito">
                <div class="info-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                </div>
                <h5 class="info-heading text-right mb-0">20</h5>
                <p class="info-text text-right mb-3">Document Expired Soon</p>
                <a class="info-link mb-0 text-xs" href="">See all document expired soon <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></a>
            </div>
    	</div>
	</div>
	<div class="row mt-2">
		<div class="col-lg-6">
			<div class="card nunito">
	            <div class="card-header bg-dark-green">
	                <h3 class="card-title">Yesterday Overtime Report</h3>
	            </div>
	            <div class="card-body table-responsive p-0">
	            	<table class="table table-striped table-valign-middle scrollable">
						<thead>
							<tr>
								<th width="60%">Departement</th>
								<th class="text-right">Person</th>
								<th class="text-center">Avg Hour</th>
							</tr>
						</thead>
						<tbody class="depart">
							<tr>
								<td>HRD</td>
								<td class="text-right">0</td>
								<td class="text-center">8 Hour</td>
							</tr>
							<tr>
								<td>Accounting Management</td>
								<td class="text-right">231</td>
								<td class="text-center">0 Hour</td>
							</tr>
							<tr>
								<td>Carton Corrugator</td>
								<td class="text-right">112</td>
								<td class="text-center">3 Hour</td>
							</tr>
							<tr>
								<td>Carton Finish Team</td>
								<td class="text-right">86</td>
								<td class="text-center">2 Hour</td>
							</tr>
							<tr>
								<td>Driver</td>
								<td class="text-right">345</td>
								<td class="text-center">2 Hour</td>
							</tr>
							<tr>
								<td>General Management</td>
								<td class="text-right">176</td>
								<td class="text-center">2 Hour</td>
							</tr>
							<tr>
								<td>Human Management</td>
								<td class="text-right">351</td>
								<td class="text-center">3 Hour</td>
							</tr>
							<tr>
								<td>Inner Digital Team</td>
								<td class="text-right">129</td>
								<td class="text-center">1 Hour</td>
							</tr>
						</tbody>
					</table>
	            </div>
	        </div>
		</div>
		<div class="col-lg-6">
			<div class="row">
				<div class="col-lg-6">
					<div class="infobox-2 nunito pl-3">
		                <div class="info-icon">
		                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
		                </div>
		                <h5 class="info-heading text-right mb-0">4,5 Years</h5>
		                <p class="info-text">Average Employee Tenure</p>
		            </div>
				</div>
				<div class="col-lg-6">
					<div class="infobox-2 nunito pl-3">
		                <div class="info-icon">
		                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
		                </div>
		                <h5 class="info-heading text-right mb-0">29 Years</h5>
		                <p class="info-text">Average Employee Age</p>
		            </div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<div class="infobox-3 nunito">
						<h5 class="text-center mt-3 custom-title">Age Group &amp; Gender</h5>
						<div id="chartAge"></div>
					</div>
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
     	series: [{
	      name: 'Attendance',
	      data: [1.5, 2.1, 2.9, 3.8, 3.9, 4.2, 4, 4.3]
	    },
	    {
	      name: 'Not Attendance',
	      data: [-0.8, -1.05, -1.06, -1.18, -1.4, -4.4, -4.1, -4]
	    }],
	    chart: {
	      type: 'bar',
	      height: 377,
	      stacked: true,
	      toolbar: {
	          show: false,
	      }
	    },
	    colors: ['#06623b', '#ec0101'],
	    plotOptions: {
	      bar: {
	        horizontal: true,
	      },
	    },
	    dataLabels: {
	      enabled: true
	    },
	    legend: {
	      show: true,
	      offsetX: 90,
      	  offsetY: 0,
      	  inverseOrder: true,
	      horizontalAlign: 'center', 
	  	},
	    xaxis: {
	      categories: ['HRD', 'Marketing', 'Logistik', 'Cartoon', 'Digital Printing', 'Shopping Bag', 'Design', 'Maintenance'],
	      labels: {
          	show: false,
	        formatter: function (val) {
	          return Math.abs(Math.round(val)) + "%"
	        }
	      },
	    },
	    tooltip: {
	    	inverseOrder: true,
	    	fillSeriesColor: false
	    }
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();

    var optionsDonut = {
		series: [80, 10, 5, 5],
		chart: {
			type: 'donut',
	    },
	    colors: ['#06623b', '#ec0101', '#feb019', '#f6830f'],
	    dataLabels: {
			enabled: true,
			style: {
				fontSize: "9px",
				fontFamily: "Nunito, sans-serif",
				fontWeight: "bold"
			}
	    },
	    labels: ['Present', 'Alpha', 'Leave', 'Off'],
	    legend: {
	    	position: 'bottom'
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
		}]
    };

    var chartDonut = new ApexCharts(document.querySelector("#chartDonut"), optionsDonut);
    chartDonut.render();

    var optionsAge = {
     	series: [{
	      name: 'Attendance',
	      data: [1.5, 2.1, 2.9, 3.8, 4, 4.3]
	    },
	    {
	      name: 'Not Attendance',
	      data: [-0.8, -1.05, -1.4, -4.4, -4.1, -4]
	    }],
	    chart: {
	      type: 'bar',
	      height: 216,
	      stacked: true,
	      toolbar: {
	          show: false,
	      }
	    },
	    colors: ['#ff414d', '#51adcf'],
	    plotOptions: {
	      bar: {
	        horizontal: true,
	      },
	    },
	    dataLabels: {
	      enabled: true
	    },
	    fill: {
		  type: 'gradient',
		  gradient: {
		    shade: 'light',
		    type: "horizontal",
		    shadeIntensity: 0.5,
		    gradientToColors: undefined, // optional, if not defined - uses the shades of same color in series
		    inverseColors: true,
		    opacityFrom: 1,
		    opacityTo: 1,
		    stops: [0, 50, 100],
		    colorStops: []
		  }
		},
	    legend: {
	      show: true,
      	  inverseOrder: true,
      	  position: 'right',
	      horizontalAlign: 'center', 
	  	},
	    xaxis: {
	      categories: ['15 - 24', '25 - 34', '35 - 44', '45 - 54', '55 - 64', '65 ++'],
	      labels: {
          	show: false,
	        formatter: function (val) {
	          return Math.abs(Math.round(val)) + "%"
	        }
	      },
	    },
	    tooltip: {
	    	inverseOrder: true,
	    	fillSeriesColor: false
	    }
    };

    var chartAge = new ApexCharts(document.querySelector("#chartAge"), optionsAge);
    chartAge.render();

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