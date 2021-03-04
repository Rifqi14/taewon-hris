@extends('admin.layouts.app')

@section('title', 'Leave Approval')
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
<li class="breadcrumb-item active">Leave Approval</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title">List Leave Approval</div>
          
        </div>
        <div class="card-body">
          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="nik">NIK</label>
              <input type="text" class="form-control" id="nik" placeholder="NIK" name="nik">
            </div>
            <div class="form-group col-md-4">
              <label for="employee_id">Searching For</label>
              <input type="text" class="form-control" id="employee_id" placeholder="Searching For" name="employee_id">
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
          <table class="table table-striped table-bordered datatable" style="width: 100%">
            <thead>
              <tr>
                <th width="10">#</th>
                <th width="10">Ref No</th>
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
          <i class="fa fa-refresh fa-spin"></i>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="add-filter" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog"
  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Filter</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>
      <div class="modal-body">
        <form id="form-search">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="name">Employee Name</label>
                <input type="text" name="employee_name" id="employee_name" class="form-control" placeholder="Searching For">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="position">Position</label>
                <input type="text" name="position" class="form-control" placeholder="Position">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="status">Status</label>
                <select name="status" id="status" class="form-control select2" style="width: 100%" aria-hidden="true">
                  <option value="1">Active</option>
                  <option value="0">Not Active</option>
                </select>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button form="form-search" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="Apply"><i
            class="fa fa-search"></i></button>
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
  function filter(){
		$('#add-filter').modal('show');
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
      order: [[ 7, "asc" ]],
      ajax: {
        url: "{{route('leaveapproval.readapproval')}}",
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
            return `${row.start_date} s/d ${row.finish_date}`;
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
            } else {
              return `<span class="badge badge-warning">Waiting Approval</span>`;
            }
          }, targets:[6]},
        { render: function ( data, type, row ) {
          return `<div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                      <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                      <li><a class="dropdown-item edit" href="#" data-id="${row.id}"><i class="fas fa-pencil-alt mr-2"></i> Edit</a></li>
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
    $('#form-search').submit(function(e){
      e.preventDefault();
      dataTable.draw();
      $('#add-filter').modal('hide');
    });
    $(document).on('click','.edit',function(){
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
        title:'Edit Leave Approval?',
        message:'You will be redirect to leave approval edit page, are you sure?',
        callback: function(result) {
          if(result) {
            document.location = "{{url('admin/leaveapproval')}}/"+id+"/editapproval";
          }
        }
      });
    });
  });
  $(document).ready(function() {
    $('#leave_type').select2({
      ajax: {
        url: "{{route('leavebalance.select')}}",
        type:'GET',
        dataType: 'json',
        data: function (term,page) {
          return {
            name:term,
            page:page,
            limit:30,
          };
        },
        results: function (data,page) {
          var more = (page * 30) < data.total;
          var option = [];
          $.each(data.rows,function(index,item){
            option.push({
              id:item.id,
              text: `${item.leave_type}`,
              leave_id: `${item.id}`,
              leave_name: `${item.leave_tag}`,
              balance: `${item.balance}`
            });
          });
          return {
            results: option, more: more,
          };
        },
      }
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