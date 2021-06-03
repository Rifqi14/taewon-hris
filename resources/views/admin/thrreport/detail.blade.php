@extends('admin.layouts.app')

@section('title',__('thrreport.thrrpt'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('thrreport.index')}}">{{__('thrreport.thrrpt')}}</a></li>
<li class="breadcrumb-item active">{{__('general.dtl')}}</li>
@endpush


@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="row">
      <div class="col-lg-8">
        <div class="card card-{{ config('configs.app_theme') }} card-outline">
          <div class="card-header" style="height:54px">
            <h3 class="card-title">{{__('thrreport.thrrpt')}}</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <input type="hidden" name="report_id" id="report_id" value="{{ $thrreport->id }}">
              <input type="hidden" name="employee_id" value="{{ $thrreport->employee_id }}">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{__('employee.empname')}}</label>
                  <input type="text" class="form-control" placeholder="{{__('employee.empname')}}" value="{{ $thrreport->employee->name }}" readonly>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>NIK Bosung</label>
                  <input type="text" class="form-control" placeholder="NIK Bosung" value="{{ @$thrreport->employee->nid }}" readonly>
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
                  <label>{{__('employee.jd')}}</label>
                  <input type="text" class="form-control" data-placeholder="{{__('employee.jd')}}" value="{{ @$thrreport->working_periode }}" readonly>
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
            <div class="pull-right card-tools">
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="{{__('general.prvious')}}"><i
                        class="fa fa-reply"></i></a>
                </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>{{__('general.period')}}</label>
                  <input type="text" class="form-control" data-placeholder="{{__('general.period')}}" value="{{ changeDateFormat('F', $thrreport->month) }}-{{$thrreport->year}}" readonly>
                </div>
              </div>
              <div class="col-sm-12">
                <div class="form-group">
                  <label>{{__('general.month')}}</label>
                  <input type="text" class="form-control" data-placeholder="{{__('general.month')}}" value="{{$thrreport->period}} Month" readonly>
                </div>
              </div>
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Status</label>
                  <input type="text" name="status" data-status="{{ $thrreport->status }}" class="form-control" data-placeholder="Status" @if ($thrreport->status < 0) value="Draft" @elseif ($thrreport->status == 0)
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
        <h3 class="card-title">{{__('thrreport.thrrpt')}}</h3>
        
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
  
  {{-- <div class="col-lg-12">
    @if ($salary_detail->status != 1)
    <a href="javascript:void(0)" onclick="waitingapproval()" class="btn btn-{{ config('configs.app_theme') }}"><i class="fa fa-save"></i></a>
    @endif
    <a href="{{url('admin/salaryreport/pdf', $salary_detail->id)}}" class="btn btn-primary text-white"><i class="fa fa-print"></i></a>
    <a href="javascript:void(0)" onclick="backurl()" class="btn btn-warning text-white"><i class="fa fa-reply"></i></a>
  </div> --}}
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
      url: "{{route('thrreportdetail.read')}}",
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
 
});
</script>
@endpush