@extends('admin.layouts.panel')

@section('title', 'Dashboard')

@section('subtitle', 'Control Panel')

@section('stylesheets')
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('apex/apexcharts.css') }}" />
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{ asset('adminlte/component/daterangepicker/daterangepicker.css') }}">
@endsection



@section('content')
<div class="container-dashboard pl-4 pr-4">
	<div class="row mt-3">
		<div class="col-md-6 col-sm-12 col-lg-3">
    		<div class="infobox-3 nunito">
                <div class="info-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
				</div>
				{{-- @foreach($attendances as $attendance) --}}
					<h5 class="info-heading text-right mb-0">{{$attendances}}</h5>
                	<p class="info-text text-right mb-3">{{__('dashboard.attnapr')}}</p>
					<a class="info-link mb-0 text-xs" href="{{route('attendanceapproval.index')}}">{{__('dashboard.see_all')}} {{__('dashboard.attnapr')}} <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></a>
				{{-- @endforeach --}}
            </div>
    	</div>
    	<div class="col-md-6 col-sm-12 col-lg-3">
    		<div class="infobox-3 nunito">
                <div class="info-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-flag"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
                </div>
				<h5 class="info-heading text-right mb-0">{{$leaves}}</h5>
                <p class="info-text text-right mb-3">{{__('dashboard.leaveapr')}}</p>
                <a class="info-link mb-0 text-xs" href="{{route('leaveapproval.indexapproval')}}">{{__('dashboard.see_all')}} {{__('dashboard.leaveapr')}} <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></a>
            </div>
    	</div>
    	<div class="col-md-6 col-sm-12 col-lg-3">
    		<div class="infobox-3 nunito">
                <div class="info-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </div>
				<h5 class="info-heading text-right mb-0">{{$contracts}}</h5>
                <p class="info-text text-right mb-3">{{__('dashboard.conexp')}}</p>
                <a class="info-link mb-0 text-xs contract" href="#" onclick="contract()">{{__('dashboard.see_all')}} {{__('dashboard.conexp')}} <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></a>
            </div>
    	</div>
    	<div class="col-md-6 col-sm-12 col-lg-3">
    		<div class="infobox-3 nunito">
                <div class="info-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                </div>
				<h5 class="info-heading text-right mb-0">{{$documents}}</h5>
                <p class="info-text text-right mb-3">{{__('dashboard.docexp')}}</p>
                <a class="info-link mb-0 text-xs" onclick="documentexpired()" href="#">{{__('dashboard.see_all')}} {{__('dashboard.docexp')}} <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></a>
            </div>
    	</div>
	</div>
	<div class="row min-20">
		<div class="col-md-6 col-lg-4 col-sm-12">
			<div class="infobox-3 nunito">
				<h5 class="text-center info-heading-count m-0">{{ $yesterdayAttendance }}</h5>
				<div class="text-center">{{__('dashboard.yescount')}}</div>
			</div>
			<div class="row mt-30">
				<div class="col">
					<div class="infobox-3 nunito pt-3 pl-0 pr-0 pb-3">
						<div id="chartDonut"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6 col-lg-8 col-sm-12">
			<div class="infobox-3 nunito">
				<h5 class="text-center mt-3 custom-title">{{__('dashboard.bydept')}}</h5>
				<div id="chart"></div>
			</div>
		</div>
	</div>
	<div class="row mt-2">
		<div class="col-lg-6">
			<div class="card nunito">
	            <div class="card-header" style="background-color:#263e8a;color: white; ">
	                <h3 class="card-title">{{__('dashboard.otreport')}}</h3>
	            </div>
	            <div class="card-body table-responsive p-0">
	            	<table class="table table-striped table-valign-middle scrollable">
						<thead>
							<tr>
								<th style="width: 40%;">{{__('dashboard.dept')}}</th>
								<th style="width: 30%;" class="text-right">{{__('dashboard.person')}}</th>
								<th style="width: 30%;" class="text-center">{{__('dashboard.avghour')}}</th>
							</tr>
						</thead>
						<tbody class="depart">
							@foreach ($yesterdayOvertime as $item)
								<tr>
									<td style="width: 40%;">{{ $item->name }}</td>
									<td style="width: 30%;" class="text-right">{{ $item->person }}</td>
									<td style="width: 30%;" class="text-center">{{ round($item->average) }} {{__('dashboard.avghour')}}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
	            </div>
	        </div>
		</div>
		<div class="col-lg-6">
			<div class="row">
				<div class="col-lg-12">
					<div class="infobox-2 nunito pl-3">
						<h5 class="info-heading mb-0 text-center">{{ number_format($estimateSalary, 0, ',', '.') }}</h5>
						<p class="info-text text-center">{{__('dashboard.monthly')}}</p>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="infobox-2 nunito pl-3">
						<h5 class="info-heading mb-0 text-center">{{ number_format($estimateSalaryHourly, 0, ',', '.') }}</h5>
						<p class="info-text text-center">{{__('dashboard.hourly')}}</p>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="infobox-3 nunito">
						<h5 class="text-center mt-3 custom-title">{{__('dashboard.grossslr')}}</h5>
						<div id="chartSalary"></div>
					</div>
				</div>
			</div>
			{{-- <div class="row">
				<div class="col-lg-12">
					<div class="infobox-3 nunito">
						<h5 class="text-center mt-3 custom-title">Age Group &amp; Gender</h5>
						<div id="chartAge"></div>
					</div>
				</div>
			</div> --}}
		</div>
	</div>
