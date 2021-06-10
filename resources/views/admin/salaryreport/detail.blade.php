@extends('admin.layouts.app')

@section('title',__('salaryreport.reportsl'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('salaryreport.index')}}">{{__('salaryreport.reportsl')}}</a></li>
<li class="breadcrumb-item active">{{__('general.dtl')}}</li>
@endpush


@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="row">
      <div class="col-lg-8">
        <div class="card card-{{ config('configs.app_theme') }} card-outline">
          <div class="card-header">
            <h3 class="card-title">{{__('employee.empdata')}}</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <input type="hidden" name="report_id" id="report_id" value="{{ $salary_detail->id }}">
              <input type="hidden" name="salary_report_id[]" id="salary_report_id" value="{{ $salary_detail->id }}">
              <input type="hidden" name="employee_id" value="{{ $salary_detail->employee_id }}">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{__('employee.empname')}}</label>
                  <input type="text" class="form-control" placeholder="{{__('employee.empname')}}" value="{{ $salary_detail->employee->name }}" readonly>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>NIK Taewon</label>
                  <input type="text" class="form-control" placeholder="NIK Taewon" value="{{ @$salary_detail->employee->nid }}" readonly>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{__('employee.position')}}</label>
                  <input type="text" class="form-control" data-placeholder="{{__('employee.position')}}" value="{{ @$employee->title->name }}" readonly>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{__('department.dep')}}</label>
                  <input type="text" class="form-control" data-placeholder="{{__('department.dep')}}" value="{{ @$employee->department->name }}" readonly>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{__('employee.workcomb')}}</label>
                  <input type="text" class="form-control" data-placeholder="{{__('employee.workcomb')}}" value="{{ @$employee->workgroup->name }}" readonly>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{__('salaryreport.sltype')}}</label>
                  <input type="text" class="form-control" data-placeholder="{{__('employee.sltype')}}" value="{{ @$salary_detail->salary_type }}" readonly>
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
        <div class="card card-{{ config('configs.app_theme') }} card-outline">
          <div class="card-header">
            <h3 class="card-title">{{__('general.other')}}</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>{{__('general.period')}}</label>
                  <input type="text" class="form-control" data-placeholder="{{__('general.period')}}" value="{{ changeDateFormat('F - Y', $salary_detail->period) }}" readonly>
                </div>
              </div>
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Status</label>
                  <input type="text" name="status" data-status="{{ $salary_detail->status }}" class="form-control" data-placeholder="Status" @if ($salary_detail->status < 0) value="Draft" @elseif ($salary_detail->status == 0)
                    value="{{__('general.wait_approve')}}"
                    @else
                    value="{{__('general.approved')}}"
                    @endif readonly>
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
        <h3 class="card-title">{{__('salaryreport.gross')}}</h3>
        @if ($salary_detail->status != 1)
        <div class="pull-right card-tools">
          <a class="btn btn-{{ config('configs.app_theme') }} add_gross"><i class="fa fa-plus"></i></a>
        </div>
        @endif
      </div>
      <div class="card-body">
        <table class="table table-striped table-bordered datatable" id="gross-table" style="width: 100%">
          <thead>
            <tr>
              <th width="10">No</th>
              <th width="600">{{__('general.desc')}}</th>
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
        <h3 class="card-title">{{__('salaryreport.deduct')}}</h3>
        @if ($salary_detail->status != 1)
        <div class="pull-right card-tools">
          <a class="btn btn-{{ config('configs.app_theme') }} add_deduction"><i class="fa fa-plus"></i></a>
        </div>
        @endif
      </div>
      <div class="card-body">
        <table class="table table-striped table-bordered datatable" id="deduction-table" style="width: 100%">
          <thead>
            <tr>
              <th width="10">No</th>
              <th width="600">{{__('general.desc')}}</th>
              <th width="200">Total</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th colspan="2" class="text-right"><b>{{__('salaryreport.deduct')}}</b></th>
              <th id="deduction" data-deduction=""></th>
            </tr>
            <tr class="text-right">
              <th colspan="2"><b>{{__('salaryreport.net')}}</b></th>
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
  <div class="col-lg-12">
    @if ($salary_detail->status != 1)
    <a href="javascript:void(0)" onclick="waitingapproval()" class="btn btn-{{ config('configs.app_theme') }}"><i class="fa fa-save"></i></a>
    @endif
    <a href="javascript:void(0)" onclick="printmass()" class="btn btn-info text-white"title="{{__('general.print')}}"><i class="fa fa-print"></i></a>
    <a href="javascript:void(0)" onclick="backurl()" class="btn btn-warning text-white"><i class="fa fa-reply"></i></a>
  </div>
</div>
{{-- Modal Tambah Data --}}
<div class="modal fade" id="add_detail" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">{{__('general.add')}} {{__('salaryreport.deduct')}}</h4>
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
                <label for="description" class="control-label">{{__('general.desc')}}</label>
                <input type="text" class="form-control" id="description" name="description" placeholder="{{__('general.desc')}}" required>
              </div>
              <div class="form-group">
                <label for="total" class="control-label">Total</label>
                <input type="text" class="form-control" id="total" name="total" placeholder="Total" required>
              </div>
            </div>
          </div>
          {{ csrf_field() }}
          <input type="hidden" name="_method" />
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
<div class="modal fade" id="print-mass" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header no-print">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">{{__('general.print')}}</h4>
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
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/jquery-mask/jquery.mask.min.js')}}"></script>
<script type="text/javascript">
function printmass() {
        var ids = [];
        $('input[name^=salary_report_id]').each(function () {
          ids.push($(this).val());
        });
        // console.log(ids);
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
  $(document).ready(function(){
  $("#form_deduction").validate({
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
        title:'Add salary detail?',
        message:'Are you sure to save the changes?',
        callback: function(result) { 
          console.log(result);
          if (result) {
            $.ajax({
              url:$('#form_deduction').attr('action'),
              method:'post',
              data: new FormData($('#form_deduction')[0]),
              processData: false,
              contentType: false,
              dataType: 'json',
              beforeSend:function(){
              $('.overlay').removeClass('d-none');
              }
            }).done(function(response){
                $('.overlay').addClass('d-none');
                if(response.status){
                  $('#add_detail').modal('hide');
                  dataTable.draw();
                  dataTableDeduction.draw();
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
        }
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
function waitingapproval() {
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
    title:'Change Status Salary?',
    message:'This salary status will change to waiting approval',
    callback: function(result) {
      if(result) {
        var data = {
          _token: "{{ csrf_token() }}",
          id: "{{ $salary_detail->id }}"
        };
        $.ajax({
          url: `{{ route('salaryreport.waitingapproval') }}`,
          dataType: 'json',
          data: data,
          method:'post',
          beforeSend:function(){
            $('.overlay').removeClass('d-none');
          }
        }).done(function(response){
          if(response.status){
            document.location = response.results;
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
}
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
        if (row.description == 'Potongan absen') {
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