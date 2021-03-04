@extends('admin.layouts.app')

@section('title', 'Salary Report')
@section('stylesheets')
<link href="{{ asset('adminlte/component/dataTables/css/datatables.min.css') }}" rel="stylesheet">
<style type="text/css">
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

  .dataTables_length {
    display: block !important;
  }
</style>
@endsection

@push('breadcrump')
<li class="breadcrumb-item active">Salary Report</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">Salary Report</h3>
        </div>
        <div class="card-body">
          <table class="table table-striped table-bordered datatable" id="salary-table" width="100%">
            <thead>
              <tr>
                <th>No</th>
                <th>Employee Name</th>
                <th>Position</th>
                <th>Department</th>
                <th>Workgroup</th>
                <th>Salary Type</th>
                <th>Period</th>
                <th>Net Salary</th>
                <th>Status</th>
                <th>Print</th>
                <th>Action</th>
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
<div class="modal fade" id="print-mass" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header no-print">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        <h4 class="modal-title">Print</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <iframe id="bodyReplace" scrolling="no" allowtransparency="true" style="width: 69%; border-width: 0px; position: relative; margin: 0 auto; display: block;" onload="this.style.height=(this.contentDocument.body.scrollHeight+45) + 'px';"></iframe>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script type="text/javascript">
  function filter() {
    $('#filter-page').modal('show');
  }
  function printmass() {
    var ids = [];
    $('input[name^=checksalary]').each(function() {
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
      url: "{{ route('salaryreport.printmass') }}",
      method: 'GET',
      data: {
        id: JSON.stringify(ids)
      },
      success: function(response) {
        $('.overlay').addClass('d-none');
        dataTable.draw();
        $('.customcheckbox').removeClass('checked');
				$('.customcheckbox input').prop('checked', false);
				var iframe = document.getElementById('bodyReplace');
        iframe = iframe.contentWindow || (iframe.contentDocument.document || iframe.contentDocument);
        iframe.document.open();
        iframe.document.write(response);
        iframe.document.close();
      }
    });
  }

  $(document).ready(function() {
    $('#filter-page').on('shown.bs.modal', function(e) {
      $('#search').on('click', function() {
        dataTable.draw();
        $('#filter-page').modal('hide');
      });
      $('#generate').on('click', function() {
        $('#form').submit();
      });
    });
  });


  $('.select2').select2();
  $(function () {
    dataTable = $('.datatable').DataTable({
      stateSave:true,
      processing:true,
      serverSide:true,
      filter:false,
      info:false,
      lengthChange:true,
      lengthMenu: [ 100, 500, 1000 ],
      // deferRender: true,
			// scrollX: true,
			// scrollCollapse: true,
      // scroller: true,
      responsive: true,
      // fixedColumns: {
      //   rightColumns: 2
      // },
      order: [[ 1, "asc" ]],
      ajax: {
        url: "{{route('salaryreport.readapproval')}}",
        type: "GET",
        data:function(data){
          var employee_id = $('input[name=employee_name]').val();
          var department_id = $('input[name=department]').val();
          var position = $('input[name=position]').val();
          var workgroup_id = $('input[name=workgroup_id]').val();
          var month = $('select[name=montly]').val();
          var year = $('select[name=year]').val();
          var status = $('select[name=status]').val();
          var type = $('select[name=type]').val();
          var nid = $('input[name=nid]').val();
          data.employee_id = employee_id;
          data.department_id = department_id;
          data.position = position;
          data.workgroup_id = workgroup_id;
          data.month = month;
          data.year = year;
          data.status = status;
          data.type = type;
          data.nid = nid;
        }
      },
      columnDefs:[
        { orderable: false,targets:[0, 9, 10] },
        { className: "text-right", targets: [7] },
        { className: "text-center", targets: [0,9,10] },
        { render: function(data, type, row) {
          return `<a class="text-blue" href="{{url('admin/salaryreport/${row.id}/detail')}}">${row.employee_name}</a><br>${row.nik}`;
        }, targets:[1]},
        { render: function(data, type, row) {
          if (row.net_salary) {
            return `Rp. ${row.net_salary}`;
          }
          return `Rp. 0`;
        }, targets:[7]},
        { render: function(data, type, row) {
          if (row.status == -1) {
            return `<span class="badge badge-secondary">Draft</span>`;
          } else if (row.status == 0) {
            return `<span class="badge badge-warning">Waiting Approval</span>`
          } else if(row.status == 1) {
            return `<span class="badge badge-success">Approved</span>`
          }else{
                `<span class="badge badge-danger">Rejected</span>`
          }
        }, targets:[8]},
        { render: function(data, type, row) {
          if (row.print_status == 1) {
            return `<span class="badge badge-success"><i class="fa fa-check"></i></span>`
          } else {
            return `<span class="badge badge-danger"><i class="fa fa-times"></i></span>`
          }
        }, targets:[9]},
       
        { render: function ( data, type, row ) {
          return `<div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                      <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item" href="{{url('admin/salaryreport/${row.id}/detail')}}"><i class="fas fa-info mr-3"></i> Detail</a></li>
                      <li><a class="dropdown-item" href="{{url('admin/salaryreport/${row.id}/editapproval')}}"><i class="fas fa-pencil-alt mr-3"></i> Edit</a></li>
                    </ul>
                  </div>`
          },targets: [10]
        }
      ],
      columns: [
        { data: "no" },
        { data: "employee_name" },
        { data: "title_name" },
        { data: "department_name" },
        { data: "workgroup_name" },
        { data: "salary_type" },
        { data: "period" },
        { data: "net_salary" },
        { data: "status" },
        { data: "print_status" },
        { data: "id" },
      ]
    });
  });
  $('#employee_name').select2({
    ajax: {
      url: "{{route('employees.select')}}",
      type: 'GET',
      dataType: 'json',
      data: function (term, page) {
          return {
            name: term,
            page: page,
            limit: 30,
          };
      },
      results: function (data, page) {
          var more = (page * 30) < data.total;
          var option = [];
          $.each(data.rows, function (index, item) {
            option.push({
            id: item.id,
            text: `${item.name}`
            });
          });
          return {
            results: option,
            more: more,
          };
      },
    },
    allowClear: true,
  });
  $('#position').select2({
    ajax: {
      url: "{{route('title.select')}}",
      type: 'GET',
      dataType: 'json',
      data: function (term, page) {
          return {
              department_id: $('#department').val() ? $('#department').val() : null,
              name: term,
              page: page,
              limit: 30,
          };
      },
      results: function (data, page) {
          var more = (page * 30) < data.total;
          var option = [];
          $.each(data.rows, function (index, item) {
              option.push({
                  id: item.id,
                  text: `${item.name}`
              });
          });
          return {
              results: option,
              more: more,
          };
      },
    },
    allowClear: true,
  });
  $('#department').select2({
    ajax: {
      url: "{{route('department.select')}}",
      type: 'GET',
      dataType: 'json',
      data: function (term, page) {
          return {
              name: term,
              page: page,
              limit: 30,
          };
      },
      results: function (data, page) {
          var more = (page * 30) < data.total;
          var option = [];
          $.each(data.rows, function (index, item) {
              option.push({
                  id: item.id,
                  text: `${item.name}`
              });
          });
          return {
              results: option,
              more: more,
          };
      },
    },
    allowClear: true,
  });
  $('#workgroup_id').select2({
    ajax: {
      url: "{{route('workgroup.select')}}",
      type: 'GET',
      dataType: 'json',
      data: function (term, page) {
          return {
              name: term,
              page: page,
              limit: 30,
          };
      },
      results: function (data, page) {
          var more = (page * 30) < data.total;
          var option = [];
          $.each(data.rows, function (index, item) {
              option.push({
                  id: item.id,
                  text: `${item.name}`
              });
          });
          return {
              results: option,
              more: more,
          };
      },
    },
    allowClear: true,
  });

  $(document).on('change', '#employee_name', function() {
    dataTable.draw();
  });
  $(document).on('keyup', '#nid', function() {
    dataTable.draw();
  });
  $(document).on('click', '.customcheckbox input', function() {
    if ($(this).is(':checked')) {
      $(this).parent().addClass('checked');
    } else {
      $(this).parent().removeClass('checked');
    }
  });
  $(document).on('change', '.checkall', function() {
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
  $(document).on('change', '#position', function() {
    dataTable.draw();
  });
  $(document).on('change', '#workgroup_id', function() {
    dataTable.draw();
  });
  $(document).on('change', '#montly', function() {
    dataTable.draw();
  });
  $(document).on('change', '#year', function() {
    dataTable.draw();
  });
  $(document).on('change', '#type', function() {
    dataTable.draw();
  });
  $(document).on('change', '#status', function() {
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
    title:'Delete salary report?',
    message:'Data that has been deleted cannot be recovered',
    callback: function(result) {
        if(result) {
          var data = {
                          _token: "{{ csrf_token() }}"
                      };
          $.ajax({
            url: `{{url('admin/salaryreport')}}/${id}`,
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