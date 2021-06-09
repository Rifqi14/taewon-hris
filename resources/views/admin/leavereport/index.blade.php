@extends('admin.layouts.app')

@section('title', __('leavereport.leaverpt'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
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
</style>
@endsection

@push('breadcrump')
<li class="breadcrumb-item active">{{ __('leavereport.leaverpt') }}</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title">{{ __('leavereport.listleav') }}</div>
          <div class="pull-right card-tools"><a href="javascript:void(0)" onclick="exportleave()" class="btn btn-primary btn-sm text-white" data-toggle="tooltip" title="{{ __('general.exp') }}" style="cursor: pointer;"><i class="fa fa-download"></i></a></div>
        </div>
        <div class="card-body">
          <form id="form" class="form-horizontal" method="post">
            {{ csrf_field() }}
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="nik">NIK</label>
                <input type="text" class="form-control" id="nik" placeholder="NIK" name="nik">
              </div>
              <div class="form-group col-md-4">
                <label for="employee_id">{{ __('employee.empname') }}</label>
                <input type="text" class="form-control" id="employee_id" placeholder="{{ __('employee.empname') }}" name="employee_id">
              </div>
              <div id="employee-container"></div>
              <div class="form-row col-md-4">
                <div class="form-group col-md-6">
                  <label for="from">{{ __('general.from') }}</label>
                  <input type="text" class="form-control datepicker" id="from" placeholder="{{ __('general.from') }}" name="from">
                </div>
                <div class="form-group col-md-6">
                  <label for="to">{{ __('general.To') }}</label>
                  <input type="text" class="form-control datepicker" id="to" placeholder="{{ __('general.To') }}" name="to">
                </div>
              </div>
            </div>
          </form>
          <table class="table table-striped table-bordered datatable" style="width: 100%">
            <thead>
              <tr>
                <th width="10">#</th>
                <th width="10">{{ __('general.date') }}</th>
                <th width="10">{{ __('employee.employ') }}</th>
                <th width="10">{{ __('position.pos') }}</th>
                <th width="10">{{ __('leave.leavetp') }}</th>
                <th width="10">{{ __('employee.duration') }}</th>
                <th width="10">{{ __('leavereport.remain') }}</th>
                <th width="10">Status</th>
                <th width="10">{{ __('general.act') }}</th>
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
<div id="waiting-dialog"class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" style="overflow-y:visible;">
      <div class="modal-dialog modal-m">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 style="margin:0;">Loading</h5>
              </div>
              <div class="modal-body">
                  <div class="progress">
                      <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                  </div>
              </div>
          </div>
      </div>
  </div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootbox/bootbox.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/jquery-ui/jquery-ui.min.js')}}"></script>
<script type="text/javascript">
  function exportleave() {
		$.ajax({
			url: "{{ route('leavereport.export') }}",
			type: 'POST',
			dataType: 'JSON',
			data: $("#form").serialize(),
			beforeSend:function(){
				// $('.overlay').removeClass('d-none');
				$('#waiting-dialog').modal('show');
			}
		}).done(function(response){
			$('#waiting-dialog').modal('hide');
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
			$('#waiting-dialog').modal('hide');
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
  $(function () {
    dataTable = $('.datatable').DataTable({
      stateSave:true,
      processing:true,
      serverSide:true,
      filter:false,
      info:false,
      lengthChange:true,
      responsive:true,
      order: [[ 2, "asc" ]],
      lengthMenu: [ 100, 250, 500, 1000, 2000 ],
      language: {
        url: language_choosen == 'id' ? urlLocaleId : '',
      },
      pageLength: 500,
      ajax: {
        url: "{{route('leavereport.read')}}",
        type: "GET",
        data:function(data){
          var employee_name = $('input[name=employee_id]').val();
          var nik = $('input[name=nik]').val();
          var from = $('input[name=from]').val();
          var to = $('input[name=to]').val();
          data.name = employee_name;
          data.nik = nik;
          data.from = from;
          data.to = to;
        }
      },
      columnDefs:[
        { orderable: false,targets:[0] },
        { className: "text-right", targets: [0] },
        { className: "text-center", targets: [4,5,6,7,8] },
        { render: function(data, type, row) {
            return row.start_date ? `${row.start_date} s/d ${row.finish_date}` : '';
          }, targets:[1]},
        { render: function(data, type, row) {
            return `${row.employee_name}<br>${row.employee_id}`;
          }, targets:[2]},
        { render: function(data, type, row) {
            return `${row.title_name}<br>${row.department_name}`;
          }, targets:[3]},
        { render: function(data, type, row) {
            return `${row.duration} days`;
          }, targets:[5]},
        { render: function(data, type, row) {
            if (row.status == 1) {
              return `<span class="badge badge-success">Approved</span>`;
            }else if(row.status == 2){
              return `<span class="badge badge-danger">Rejected</span>`;
            }else{
              return `<span class="badge badge-warning">Waiting Approval</span>`;
            }
          }, targets:[7]},
        { render: function ( data, type, row ) {
          return `<div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                      <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                      <li><a class="dropdown-item" href="{{url('admin/leavereport')}}/${row.id}/detail"><i class="fas fa-search mr-2"></i> Detail</a></li>
                      <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> {{ __('general.dlt') }}</a></li>
                    </ul>
                  </div>`
          },targets: [8]
        }
      ],
      columns: [
        { data: "no" },
        { data: "start_date" },
        { data: "employee_name" },
        { data: "title_name" },
        { data: "leave_type" },
        { data: "duration" },
        { data:"remaining"},
        { data: "status" },
        { data: "id" },
      ]
    });
    $(document).on('click','.delete',function(){
      var id = $(this).data('id');
      bootbox.confirm({
        buttons: {
          confirm: {
            label: '<i class="fa fa-check"></i>',
            className: `btn-{{ config('configs.app_theme') }}`
          },
          cancel: {
            label: '<i class="fa fa-undo"></i>',
            className: 'btn-default'
          },
        },
        title:'Delete Leave Report?',
        message:'Data that has been deleted cannot be recovered',
        callback: function(result) {
          if(result) {
            var data = {
              _token: "{{ csrf_token() }}"
            };
            $.ajax({
              url: `{{url('admin/leavereport')}}/${id}`,
              dataType: 'json',
              data:data,
              type:'DELETE',
              beforeSend:function(){
                $('.overlay').removeClass('d-none');
              }
            }).done(function(response){
              if(response.status){
                $('.overlay').addClass('d-none');
                $.gritter.add({
                  title: 'Success!',
                  text: response.message,
                  class_name: 'gritter-success',
                  time: 1000,
                });
                dataTable.ajax.reload( null, false );
              } else {
                $.gritter.add({
                  title: 'Warning!',
                  text: response.message,
                  class_name: 'gritter-warning',
                  time: 1000,
                });
              }
            }).fail(function(response){
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
        }
      });
    });
  });
  $(document).ready(function() {
    var employees = [
				@foreach($employees as $employee)
          "{!!$employee->name!!}",
        @endforeach
			];
			$( "input[name=employee_id]" ).autocomplete({
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
			$("input[name=employee_id]").keydown(function(event){
				if(event.keyCode == 13) {
					event.preventDefault();
					$('input[name=employee_id]').autocomplete('close');
					return false;
				}
		});
    var employees = [
				@foreach($employees as $nik)
                	"{!!$nik->nid!!}",
            	@endforeach
			];
			$( "input[name=nik]" ).autocomplete({
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
			$("input[name=nik]").keydown(function(event){
				if(event.keyCode == 13) {
					event.preventDefault();
					$('input[name=nik]').autocomplete('close');
					return false;
				}
		});
    $(document).on('keyup', '#employee_id', function() {
      dataTable.draw();
    });
    $(document).on('keyup', '#nik', function() {
      dataTable.draw();
    });
    $(document).on('change keyup keydown keypress focus', '#from #to', function() {
      dataTable.draw();
    });
    $('#from').daterangepicker({
      autoUpdateInput: false,
      singleDatePicker: true,
      timePicker: false,
      timePickerIncrement: 30,
      locale: {
      format: 'MM/DD/YYYY'
      }
    }, function(chosen_date) {
      $('#from').val(chosen_date.format('MM/DD/YYYY'));
      dataTable.draw();
    });
    $('#to').daterangepicker({
      autoUpdateInput: false,
      singleDatePicker: true,
      timePicker: false,
      timePickerIncrement: 30,
      locale: {
      format: 'MM/DD/YYYY'
      }
    }, function(chosen_date) {
      $('#to').val(chosen_date.format('MM/DD/YYYY'));
      dataTable.draw();
    });
  });
</script>
@endpush