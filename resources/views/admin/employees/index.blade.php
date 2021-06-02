@extends('admin.layouts.app')

@section('title',__('employee.employ'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<link href="{{asset('adminlte/component/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet">
<style type="text/css">
	.ui-state-active{
        background: #28a745 !important;
        border-color: #28a745 !important;
    }
    .ui-menu {
        overflow: auto;
        height:200px;
    }
	.customcheckbox {
        width: 22px;
        height: 22px;
        background: url("/img/green.png") no-repeat;
        background-position-x: 0%;
        background-position-y: 0%;
        cursor: pointer;
        margin: 0 auto;
    }

    .customcheckbox.checked {
        background-position: -48px 0;
    }
	.customcheckbox input {
        cursor: pointer;
        opacity: 0;
        scale: 1.6;
        width: 22px;
        height: 22px;
        margin: 0;
    }
</style>
@endsection

@push('breadcrump')
<li class="breadcrumb-item active">{{ __('employee.employ') }}</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="card card-{{ config('configs.app_theme') }} card-outline">
				<div class="card-header">
					<h3 class="card-title">{{ __('employee.emplist') }}</h3>
					<!-- tools box -->
					<div class="pull-right card-tools">
						<!-- <a href="{{route('employees.import')}}" class="btn btn-{{ config('configs.app_theme') }} btn-sm text-white" data-toggle="tooltip"
							title="Tambah">
							<i class="fa fa-file-import"></i>
						</a> -->
						<a href="{{route('employees.create')}}" class="btn btn-{{ config('configs.app_theme') }} btn-sm text-white" data-toggle="tooltip" title="{{ __('general.crt') }}"><i class="fa fa-plus"></i></a>

					<a href="{{route('employees.import')}}" class="btn btn-{{ config('configs.app_theme') }} btn-sm" data-toggle="tooltip" title="Import" style="cursor: pointer;"><i class="fa fa-file-import"></i></a>
					<a href="javascript:void(0)" onclick="printmass()" class="btn btn-sm btn-info text-white" title="Print Mass"><i class="fa fa-print"></i></a>
					<a href="javascript:void(0)" onclick="exportemployee()" class="btn btn-primary btn-sm text-white" data-toggle="tooltip" title="Export" style="cursor: pointer;"><i class="fa fa-download"></i></a>
					<!-- <a href="#" onclick="filter()" class="btn btn-default btn-sm" data-toggle="tooltip" title="Search">
						<i class="fa fa-search"></i>
					</a> -->
				</div>
				<!-- /. tools -->
			</div>
			<div class="card-body">
			<form id="form" action="{{ route('salaryreport.store') }}" class="form-horizontal" method="post">
          {{ csrf_field() }}
            <div class="row">
              <input type="hidden" name="user" value="{{ Auth::guard('admin')->user()->id }}">
              <div class="col-md-4">
                <div class="col-md-12">
                  <div class="form-group">
					<label class="control-label" for="employee_name">{{ __('employee.search') }}</label>
					<input type="text" name="employee_name" id="employee_name" class="form-control filter" placeholder="{{ __('employee.search') }}">
					{{-- <select name="employee_name" id="employee_name" class="form-control select2" style="width: 100%" aria-hidden="true" data-placeholder="Employee Name">
						<option value=""></option>
						@foreach ($employees as $employee)
						<option value="{{ $employee->id }}">{{ $employee->name }}</option>
						@endforeach
					  </select> --}}
				  </div>
				  <div id="employee-container"></div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label" for="nid">NIK</label>
                    <input type="text" class="form-control" placeholder="NIK" name="nid" id="nid">
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label" for="birthday">{{ __('employee.birthday') }}</label>
                    <div class="form-row">
                      <div class="col-sm-4">
                          <select class="form-control select2" onclick="" placeholder="Month" multiple name="month" id="month">
						  	<option value=""></option>
                            <option value="01" >Jan</option>
                            <option value="02" >Feb</option>
                            <option value="03" >Mar</option>
                            <option value="04" >Apr</option>
                            <option value="05" >May</option>
                            <option value="06" >Jun</option>
                            <option value="07" >July</option>
                            <option value="08" >Aug</option>
                            <option value="09" >Sep</option>
                            <option value="10" >Oct</option>
                            <option value="11" >Nov</option>
                            <option value="12" >Dec</option>
                          </select>
                      </div>
					  <div class="col-sm-4">
                          <select class="form-control select2" placeholder="Day" multiple name="day" id="day">
						  		<option value=""></option>
                                @for ($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                          </select>
                      </div>
                      <div class="col-sm-4">
                          <div class="input-group">
                              <select name="year" placeholder="Year" class="form-control select2" multiple id="year">
							  	<option value=""></option>
                                @php
                                    $thn_skr = date('Y');
                                @endphp
                                @for ($i = $thn_skr; $i >= 1945; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                              </select>
                          </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="col-md-12">
                  <div class="form-group">
					<label class="control-label" for="department">{{ __('department.dep') }}</label>
					<select name="department" id="department" class="form-control select2" style="width: 100%" aria-hidden="true" multiple data-placeholder="{{ __('department.dep') }}">
						@foreach ($departments as $department)
						<option value="{{ $department->name }}">{{ $department->path }}</option>
						@endforeach
					  </select>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label" for="type">{{ __('employee.position') }}</label>
					<select name="position" id="position" class="form-control select2" style="width: 100%" aria-hidden="true" multiple data-placeholder="{{ __('employee.position') }}">
						@foreach ($titles as $title)
						<option value="{{ $title->id }}">{{ $title->name }}</option>
						@endforeach
					  </select>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label" for="workgroup">{{ __('employee.workcomb') }}</label>
					<select name="workgroup" id="workgroup" class="form-control select2" style="width: 100%" aria-hidden="true" multiple data-placeholder="{{ __('employee.workcomb') }}">
						@foreach ($workgroups as $workgroup)
						<option value="{{ $workgroup->id }}">{{ $workgroup->name }}</option>
						@endforeach
					  </select>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label" for="workgroup">Status</label>
					<select name="status" id="status" class="form-control select2" style="width: 100%" aria-hidden="true" multiple data-placeholder="Status">
						<option value=""></option>
						<option value="1" selected>{{ __('general.actv') }}</option>
						<option value="0">{{ __('general.noactv') }}</option>
					  </select>
                  </div>
                </div>
              </div>
            </div>
          </form>
				<table class="table table-striped table-bordered datatable" style="width:100%">
					<thead>
						<tr>
							<th width="10">#</th>
							<th width="100">NIK</th>
							<th width="250">{{ __('employee.empname') }}</th>
							<th width="150">{{ __('department.dep') }}</th>
							<th width="200">{{ __('employee.position') }}</th>
							<th width="250">{{ __('employee.workcomb') }}</th>
							<th>
								<div class="customcheckbox">
									<input type="checkbox" class="checkall">
								</div>
							</th>
							<th width="100">{{ __('general.act') }}</th>
						</tr>
					</thead>
				</table>
			</div>
			<div class="overlay d-none">
				<i class="fa fa-2x fa-sync-alt fa-spin"></i>
			</div>
		</div>
	</div>

</div>
</div>
<div class="modal fade" id="add-filter" tabindex="-1" role="dialog"  aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">{{ __('general.filter') }}</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
			<div class="modal-body">
				<form id="form-search" autocomplete="off">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label" for="name">{{ __('employee.empname') }}</label>
								<input type="text" name="name" class="form-control" placeholder="{{ __('employee.empname') }}">
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label" for="name">NIK</label>
								<input type="text" name="name" class="form-control" placeholder="NIK">
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label" for="name">Birth Date</label>
								<input type="text" name="name" class="form-control" placeholder="Nama">
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button form="form-search" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="Apply"><i class="fa fa-search"></i></button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="print-mass" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header no-print">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">{{ __('general.print') }}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <iframe id="bodyReplace" scrolling="no" allowtransparency="true"
                        style="width: 69%; border-width: 0px; position: relative; margin: 0 auto; display: block;"
                        onload="this.style.height=(this.contentDocument.body.scrollHeight+45) + 'px';"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script type="text/javascript">
	// function filter(){
	// 	$('#add-filter').modal('show');
	// }
	// $("#day").select2({
	// 	allowClear: true
	// });
	// $("#month").select2();
	// $("#year").select2();
	function printmass() {
        var ids = [];
        $('input[name^=checksalary]').each(function () {
            if (this.checked) {
                ids.push($(this).data('id'));
            }
        });
        if (ids.length <= 0) {
            $.gritter.add({
                title: 'Warning!',
                text: 'No data has been selected yet',
                class_name: 'gritter-error',
                time: 1000,
            });
            return false;
        }
        printpreview(ids);
    }
	function printpreview(ids) {
        $('.overlay').removeClass('d-none');
        $.ajax({
            url: "{{ route('employees.printmass') }}",
            method: 'GET',
            data: {
                id: JSON.stringify(ids)
            },
            success: function (response) {
                $('.overlay').addClass('d-none');
                dataTable.draw();
                $('.customcheckbox').removeClass('checked');
                $('.customcheckbox input').prop('checked', false);
                var iframe = document.getElementById('bodyReplace');
                iframe = iframe.contentWindow || (iframe.contentDocument.document || iframe
                    .contentDocument);
                iframe.document.open();
                iframe.document.write(response);
                iframe.document.close();
            }
        });
    }
	function exportemployee() {
    $.ajax({
        url: "{{ route('employees.export') }}",
        type: 'POST',
        dataType: 'JSON',
        data: $("#form").serialize(),
        beforeSend:function(){
			// $('.overlay').removeClass('d-none');
			waitingDialog.show('Loading...');
        }
    }).done(function(response){
		waitingDialog.hide();
        if(response.status){
          $('.overlay').addClass('d-none');
          $.gritter.add({
              title: 'Success!',
              text: response.message,
              class_name: 'gritter-success',
              time: 1000,
          });
          let download = document.createElement("a");
          download.href = response.file;
          document.body.appendChild(download);
          download.download = response.name;
          download.click();
          download.remove();
        }
        else{
          $.gritter.add({
              title: 'Warning!',
              text: response.message,
              class_name: 'gritter-warning',
              time: 1000,
          });
        }
    }).fail(function(response){
		waitingDialog.hide();
        var response = response.responseJSON;
        $('.overlay').addClass('d-none');
        $.gritter.add({
            title: 'Error!',
            text: response.message,
            class_name: 'gritter-error',
            time: 1000,
        });
    });
  }
	$(function(){
		dataTable = $('.datatable').DataTable( {
			stateSave:true,
			processing: true,
			serverSide: true,
			filter:false,
			info:false,
			lengthChange:true,
			responsive: true,
			order: [[ 6, "asc" ]],
			lengthMenu: [ 100, 250, 500, 1000, 2000 ],
      		pageLength: 100,
			ajax: {
				url: "{{route('employees.read')}}",
				type: "GET",
				data:function(data){
					var employee_id = $('input[name=employee_name]').val();
					var nid = $('input[name=nid]').val();
					var date = $('input[name=birthday]').val();
					var department = $('select[name=department]').val();
					var position = $('select[name=position]').val();
					var workgroup = $('select[name=workgroup]').val();
					var day = $('select[name=day]').val()
					var month = $('select[name=month]').val();
					var year = $('select[name=year]').val();
					var status = $('select[name=status]').val();
					data.employee_id = employee_id;
					data.nid = nid;
					data.date = date;
					data.department = department;
					data.workgroup = workgroup;
					data.position = position;
					data.day = day;
					data.month = month;
					data.year = year;
					data.status = status;
				}
			},
			columnDefs:[
			{
				orderable: false,targets:[0,6]
			},
			{ className: "text-right", targets: [0] },
			{ className: "text-center", targets: [6,7] },
			{
				render: function ( data, type, row ) {
					if (row.status == 1) {
						return `<p>${row.nid}</p>`;
					}else if(row.status == 0 && row.nid == null){
						return `<p style="color: red;">-</p>`
					}
					else{
						return `<p style="color: red;">${row.nid}</p>`;
					}
				},targets: [1]
			},
			{
				render: function ( data, type, row ) {
				return `<a href="{{url('admin/employees')}}/${row.id}/">${row.name}</a>`;
				},targets: [2]
			},
			{
				render: function (data, type, row) {
				return `<label class="customcheckbox">
				<input data-id="${data}" type="checkbox" name="checksalary[]" value="${row.id}"><span class="checkmark"></span>
				</label>`
				},
				targets: [6]
			},
			{ render: function ( data, type, row ) {
				return `<div class="dropdown">
				<button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
				<i class="fa fa-bars"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right">
				<li><a class="dropdown-item edit" href="javascript:void(0)" data-id="${row.id}"><i class="fas fa-pencil-alt mr-2"></i> Edit</a></li>
				<li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> Delete</a></li>
				</ul></div>`
			},targets: [7]
		}
		],
		columns: [
		{ 
			data: "no" 
		},
		{ 
			data: "nid" 
		},
		{ 
			data: "name" 
		},
		{ 
			data: "department_name" 
		},
		{ 
			data: "title_name" 
		},
		{ 
			data: "workgroup_name" 
		},
		{ 
			data: "id" 
		},
		{ 
			data: "id" 
		},
		]
	});
		$(".select2").select2({
			allowClear: true
		});
		$('#form-search').submit(function(e){
			e.preventDefault();
			dataTable.draw();
			$('#add-filter').modal('hide');
		})
		$(document).ready(function(){
			var employees = [
				@foreach($employees as $employee)
                	"{!!$employee->name!!}",
            	@endforeach
			];
			$( "input[name=employee_name]" ).autocomplete({
			source: employees,
			minLength:0,
			appendTo: '#employee-container',
			select: function(event, response) {
				if(event.preventDefault(), 0 !== response.item.id){
					$(this).val(response.item.value);
					dataTable.draw();
				}
			}
			}).focus(function () {
				$(this).autocomplete("search");
			});
			$("input[name=employee_name]").keydown(function(event){
				if(event.keyCode == 13) {
					event.preventDefault();
					$('input[name=employee_name]').autocomplete('close');
					return false;
				}
			});
			var employees = [
				@foreach($employees as $nik)
                	"{!!$nik->nid!!}",
            	@endforeach
			];
			$( "input[name=nid]" ).autocomplete({
			source: employees,
			minLength:0,
			appendTo: '#employee-container',
			select: function(event, response) {
				if(event.preventDefault(), 0 !== response.item.id){
					$(this).val(response.item.value);
					dataTable.draw();
				}
			}
			}).focus(function () {
				$(this).autocomplete("search");
			});
			$("input[name=nid]").keydown(function(event){
				if(event.keyCode == 13) {
					event.preventDefault();
					$('input[name=nid]').autocomplete('close');
					return false;
				}
			});
			$(document).on('keyup', '#employee_name', function() {
				dataTable.draw();
			});
			$(document).on('keyup', '#nid', function() {
				dataTable.draw();
			});
			$(document).on('click', '.customcheckbox input', function () {
				if ($(this).is(':checked')) {
					$(this).parent().addClass('checked');
				} else {
					$(this).parent().removeClass('checked');
				}
			});
			$(document).on('change', '.checkall', function () {
				if (this.checked) {
					$('input[name^=checksalary]').prop('checked', true);
					$('input[name^=checksalary]').parent().addClass('checked');
				} else {
					$('input[name^=checksalary]').prop('checked', false);
					$('input[name^=checksalary]').parent().removeClass('checked');
				}
			});
			$(document).on('change', '#department', function() {
				dataTable.draw();
			});
			$(document).on('change', '#workgroup', function() {
				dataTable.draw();
			});
			$(document).on('change', '#position', function() {
				dataTable.draw();
			});
			$(document).on('change', '#day', function() {
				dataTable.draw();
			});
			$(document).on('change', '#month', function() {
				dataTable.draw();
			});
			$(document).on('change', '#year', function() {
				dataTable.draw();
			});
			$(document).on('change', '#status', function() {
				dataTable.draw();
			});
		});
		
		$('input[name=birthday]').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd'
		})
		$('input[name=birthday]').on('change', function(){
			if (!$.isEmptyObject($(this).closest("form").validate())) {
				$(this).closest("form").validate().form();
			}
		});
		$(document).on('click','.delete',function(){
			var id = $(this).data('id');
			bootbox.confirm({
				buttons: {
					confirm: {
						label: '<i class="fa fa-check"></i>',
						className: 'btn-primary'
					},
					cancel: {
						label: '<i class="fa fa-undo"></i>',
						className: 'btn-default'
					},
				},
				title:'Menghapus Employee?',
				message:'Data yang telah dihapus tidak dapat dikembalikan',
				callback: function(result) {
					if(result) {
						var data = {
							_token: "{{ csrf_token() }}"
						};
						$.ajax({
							url: `{{url('admin/employees')}}/${id}`,
							dataType: 'json',
							data:data,
							type:'DELETE',
							beforeSend:function(){
								$('.overlay').removeClass('hidden');
							}
						}).done(function(response){
							if(response.status){
								$('.overlay').addClass('hidden');
								$.gritter.add({
									title: 'Success!',
									text: response.message,
									class_name: 'gritter-success',
									time: 1000,
								});
								dataTable.ajax.reload( null, false );
							}
							else{
								$.gritter.add({
									title: 'Warning!',
									text: response.message,
									class_name: 'gritter-warning',
									time: 1000,
								});
							}
						}).fail(function(response){
							var response = response.responseJSON;
							$('.overlay').addClass('hidden');
							$.gritter.add({
								title: 'Error!',
								text: response.message,
								class_name: 'gritter-error',
								time: 1000,
							});
						})
					}
				}
			});
		});
		$(document).on('click','.edit',function(){
			var id = $(this).data('id');
			bootbox.confirm({
				buttons: {
					confirm: {
						label: '<i class="fa fa-check"></i>',
						className: 'btn-primary'
					},
					cancel: {
						label: '<i class="fa fa-undo"></i>',
						className: 'btn-default'
					},
				},
				title:'Edit Employee?',
				message:'You will be redirect to employee edit page, are you sure?',
				callback: function(result) {
					if(result) {
						document.location = "{{url('admin/employees')}}/"+id+"/edit";
					}
				}
			});
		});
	})
</script>
@endpush