</div>

{{-- Modal Contract --}}
<div class="modal fade" id="add_contract" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{__('employee.employ')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered w-100 datatable" id="contract" style="width:100%">
                    <thead>
                        <tr>
							<th width="20">#</th>
                            <th width="100">{{__('general.name')}}</th>
                            <th width="100">{{__('department.dep')}}</th>
                            <th width="100">{{__('employee.workcomb')}}</th>
                            <th width="100">{{__('dashboard.end_contract')}}</th>
                            <th width="100">{{__('general.desc')}}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Yesterday Attendance By Dept --}}
<div class="modal fade" id="dept-attendance" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{__('dashboard.detail_bydept')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
				<input type="hidden" name="department_name" value="">
                <table class="table table-striped table-bordered w-100" id="attendance-table">
                    <thead>
                        <tr>
							<th width="20">#</th>
                            <th width="100">{{__('department.depname')}}</th>
                            <th width="100">{{__('dashboard.notatten')}}</th>
                            <th width="100">{{__('dashboard.attend')}}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Document --}}
<div class="modal fade" id="add_document" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{__('document.docmanaj')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered w-100 table-document" id="document">
                    <thead>
                        <tr>
							<th width="10">#</th>
							<th width="100">{{__('document.nodoc')}}</th>
							<th width="100">{{__('document.docname')}}</th>
							<th width="100">{{__('document.exp_date')}}</th>
							<th width="100">{{__('document.pic')}}</th>
							<th width="50">{{__('general.file')}}</th>
							<th width="50">Status</th>
							<th width="10">#</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- Modal preview Document --}}
<div class="modal fade" id="show-document" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
						<embed id="url-document" src="" style="height:500px;width:500px;object-fit:contain;padding:20px">
						<a href="" class="btn btn-{{ config('configs.app_theme') }} rounded-0 download-button" download>Download</a>
        </div>
    </div>
</div>
{{-- Modal Add Document --}}
<div class="modal fade" id="edit_document" tabindex="-1" role="dialog" aria-hidden="true" role="dialog"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="overlay-wrapper">
                <div class="modal-header">
                    <h4 class="modal-title">Edit {{__('document.docmanaj')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_document" class="form-horizontal" method="post" autocomplete="off">
                            
                        <div class="form-group col-sm-12">
                            <label for="code" class="control-label">{{__('document.nodoc')}} <b class="text-danger">*</b></label>
                            <input type="code" class="form-control" id="code" name="code" placeholder="{{__('document.nodoc')}}">
                        </div>
                        <div class="d-flex">
                            <div class="form-group col-sm-6">
                                <label for="name" class="control-label">{{__('document.docname')}}<b class="text-danger">*</b></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="{{__('document.docname')}}"
                                    required>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="date" class="control-label">{{__('document.exp_date')}}<b class="text-danger">*</b></label>
                                <input type="text" class="form-control" id="expired_date" name="expired_date" placeholder="{{__('document.exp_date')}}"
                                    required>
                            </div>
                        </div>
                        <div class="d-flex">
                            
                            <div class="form-group col-sm-6">
                                <label for="nilai" class="control-label">{{__('dashboard.remain_days')}}<b class="text-danger">*</b></label>
                                <input type="number" class="form-control" id="nilai" name="nilai" placeholder="{{__('document.reminder')}}"
                                    required>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="pic" class="control-label">{{__('document.pic')}}</label><b class="text-danger">*</b></label>
                                <input type="text" class="form-control" id="pic" name="pic" placeholder="{{__('document.pic')}}"
                                    required>
                            </div>
                            
                        </div>
                        <div class="form-group col-sm-12">
                            <label for="file" class="control-label">{{__('general.file')}} <b class="text-danger">*</b></label>
                            <input type="file" class="form-control" name="file" id="file"
                                accept="image/*" />
                            <a id="document-preview" onclick="showDocument(this)" href="#" data-url="" class="mt-2"></a>
                        </div>
                        <div class="form-group col-sm-12">
                            <label for="description" class="control-label">{{__('general.desc')}}</label>
                            <textarea name="description" id="description" class="form-control"
                                placeholder="{{__('general.desc')}}"></textarea>
                        </div>
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" />
                    </form>
                </div>
                <div class="modal-footer">
                    <button form="form_document" type="submit"
                        class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="Simpan"><i
                            class="fa fa-save"></i></button>
                </div>
                <div class="overlay d-none">
                    <i class="fa fa-2x fa-sync-alt fa-spin"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script src="{{ asset('apex/apexcharts.min.js') }}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('adminlte/component/daterangepicker/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('adminlte/component/daterangepicker/daterangepicker.js') }}"></script>
<script type="text/javascript" src="{{ asset('adminlte/component/blockui/jquery.blockUI.js') }}"></script>
<script>
	var departement = [];
	var attend = [];
	var notAttend = [];
	var gross = [];
	var label = @JSON($donutChart['label']);
	var data = @JSON($donutChart['data']);
	var labelAttend = [];
	@foreach($yesterdayAttendancebyDept as $value)
	departement.push('{!! $value->name !!}');
	attend.push('{!! $value->attend !!}');
	notAttend.push('{!! $value->notAttend !!}');
	@endforeach
	@foreach($grossSalaryYear as $gross)
	gross.push('{!! round($gross) !!}');
	@endforeach
</script>
<script type="text/javascript" src="{{ asset('js/dashboard.js') }}"></script>
<script type="text/javascript">
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
<script type="text/javascript">
	function contract(){
			$('#add_contract').modal('show');
	}
	$('#add_contract').on('shown.bs.modal', function () {
		dataTable.columns.adjust().responsive.recalc();
	})
	function documentexpired(){
		$('#add_document').modal('show');
	}
	$('#add_document').on('shown.bs.modal', function () {
		dataTableDocument.columns.adjust().responsive.recalc();
	})
	$(document).ready(function () {
			var url = "{!! url('admin/' . 'attendanceapproval') !!}";
			var urlNow = window.location.href;
			if (urlNow.indexOf(url) === -1) {
					localStorage.clear();
			}
	});
	$(document).on('click','.editdocument',function(){
		var id = $(this).data('id');
		$.ajax({
			url:`{{url('admin/documentmanagement')}}/${id}/edit`,
			method:'GET',
			dataType:'json',
			beforeSend:function(){
				$('#box-menu .overlay').removeClass('d-none');
			},
		}).done(function(response){
			$('#box-menu .overlay').addClass('d-none');
			if(response.status){
				$('#edit_document .modal-title').html('Edit Document');
				$('#edit_document').modal('show');
				$('#form_document')[0].reset();
				$('#form_document .invalid-feedback').each(function () { $(this).remove(); });
				$('#form_document .form-group').removeClass('has-error').removeClass('has-success');
				$('#form_document input[name=_method]').attr('value','PUT');
				$('#form_document input[name=name]').attr('value',response.data.name);
				$('#form_document input[name=nilai]').attr('value',response.data.nilai);
				$('#form_document input[name=code]').attr('value',response.data.code);
				$('#form_document input[name=file]').attr('value',response.data.file);
				$('#form_document input[name=pic]').attr('value',response.data.pic);
				$('#form_document input[name=expired_date]').attr('value',response.data.expired_date);
				$('#form_document textarea[name=description]').html(response.data.description);
				$('#document-preview').html(response.data.file).attr('data-url',response.data.link);
				$('#form_document').attr('action',`{{url('admin/documentmanagement/')}}/${response.data.id}`);
			}          
		}).fail(function(response){
			var response = response.responseJSON;
			$('#box-menu .overlay').addClass('d-none');
			$.gritter.add({
				title: 'Error!',
				text: response.message,
				class_name: 'gritter-error',
				time: 1000,
			});
		})	
	});
	$("#form_document").validate({
			errorElement: 'div',
			errorClass: 'invalid-feedback',
			focusInvalid: false,
			highlight: function (e) {
			$(e).closest('.form-group').removeClass('has-success').addClass('was-validated has-error');
			},

			success: function (e) {
			$(e).closest('.form-group').removeClass('has-error').addClass('has-success');
			$(e).remove();
			},
			errorPlacement: function (error, element) {
			if(element.is(':file')) {
					error.insertAfter(element.parent().parent().parent());
			}else
			if(element.parent('.input-group').length) {
					error.insertAfter(element.parent());
			}
			else{
					error.insertAfter(element);
			}
			},
			submitHandler: function() {
			$.ajax({
					url:$('#form_document').attr('action'),
					method:'post',
					data: new FormData($('#form_document')[0]),
					processData: false,
					contentType: false,
					dataType: 'json',
					beforeSend:function(){
					$('.overlay').removeClass('d-none');
					}
			}).done(function(response){
							$('.overlay').addClass('d-none');
							if(response.status){
							$('#edit_document').modal('hide');
							dataTableDocument.draw();
							}
							else{
							$.gritter.add({
									title: 'Warning!',
									text: response.message,
									class_name: 'gritter-warning',
									time: 1000,
							});
							}
							return;
			}).fail(function(response){
					$('.overlay').addClass('d-none');
					var response = response.responseJSON;
					$.gritter.add({
							title: 'Error!',
							text: response.message,
							class_name: 'gritter-error',
							time: 1000,
					});
			})
			}
	});
	
	function showDocument(e){
		$('#url-document').attr("src",$(e).data('url'));
		$('.download-button').attr("href",$(e).data('url'));
		$('#show-document').modal('show');
  	}
	$(function(){
		$('#expired_date').daterangepicker({
      singleDatePicker: true,
      timePicker: false,
      timePickerIncrement: 1,
      locale: {
      format: 'YYYY/MM/DD'
      }
    });
		attendanceDepartment = $('#attendance-table').DataTable( {
				processing: true,
				serverSide: true,
				filter:false,
				info:false,
				lengthChange:true,
				responsive: true,
				paging: false,
				order: [[ 1, "asc" ]],
				ajax: {
					url: "{{route('dashboard.departmentdetail')}}",
					type: "GET",
					data:function(data){
						var department_name = $('input[name=department_name]').val();
						data.department_name = department_name;
					}
				},
				columnDefs:[
				{
					orderable: false,targets:[0]
				},
				{ className: "text-center", targets: [2,3] },
				
			],
			columns: [
				{ 
					data: "no" 
				},
				{ 
					data: "department_name" 
				},
				{ 
					data: "not_attend" 
				},
				{ 
					data: "attend" 
				}
			]
		});
		dataTable = $('.datatable').DataTable( {
				stateSave:true,
				processing: true,
				serverSide: true,
				filter:false,
				info:false,
				lengthChange:true,
				responsive: true,
				order: [[ 5, "asc" ]],
				ajax: {
					url: "{{route('dashboard.readcontract')}}",
					type: "GET",
					data:function(data){
						var employee_id = $('input[name=employee_name]').val();
						var nid = $('input[name=nid]').val();
						var date = $('input[name=birthday]').val();
						var department = $('input[name=department]').val();
						var position = $('input[name=position]').val();
						var workgroup = $('input[name=workgroup]').val();
						var day = $('select[name=day] option').filter(':selected').val()
						var month = $('select[name=month] option').filter(':selected').val();
						var year = $('select[name=year] option').filter(':selected').val();
						data.employee_id = employee_id;
						data.nid = nid;
						data.date = date;
						data.department = department;
						data.workgroup = workgroup;
						data.position = position;
						data.day = day;
						data.month = month;
						data.year = year;
					}
				},
				columnDefs:[
				{
					orderable: false,targets:[0]
				},
				{ className: "text-right", targets: [0] },
				{ className: "text-center", targets: [0,4] },
				{
					render: function ( data, type, row ) {
					return `<a href="{{url('admin/employees')}}/${row.id}/">${row.name}</a><br>NIK: ${row.nik}`;
					},targets: [1]
				}
				
			],
			columns: [
				{ 
					data: "no" 
				},
				{ 
					data: "name" 
				},
				{ 
					data: "department_name" 
				},
				{ 
					data: "workgroup_name" 
				},
				{ 
					data: "end_date" 
				},
				{ 
					data: "employee_desc" 
				},
			]
		});
		dataTableDocument = $('.table-document').DataTable( {
        stateSave:true,
        processing: true,
        serverSide: true,
        filter:false,
        info:false,
        lengthChange:true,
        responsive: true,
        order: [[ 6, "asc" ]],
        ajax: {
            url: "{{route('dashboard.readdocument')}}",
            type: "GET",
            data:function(data){
                var code = $('#form-search').find('input[name=code]').val();
                var code = $('#form-search').find('input[name=name]').val();
                data.code = code;
                data.name = name;
            }
        },
        columnDefs:[
					{
						orderable: false,targets:[0]
					},
					{ className: "text-right", targets: [0] },
                    { className: "text-center", targets: [5,6] },
                   
					{
					render: function (data, type, row) {
						// return `<a href="${row.file}" target="_blank"><img class="img-fluid" src="${row.file}" height=\"100\" width=\"150\"/><a/>`
							return `<a onclick="showDocument(this)" data-url="${row.link}" href="#"><span class="badge badge-info">Preview</span><a/>`
					},
					targets: [5]
                    },
                    {
						render: function (data, type, row) {
							if (row.status == 'Active') {
								return `<span class="badge badge-success">Active</span>`
							}else{
								return `<span class="badge badge-danger">Expired</span>`
							}
						},
						targets: [6]
										},
					{ render: function ( data, type, row ) {
						return `<div class="dropdown">
							<button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
							<i class="fa fa-bars"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right">
								<li><a class="dropdown-item editdocument" href="#" data-id="${row.id}"><i class="fa fa-pencil-alt mr-2"></i> Edit</a></li>
							</ul>
							</div>`
					},targets: [7]
					}
                    
				
				],
				columns: [
                    { data: "no" },
                    { data: "code"},
                    { data: "name" },
										{ data: "expired_date"},
										{ data: "pic"},
                    { data: "file" },
										{ data: "status" },
										{ data: "id"}
				]
    });
	});
</script>
@endsection