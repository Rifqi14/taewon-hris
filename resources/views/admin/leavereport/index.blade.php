@extends('admin.layouts.app')

@section('title', 'Leave Report')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet">
<style type="text/css">
  .ui-state-active {
    background: #28a745 !important;
    border-color: #28a745 !important;
  }

  .ui-menu {
    overflow: auto;
    height: 200px;
  }
</style>
@endsection

@push('breadcrump')
<li class="breadcrumb-item active">Leave Report</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title">List Leave Report</div>
          <div class="pull-right card-tools"><a href="javascript:void(0)" onclick="exportleave()" class="btn btn-primary btn-sm text-white" data-toggle="tooltip" title="Export" style="cursor: pointer;"><i class="fa fa-download"></i></a></div>
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
                <label for="employee_id">Employee Name</label>
                <input type="text" class="form-control" id="employee_id" placeholder="Employee Name" name="employee_id">
              </div>
              <div id="employee-container"></div>
              <div class="form-row col-md-4">
                <div class="form-group col-md-6">
                  <label for="from">From</label>
                  <input type="text" class="form-control datepicker" id="from" placeholder="From" name="from">
                </div>
                <div class="form-group col-md-6">
                  <label for="to">To</label>
                  <input type="text" class="form-control datepicker" id="to" placeholder="To" name="to">
                </div>
              </div>
            </div>
          </form>
          <table class="table table-striped table-bordered datatable" style="width: 100%">
            <thead>
              <tr>
                <th width="10">#</th>
                <th width="10">Tanggal</th>
                <th width="10">Employee</th>
                <th width="10">Position</th>
                <th width="10">Leave Type</th>
                <th width="10">Duration</th>
                <th width="10">Status</th>
                <th width="10">Action</th>
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
            waitingDialog.show('Loading...');
        }
    }).done(function(response){
      if(response.status){
        // $('.overlay').addClass('d-none');
        $.gritter.add({
          title: 'Success!',
          text: response.message,
          class_name: 'gritter-success',
          time: 1000,
        });
          waitingDialog.hide();
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
        // $('.overlay').addClass('d-none');
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
        { className: "text-center", targets: [4,5,6,7] },
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
          }, targets:[6]},
        { render: function ( data, type, row ) {
          return `<div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                      <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                      <li><a class="dropdown-item" href="{{url('admin/leavereport')}}/${row.id}/detail"><i class="fas fa-search mr-2"></i> Detail</a></li>
                    </ul>
                  </div>`
          },targets: [7]
        }
      ],
      columns: [
        { data: "no" },
        { data: "start_date" },
        { data: "employee_name" },
        { data: "title_name" },
        { data: "leave_type" },
        { data: "duration" },
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
        title:'Delete Leave Application?',
        message:'Data that has been deleted cannot be recovered',
        callback: function(result) {
          if(result) {
            var data = {
              _token: "{{ csrf_token() }}"
            };
            $.ajax({
              url: `{{url('admin/leave')}}/${id}`,
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