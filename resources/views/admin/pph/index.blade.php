@extends('admin.layouts.app')

@section('title', 'Tax Report')
@section('stylesheets')
<link href="{{ asset('adminlte/component/dataTables/css/datatables.min.css') }}" rel="stylesheet">
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
<li class="breadcrumb-item active">Tax Report</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">Tax List</h3>
          {{-- <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Generate"><i class="fa fa-sync"></i></button>
          </div> --}}
        </div>
        <div class="card-body">
          <form id="form" action="{{ route('pph.store') }}" class="form-horizontal" method="post">
              {{ csrf_field() }}
              <div class="row">
                <input type="hidden" name="user" value="{{ Auth::guard('admin')->user()->id }}">
                <div class="col-md-4">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="control-label" for="employee_name">Employee Name</label>
                      <input type="text" class="form-control" placeholder="Search" name="employee_name" id="employee_name">
                      {{-- <select name="employee_name" id="employee_name" class="form-control select2" multiple style="width: 100%" aria-hidden="true" data-placeholder="Employee Name">
                        <option value=""></option>
                        @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                        </select> --}}
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="control-label" for="nid">NIK</label>
                      <input type="text" class="form-control" placeholder="Search" name="nid" id="nid">
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="control-label" for="position">Position</label>
                      {{-- <input type="text" class="form-control" placeholder="Position" name="position" id="position"> --}}
                      <select name="position" id="position" class="form-control select2" style="width: 100%" aria-hidden="true" multiple data-placeholder="Position">
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
                      <label class="control-label" for="department">Department</label>
                      {{-- <input type="text" class="form-control" placeholder="Department" name="department" id="department"> --}}
                      <select name="department" id="department" class="form-control select2" style="width: 100%" aria-hidden="true" multiple data-placeholder="Department">
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
                      <label class="control-label" for="workgroup">Workgroup Combination</label>
                      {{-- <input type="text" class="form-control" placeholder="Workgroup Combination" name="workgroup_id" id="workgroup_id"> --}}
                      <select name="workgroup_id" id="workgroup_id" class="form-control select2" style="width: 100%" aria-hidden="true" multiple data-placeholder="Workgroup Combination">
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
                      <label class="control-label" for="period">Period</label>
                      <div class="form-row">
                        <div class="col-sm-8">
                            <select class="form-control select2" name="montly" multiple id="montly" data-placeholder="Month">
                              <option value="01" @if (date('m', time()) == "01") selected @endif>January</option>
                              <option value="02" @if (date('m', time()) == "02") selected @endif>February</option>
                              <option value="03" @if (date('m', time()) == "03") selected @endif>March</option>
                              <option value="04" @if (date('m', time()) == "04") selected @endif>April</option>
                              <option value="05" @if (date('m', time()) == "05") selected @endif>May</option>
                              <option value="06" @if (date('m', time()) == "06") selected @endif>June</option>
                              <option value="07" @if (date('m', time()) == "07") selected @endif>July</option>
                              <option value="08" @if (date('m', time()) == "08") selected @endif>August</option>
                              <option value="09" @if (date('m', time()) == "09") selected @endif>September</option>
                              <option value="10" @if (date('m', time()) == "10") selected @endif>October</option>
                              <option value="11" @if (date('m', time()) == "11") selected @endif>November</option>
                              <option value="12" @if (date('m', time()) == "12") selected @endif>December</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <select name="year" class="form-control select2" multiple id="year" data-placeholder="Year">
                                  @php
                                      $thn_skr = date('Y');
                                  @endphp
                                  @for ($i = $thn_skr; $i >= 1991; $i--)
                                  <option value="{{ $i }}" @if ($i == date('Y')) selected @endif>{{ $i }}</option>
                                  @endfor
                                </select>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div> 
          </form>
          <table class="table table-striped table-bordered datatable" style="width: 100%">
            <thead>
              <tr>
                <th width="10">No</th>
                <th width="250">Employee Name</th>
                <th width="200">NPWP</th>
                <th width="150">Metode</th>
                <th width="150">Period</th>
                <th width="200">Basic Salary</th>
                <th width="200">Tax</th>
                <th width="100">Action</th>
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
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/jquery-ui/jquery-ui.min.js')}}"></script>
<script type="text/javascript">
  // $('.select2').select2();
  $(function () {
    $(".select2").select2({
			allowClear: true
		});
    dataTable = $('.datatable').DataTable({
      stateSave:true,
      processing:true,
      serverSide:true,
      filter:false,
      info:false,
      lengthChange:true,
      responsive:true,
      order: [[ 1, "asc" ]],
			lengthMenu: [ 100, 250, 500, 1000, 2000 ],
      pageLength: 100,
      ajax: {
        url: "{{route('pph.read')}}",
        type: "GET",
        data:function(data){
          var employee_id = $('#form').find('input[name=employee_name]').val();
          var month = $('#form').find('select[name=montly]').val();
          var year = $('#form').find('select[name=year]').val();
					var department = $('select[name=department]').val();
					var position = $('select[name=position]').val();
					var workgroup = $('select[name=workgroup_id]').val();
          var nid = $('input[name=nid]').val();
          data.nid = nid;
          data.employee_id = employee_id;
          data.department = department;
					data.workgroup = workgroup;
					data.position = position;
          data.month = month;
          data.year = year;
        }
      },
      columnDefs:[
        { orderable: false,targets:[0] },
        { className: "text-right", targets: [3,4,5,6] },
        { className: "text-center", targets: [0,2,7] },
        {
          render: function ( data, type, row ) {
          return `<a href="{{url('admin/pph')}}/${row.id}/">${row.employee_name}</a>`;
          },targets: [1]
        },
        { render: function(data, type, row) {
          if (row.basic_salary) {
            return `Rp. ${row.basic_salary}`;
          }
          return `Rp. 0`;
        }, targets:[5]},
        { render: function(data, type, row) {
          if (row.tax) {
            return `Rp. ${row.tax}`;
          }
          return `Rp. 0`;
        }, targets:[6]},
        { render: function ( data, type, row ) {
          return `<div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                      <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                      <li><a class="dropdown-item" href="{{url('admin/pph/${row.id}/detail')}}"><i class="fas fa-info mr-3"></i> Detail</a></li>
                      <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> Delete</a></li>
                    </ul>
                  </div>`
          },targets: [7]
        }
      ],
      columns: [
        { data: "no" },
        { data: "employee_name" },
        { data: "npwp" },
        { data: "metode" },
        { data: "period" },
        { data: "basic_salary" },
        { data: "tax" },
        { data: "id" },
      ]
    });
  });
  // $('#employee_name').select2({
  //   ajax: {
  //     url: "{{route('employees.select')}}",
  //     type: 'GET',
  //     dataType: 'json',
  //     data: function (term, page) {
  //         return {
  //           department_id: $('#department').val() ? $('#department').val() : null,
  //           title_id: $('#position').val() ? $('#position').val() : null,
  //           name: term,
  //           page: page,
  //           limit: 30,
  //         };
  //     },
  //     results: function (data, page) {
  //         var more = (page * 30) < data.total;
  //         var option = [];
  //         $.each(data.rows, function (index, item) {
  //           option.push({
  //           id: item.id,
  //           text: `${item.name}`,
  //           department_id:`${item.department_id}`,
  //           department_name:`${item.department_name}`,
  //           title_id:`${item.title_id}`,
  //           title_name:`${item.title_name}`,
  //           });
  //         });
  //         return {
  //           results: option,
  //           more: more,
  //         };
  //     },
  //   },
  //   allowClear: true,
  // });
  // $('#position').select2({
  //   ajax: {
  //     url: "{{route('title.select')}}",
  //     type: 'GET',
  //     dataType: 'json',
  //     data: function (term, page) {
  //         return {
  //             department_id: $('#department').val() ? $('#department').val() : null,
  //             name: term,
  //             page: page,
  //             limit: 30,
  //         };
  //     },
  //     results: function (data, page) {
  //         var more = (page * 30) < data.total;
  //         var option = [];
  //         $.each(data.rows, function (index, item) {
  //             option.push({
  //                 id: item.id,
  //                 text: `${item.name}`
  //             });
  //         });
  //         return {
  //             results: option,
  //             more: more,
  //         };
  //     },
  //   },
  //   allowClear: true,
  //   multiple: true
  // })
  // $('#department').select2({
  //   ajax: {
  //     url: "{{route('department.select')}}",
  //     type: 'GET',
  //     dataType: 'json',
  //     data: function (term, page) {
  //         return {
  //             name: term,
  //             page: page,
  //             limit: 30,
  //         };
  //     },
  //     results: function (data, page) {
  //         var more = (page * 30) < data.total;
  //         var option = [];
  //         $.each(data.rows, function (index, item) {
  //             option.push({
  //                 id: item.name,
  //                 text: `${item.path}`

  //             });
  //         });
  //         return {
  //             results: option,
  //             more: more,
  //         };
  //     },
  //   },
  //   allowClear: true,
  //   multiple: true
  // });
  // $('#workgroup_id').select2({
  //   ajax: {
  //     url: "{{route('workgroup.select')}}",
  //     type: 'GET',
  //     dataType: 'json',
  //     data: function (term, page) {
  //         return {
  //             name: term,
  //             page: page,
  //             limit: 30,
  //         };
  //     },
  //     results: function (data, page) {
  //         var more = (page * 30) < data.total;
  //         var option = [];
  //         $.each(data.rows, function (index, item) {
  //             option.push({
  //                 id: item.id,
  //                 text: `${item.name}`
  //             });
  //         });
  //         return {
  //             results: option,
  //             more: more,
  //         };
  //     },
  //   },
  //   allowClear: true,
  //   multiple: true
  // });

  // $(document).on('change', '#employee_name', function() {
  //   var department_id = $('#employee_name').select2('data') ? $('#employee_name').select2('data').department_id : null;
  //   var department_name = $('#employee_name').select2('data') ? $('#employee_name').select2('data').department_name : null;
  //   var title_id = $('#employee_name').select2('data') ? $('#employee_name').select2('data').title_id : null;
  //   var title_name = $('#employee_name').select2('data') ? $('#employee_name').select2('data').title_name : null;
  //   dataTable.draw();
  //   // if (department_id) {
  //   //     $('#department').select2('data', {id: department_id, text:`${department_name}`}).trigger('change');
  //   // } else {
  //   //     $('#department').select2('data', null).trigger('change');    
  //   // }
  //   // if (title_id) {
  //   //     $('#position').select2('data', {id: title_id, text:`${title_name}`}).trigger('change');
  //   // } else {
  //   //     $('#position').select2('data', null).trigger('change');    
  //   // }
  // });
  $(document).ready(function(){
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
      $(document).on('keyup', '#nid', function () {
          dataTable.draw();
      });
    $(document).on('keyup', '#employee_name', function() {
      dataTable.draw();
    });
    });
	$(document).on('change', '#department', function() {
		dataTable.draw();
	});
	$(document).on('change', '#workgroup_id', function() {
		dataTable.draw();
	});
	$(document).on('change', '#position', function() {
		dataTable.draw();
	});
  $(document).on('change', '#montly', function() {
    dataTable.draw();
  });
  $(document).on('change', '#year', function() {
    dataTable.draw();
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
    title:'Delete PPh Report?',
    message:'Data that has been deleted cannot be recovered',
    callback: function(result) {
        if(result) {
          var data = {
                          _token: "{{ csrf_token() }}"
                      };
          $.ajax({
            url: `{{url('admin/pph')}}/${id}`,
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
              $('.overlay').addClass('hidden');
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
  $('#form').validate({
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
      else
        if (element.attr('type') == 'checkbox') {
          error.insertAfter(element.parent());
        }
        else{
          error.insertAfter(element);
        }
      },
    submitHandler: function() {
      $.ajax({
        url:$('#form').attr('action'),
        method:'post',
        data: new FormData($('#form')[0]),
        processData: false,
        contentType: false,
        dataType: 'json',
        beforeSend:function(){
            $('.overlay').removeClass('d-none');
        }
      }).done(function(response){
            $('.overlay').addClass('d-none');
            if(response.status){
              dataTable.draw();
              $.gritter.add({
                  title: 'Success!',
                  text: response.message,
                  class_name: 'gritter-success',
                  time: 1000,
              });
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
      });
    }
  });
</script>
@endpush