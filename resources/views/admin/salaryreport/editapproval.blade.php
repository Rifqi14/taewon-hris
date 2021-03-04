@extends('admin.layouts.app')

@section('title', 'Salary Report')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('salaryreport.index')}}">Salary Report</a></li>
<li class="breadcrumb-item active">Detail Report</li>
@endpush


@section('content')
<form id="form" action="{{ route('salaryreport.updateapprove',['id'=>$salary_detail->id]) }}" class="form-horizontal no-gutters" method="post"
  enctype="multipart/form-data" autocomplete="off">
<div class="row">
     {{ csrf_field() }}
    {{ method_field('put') }}
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-{{ config('configs.app_theme') }} card-outline">
                    <div class="card-header" style="height: 55px;">
                        <h3 class="card-title">Employee Data</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                          <input type="hidden" name="report_id" id="report_id" value="{{ $salary_detail->id }}">
                          <input type="hidden" name="employee_id" value="{{ $salary_detail->employee_id }}">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Employee Name</label>
                                    <input type="text" class="form-control" placeholder="Employee Name" value="{{ $salary_detail->employee->name }}" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>NIK Bosung</label>
                                    <input type="text" class="form-control" placeholder="NIK Bosung" value="{{ $salary_detail->employee->nid }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Position</label>
                                    <input type="text" class="form-control" data-placeholder="Position" value="{{ $employee->title->name }}" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Department</label>
                                    <input type="text" class="form-control" data-placeholder="Department" value="{{ $employee->department->name }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Workgroup Combination</label>
                                    <input type="text" class="form-control" data-placeholder="Workgroup Combination" value="{{ $employee->workgroup->name }}" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Salary Type</label>
                                    <input type="text" class="form-control" data-placeholder="Salary Type" value="{{ $salary_detail->salary_type }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="overlay d-none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-{{ config('configs.app_theme') }} card-outline" style="height:95%;">
                    <div class="card-header">
                        <h3 class="card-title">Other</h3>
                        <div class="pull-right card-tools">
                        <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Simpan"><i
                            class="fa fa-save"></i></button>
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                            class="fa fa-reply"></i></a>
                    </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Period</label>
                                    <input type="text" class="form-control" data-placeholder="Period" value="{{ changeDateFormat('F - Y', $salary_detail->period) }}" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Status</label>
                                    <div class="col-sm-12">
                                        <input class="form-control" type="radio" name="status" value="1" checked> <i></i>
                                        <label class="p-2">Approve</label>
                                        <input class="form-control" type="radio" name="status" value="2"> <i></i>
                                        <label class="p-2">Reject</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="overlay d-none">
                        <i class="fa fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card card-{{ config('configs.app_theme') }} card-outline">
            <div class="card-header">
                <h3 class="card-title">Gross Salary</h3>
               
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered datatable" id="gross-table" style="width: 100%">
                    <thead>
                        <tr>
                            <th width="10">No</th>
                            <th width="600">Description</th>
                            <th width="200">Total</th>
                        </tr>
                    </thead>
                    <tfoot>
                      <tr>
                        <th colspan="2" class="text-right">Total</th>
                        <th id="gross" data-gross="1400"></th>
                      </tr>
                    </tfoot>
                </table>
            </div>
            <div class="overlay d-none">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card card-{{ config('configs.app_theme') }} card-outline">
            <div class="card-header">
                <h3 class="card-title">Deduction</h3>
                
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered datatable" id="deduction-table" style="width: 100%">
                    <thead>
                        <tr>
                            <th width="10">No</th>
                            <th width="600">Description</th>
                            <th width="200">Total</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right"><b>Total Potongan</b></th>
                            <th id="deduction" data-deduction=""></th>
                        </tr>
                        <tr class="text-right">
                            <th colspan="2"><b>Net Salary</b></th>
                            <th id="net" data-net=""></th>
                        </tr>
                    </tfoot> 
                </table>
            </div>
            <div class="overlay d-none">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>
    </div>
    
