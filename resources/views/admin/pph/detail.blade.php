@extends('admin.layouts.app')

@section('title', 'Tax Calculation')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('pph.index')}}">Tax Report</a></li>
<li class="breadcrumb-item active">Tax Calculation</li>
@endpush


@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="row">
      <div class="col-lg-8">
        <div class="card card-{{ config('configs.app_theme') }} card-outline">
          <div class="card-header">
            <h3 class="card-title">Employee Data</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <input type="hidden" name="report_id" id="report_id" value="{{ $pph_detail->id }}">
              <input type="hidden" name="employee_id" value="{{ $pph_detail->employee_id }}">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Employee Name</label>
                  <input type="text" class="form-control" placeholder="Employee Name" value="{{ $pph_detail->employee->name }}" readonly>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>NPWP</label>
                  <input type="text" class="form-control" placeholder="NPWP" value="{{ $pph_detail->employee->npwp }}" readonly>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Metode</label>
                  <input type="text" class="form-control" data-placeholder="Metode" value="{{ $pph_detail->employee->tax_calculation }}" readonly>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>PTKP</label>
                  <input type="text" class="form-control" data-placeholder="Department" value="{{ $pph_detail->employee->ptkp }}" readonly>
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
            <h3 class="card-title">Other</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Period</label>
                  <input type="text" class="form-control" data-placeholder="Period" value="{{ changeDateFormat('F - Y', $pph_detail->period) }}" readonly>
                </div>
              </div>
              <div style="height: 165px;"></div>
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
        <h3 class="card-title">Tax Calculation Detail</h3>
      </div>
      <div class="card-body">
        <table class="table table-striped table-bordered datatable" id="gross-table" style="width: 100%">
          <thead>
            <tr>
              <th width="10">No</th>
              <th width="600">Description</th>
              <th width="200" class="text-right">Total</th>
            </tr>
          </thead>
          <Tbody>
            @if ($pph_detail->salarydetail)
            @foreach ($pph_detail->salarydetail as $key => $value)
            @if ($value->description == 'Basic Salary')
            <tr>
              <td>1</td>
              <td>{{ $value->description }}</td>
              <td class="text-right">{{ 'Rp. ' . number_format($value->total,0,",",".") }}</td>
            </tr>
            @endif
            @if (strpos($value->description, 'Prestasi') !== false)
            <tr>
              <td>2</td>
              <td>{{ $value->description }}</td>
              <td class="text-right">{{ 'Rp. ' . number_format($value->total,0,",",".") }}</td>
            </tr>
            @endif
            @if (strpos($value->description, 'Jabatan') !== false)
            <tr>
              <td>3</td>
              <td>{{ 'Gross Salary' }}</td>
              <td class="text-right">{{ 'Rp. ' . number_format($pph_detail->gross_salary,0,",",".") }}</td>
            </tr>
            @endif
            @if (strpos($value->description, 'Jabatan') !== false)
            <tr>
              <td>4</td>
              <td>&emsp;&emsp;&emsp;{{ $value->description }}</td>
              <td class="text-right">{{ 'Rp. ' . number_format($value->total,0,",",".") }}</td>
            </tr>
            @endif
            @if (strpos($value->description, 'Net Salary') !== false)
            <tr>
              <td>5</td>
              <td>Net Salary (Monthly)</td>
              <td class="text-right">{{ 'Rp. ' . number_format($value->total / $multipleMonth,0,",",".") }}</td>
            </tr>
            @endif
            @if (strpos($value->description, 'Net Salary') !== false)
            <tr>
              <td>6</td>
              <td>{{ $value->description }}</td>
              <td class="text-right">{{ 'Rp. ' . number_format($value->total,0,",",".") }}</td>
            </tr>
            @endif
            @if (strpos($value->description, 'Net Salary') !== false)
            <tr>
              <td>7</td>
              <td>PTKP</td>
              <td class="text-right">{{ 'Rp. ' . number_format($ptkp->value,0,",",".") }}</td>
            </tr>
            @endif
            @if (strpos($value->description, 'Net Salary') !== false)
            <tr>
              <td>8</td>
              <td>PKP</td>
              <td class="text-right">{{ 'Rp. ' . number_format($value->total - $ptkp->value,0,",",".") }}</td>
            </tr>
            @endif
            @if (strpos($value->description, 'PPh 21 (Yearly)') !== false)
            <tr>
              <td>9</td>
              <td>{{ $value->description }}</td>
              <td class="text-right"></td>
            </tr>
            @endif
            @if (strpos($value->description, 'PPh 21 (Yearly)') !== false)
            <tr>
              <td></td>
              <td>&emsp;&emsp;&emsp;{{ $value->description }}</td>
              <td class="text-right">{{ 'Rp. ' . number_format($value->total,0,",",".") }}</td>
            </tr>
            @endif
            @if (strpos($value->description, 'Potongan PPh 21') !== false)
            <tr>
              <td>10</td>
              <td>PPh 21 (Monthly)</td>
              <td class="text-right"></td>
            </tr>
            @endif
            @if (strpos($value->description, 'Potongan PPh 21') !== false)
            <tr>
              <td></td>
              <td>&emsp;&emsp;&emsp;PPh 21 (Monthly)</td>
              <td class="text-right">{{ 'Rp. ' . number_format($value->total,0,",",".") }}</td>
            </tr>
            @endif
            @endforeach
            @endif
          </Tbody>
        </table>
      </div>
      <div class="overlay d-none">
        <i class="fa fa-refresh fa-spin"></i>
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
  });
});
$(function () {
  // dataTable = $('#gross-table').DataTable({
  //   stateSave:true,
  //   processing:true,
  //   serverSide:true,
  //   filter:false,
  //   info:false,
  //   lengthChange:false,
  //   paging: false,
  //   responsive:true,
  //   ordering: false,
  //   ajax: {
  //     url: "{{route('pphreportdetail.read_gross')}}",
  //     type: "GET",
  //     data:function(data){
  //       var report_id = $('#report_id').val();
  //       data.report_id = report_id;
  //     }
  //   },
  //   columnDefs:[
  //     { orderable: false,targets:[0,1,2] },
  //     { className: "text-right", targets: [2] },
  //     { render: function(data, type, row) {
  //       console.log(row);
  //       if(row.total == -1){
  //         return "";
  //       }
  //       if (row.total) {
  //         return `Rp. ${row.total}`;
  //       }
  //       return `Rp. 0`;
  //     }, targets:[2]},
  //     { render: function(data, type, row) {
  //       console.log(row);
  //       var description = row.description;
  //       if (row.type == 0) {
  //           description = "&emsp;&emsp;"+row.description;
  //       }
  //       return description;
  //     }, targets:[1]},
  //   ],
  //   columns: [
  //     { data: "no" },
  //     { data: "description" },
  //     { data: "total" }
  //   ]
  // });
  // dataTableDeduction = $('#deduction-table').DataTable({
  //   stateSave:true,
  //   processing:true,
  //   serverSide:true,
  //   filter:false,
  //   info:false,
  //   lengthChange:false,
  //   paging: false,
  //   responsive:true,
  //   ordering: false,
  //   language: {
  //     emptyTable: "There are no salary deductions for this period",
  //   },
  //   ajax: {
  //     url: "{{route('pphreportdetail.read_deduction')}}",
  //     type: "GET",
  //     data:function(data){
  //       var report_id = $('#report_id').val();
  //       data.report_id = report_id;
  //     }
  //   },
  //   columnDefs:[
  //     { orderable: false,targets:[0,1,2] },
  //     { className: "text-right", targets: [2] },
  //     { render: function(data, type, row) {
  //       if (row.total) {
  //         return `Rp. ${row.total}`;
  //       }
  //       return `Rp. 0`;
  //     }, targets:[2]},
  //   ],
  //   columns: [
  //     { data: "no" },
  //     { data: "description" },
  //     { data: "total" }
  //   ]
  // });
});
</script>
@endpush