</div>
</form>
{{-- Modal Tambah Data --}}
<div class="modal fade" id="add_detail" tabindex="-1" role="dialog"  aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Deduction</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_deduction" class="form-horizontal" autocomplete="off">
                    <div class="row">
                      <input type="hidden" name="id">
                      <input type="hidden" name="employee">
                      <input type="hidden" name="type">
                      <input type="hidden" name="add_status">
                      <div class="col-md-12">
                          <div class="form-group">
                              <label for="description" class="control-label">Description</label>
                              <input type="text" class="form-control" id="description" name="description" placeholder="Description" required>
                          </div>
                          <div class="form-group">
                              <label for="total" class="control-label">Total</label>
                              <input type="text" class="form-control" id="total" name="total" placeholder="Total" required>
                          </div>
                      </div>
                    </div>
                    {{ csrf_field() }}
                  	<input type="hidden" name="_method"/> 
                </form>
            </div>
            <div class="modal-footer">
                <button form="form_deduction" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="Simpan"><i class="fa fa-save"></i></button>            
            </div> 
            <div class="overlay d-none">
              <i class="fa fa-2x fa-sync-alt fa-spin"></i>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/jquery-mask/jquery.mask.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(){
    $('input[name=status]').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
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
        var formData = new FormData($('#form')[0]);
        $.each($('#leave-list > tbody').find('tr[data-date]'), function (key, value) {
          formData.append('date[]', $(value).data('date'));
          formData.append('time_start[]', $(value).data('time_start'));
          formData.append('time_finish[]', $(value).data('time_finish'));
          formData.append('type[]', $(value).data('type'));
        });
        $.ajax({
          url:$('#form').attr('action'),
          method:'post',
          data: formData,
          processData: false,
          contentType: false,
          dataType: 'json',
          beforeSend:function(){
              $('.overlay').removeClass('d-none');
          }
        }).done(function(response){
          $('.overlay').addClass('d-none');
          if(response.status){
            document.location = response.results;
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

  $('.add_deduction').on('click',function(){
    $('#form_deduction')[0].reset();
    $('#form_deduction').attr('action',"{{ route('salaryreportdetail.store') }}");
    $('#form_deduction input[name=_method]').attr('value','POST');
    $('#form_deduction input[name=id]').val({{ $salary_detail->id }});
    $('#form_deduction input[name=employee]').val({{ $salary_detail->employee_id }});
    $('#form_deduction input[name=type]').val(0);
    $('#total').mask('000.000.000.000.000.000', {reverse: true});
    $('#form_deduction input[name=add_status]').val($('input[name=status]').val());
    $('#form_deduction .invalid-feedback').each(function () { $(this).remove(); });
    $('#form_deduction .form-group').removeClass('has-error').removeClass('has-success');
    $('#add_detail .modal-title').html('Add Deduction');
    $('#add_detail').modal('show');
  });

  $('.add_gross').on('click',function(){
    $('#form_deduction')[0].reset();
    $('#form_deduction').attr('action',"{{ route('salaryreportdetail.store') }}");
    $('#form_deduction input[name=_method]').attr('value','POST');
    $('#form_deduction input[name=id]').val({{ $salary_detail->id }});
    $('#form_deduction input[name=employee]').val({{ $salary_detail->employee_id }});
    $('#form_deduction input[name=type]').val(1);
    $('#total').mask('000.000.000.000.000.000', {reverse: true});
    $('#form_deduction input[name=add_status]').val($('input[name=status]').val());
    $('#form_deduction .invalid-feedback').each(function () { $(this).remove(); });
    $('#form_deduction .form-group').removeClass('has-error').removeClass('has-success');
    $('#add_detail .modal-title').html('Add Gross');
    $('#add_detail').modal('show');
  });
});
$(function () {
  dataTable = $('#gross-table').DataTable({
    stateSave:true,
    processing:true,
    serverSide:true,
    filter:false,
    info:false,
    lengthChange:false,
    paging: false,
    responsive:true,
    ordering: false,
    ajax: {
      url: "{{route('salaryreportdetail.read_gross')}}",
      type: "GET",
      data:function(data){
        var report_id = $('#report_id').val();
        data.report_id = report_id;
      }
    },
    columnDefs:[
      { orderable: false,targets:[0,1,2] },
      { className: "text-right", targets: [2] },
      { render: function(data, type, row) {
        var total = $.fn.dataTable.render.number( '.', ',', 0, ' Rp. ' ).display(data);
        if (row.description == 'Alpha penalty') {
          return `<span class="text-danger">( ${total} )</span>`;
        }
        return `${total}`;
      }, targets:[2]}
    ],
    columns: [
      { data: "no" },
      { data: "description" },
      { data: "total"}
    ],
    footerCallback: function(row, data, start, end, display) {
      var api = this.api(), data;

      var intVal = function ( i ) {
          return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
      };

      total = api.column( 2 ).data().reduce( function (a, b) { return intVal(a) + intVal(b);}, 0 );

      pageTotal = api.column( 2, { page: 'current'} ).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      var numFormat = $.fn.dataTable.render.number('.', ',', 0, 'Rp. ' ).display;

      $( api.column( 2 ).footer() ).html(numFormat(total));
      $('#gross').attr('data-gross', total);
    }
  });
  dataTableDeduction = $('#deduction-table').DataTable({
    stateSave:true,
    processing:true,
    serverSide:true,
    filter:false,
    info:false,
    lengthChange:false,
    paging: false,
    responsive:true,
    ordering: false,
    language: {
      emptyTable: "There are no salary deductions for this period",
    },
    ajax: {
      url: "{{route('salaryreportdetail.read_deduction')}}",
      type: "GET",
      data:function(data){
        var report_id = $('#report_id').val();
        data.report_id = report_id;
      }
    },
    columnDefs:[
      { orderable: false,targets:[0,1,2] },
      { className: "text-right", targets: [2] },
    ],
    columns: [
      { data: "no" },
      { data: "description" },
      { data: "total", render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' )}
    ],
    footerCallback: function(row, data, start, end, display) {
      var api = this.api(), data;

      var intVal = function ( i ) {
          return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
      };

      total = api.column( 2 ).data().reduce( function (a, b) { return intVal(a) + intVal(b);}, 0 );

      pageTotal = api.column( 2, { page: 'current'} ).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      var numFormat = $.fn.dataTable.render.number('.', ',', 0, 'Rp. ' ).display;

      $( api.column( 2 ).footer() ).html(numFormat(total));
      $('#deduction').attr('data-deduction', total);
      net_total = $('#gross').attr('data-gross') - $('#deduction').attr('data-deduction');
      $('#net').html(numFormat(net_total));
    }
  });
});
</script>
@endpush
