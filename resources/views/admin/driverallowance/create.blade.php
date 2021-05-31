@extends('admin.layouts.app')
@section('title', 'Create Driver Allowance')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{ route('driverallowance.index') }}">Master Driver Allowance</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush

@section('content')
<form id="form" action="{{ route('driverallowance.store') }}" class="form-horizontal" method="post" autocomplete="off">
  <div class="row">
    <div class="col-lg-12">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">Add Allowance</h3>
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Simpan"><i
                class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                class="fa fa-reply"></i></a>
          </div>
        </div>
        <div class="card-body">
            {{ csrf_field() }}
            <div class="form-group row">
              <label for="driver_allowance" class="col-sm-2 col-form-label">Type</label>
              <div class="col-sm-6">
                <select name="driver_allowance" id="driver_allowance" class="form-control select2" style="width: 100%" required>
                  <option value="pribadi">Reccurance</option>
                  <option value="truck">Truck</option>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label for="allowance" class="col-sm-2 col-form-label">Allowance</label>
              <div class="col-sm-6">
                <input type="text" class="form-control" name="allowance" id="allowance" required>
              </div>
            </div>
            <div class="form-group row">
              <label for="category" class="col-sm-2 col-form-label">Category</label>
              <div class="col-sm-6">
                <select name="category" id="category" class="form-control select2" style="width: 100%" aria-hidden="true">
                  @foreach (config('enums.allowance_category') as $key=>$value)
                  <option value="{{ $key }}">{{ $value }}</option>
                  @endforeach
                </select>
              </div>
            </div>
        </div>
        <div class="overlay d-none">
          <i class="fa fa-2x fa-sync-alt fa-spin"></i>
        </div>
      </div>
    </div>
    <div class="col-lg-12 d-none" id="pribadi">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">Allowance List</h3>
        </div>
        <div class="card-body">
          <div class="form-group row">
            <label for="recurrence" class="col-sm-2 col-form-label">Recurrence Day</label>
            <div class="col-sm-6">
              <select name="recurrence[]" id="recurrence" class="form-control select2" style="width: 100%" multiple="multiple">
                <option value="Mon">Monday</option>
                <option value="Tue">Tuesday</option>
                <option value="Wed">Wednesday</option>
                <option value="Thu">Thursday</option>
                <option value="Fri">Friday</option>
                <option value="Sat">Saturday</option>
                <option value="Sun">Sunday</option>
              </select>
            </div>
          </div>
          <table class="table table-striped table-bordered datatable" id="recurrence_table" style="width: 100%">
            <thead>
              <tr>
                <th width="10">No</th>
                <th width="100">Start Time</th>
                <th width="100">Finish Time</th>
                <th width="100">Type</th>
                <th width="100">Value</th>
                <th width="10">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-center align-middle">1</td>
                <td class="text-center align-middle">
                  <div class="form-group mb-0"><input type="hidden" name="recurrence_choose[]" /><input placeholder="Start Time"
                      name="start[]" class="form-control timepicker" required /></div>
                </td>
                <td class="text-center align-middle">
                  <div class="form-group mb-0"><input placeholder="Finish Time" name="finish[]" class="form-control timepicker" required />
                  </div>
                </td>
                <td class="align-middle">
                  <div class="form-group mb-0">
                    <select name="type_value" class="form-control select2" id="type_value">
                      <option value="nominal">Nominal</option>
                      <option value="percentage">Percentage</option>
                    </select>
                  </div>
                </td>
                <td class="text-center align-middle">
                  <div class="input-group mb-0">
                    {{-- <div class="input-group-prepend"> --}}
                      {{-- <span class="input-group-text" id="currency_symbol">Rp.</span> --}}
                      {{-- <select class="input-group-text" style="appearance:none; -webkit-appearance:none; -moz-appearance:none;" name="type_value" id="currency_symbol">
                        <option value="nominal">Rp.</option>
                        <option value="percentage">%</option>
                      </select>
                    </div> --}}
                    <input placeholder="Nilai" name="value[]" class="form-control rupiah" aria-label="Value" aria-describedby="currency_symbol" required>
                  </div>
                </td>
                <td class="text-center align-middle"><a href="javascript:void(0)"
                    onclick="addRecurrence()" class="fa fa-plus fa-lg d-inline"></a></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="overlay d-none">
          <i class="fa fa-2x fa-sync-alt fa-spin"></i>
        </div>
      </div>
    </div>
    <div class="col-lg-12 d-none" id="truck">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">Allowance List</h3>
        </div>
        <div class="card-body">
          <div class="form-group row">
            <label for="type" class="col-sm-2 col-form-label">Type</label>
            <div class="col-sm-6">
              <select name="type" id="type" class="form-control select2" style="width: 100%">
                <option value="fuso">Fuso</option>
                <option value="colt_diesel">Colt Diesel</option>
              </select>
            </div>
          </div>
          <table class="table table-striped table-bordered datatable" id="type_table" style="width: 100%; height: 100%">
            <thead>
              <tr>
                <th width="10">No</th>
                <th width="100">Rule</th>
                <th width="100">Type</th>
                <th width="200">Value</th>
                <th width="10">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-center align-middle">1</td>
                <td class="text-center align-middle">
                  <div class="form-group mb-0"><input type="hidden" name="type_choose[]" /><input placeholder="Rule"
                      name="rit[]" class="form-control" required /></div>
                </td>
                <td class="align-middle">
                  <div class="form-group mb-0">
                    <select name="type_value" class="form-control select2" id="type_value">
                      <option value="nominal">Nominal</option>
                      <option value="percentage">Percentage</option>
                    </select>
                  </div>
                </td>
                <td class="text-center align-middle">
                  <div class="input-group mb-0">
                    {{-- <div class="input-group-prepend">
                      <span class="input-group-text" id="currency_symbol2">Rp.</span>
                      <select class="input-group-text" style="appearance:none; -webkit-appearance:none; -moz-appearance:none;" name="type_value" id="currency_symbol2">
                        <option value="nominal">Rp.</option>
                        <option value="percentage">%</option>
                      </select>
                    </div> --}}
                    <input placeholder="Nilai" name="rit_value[]" class="form-control rupiah" aria-label="Value" aria-describedby="currency_symbol2" required>
                  </div>
                </td>
                <td class="text-center align-middle"><a href="javascript:void(0)"
                    onclick="addType()" class="fa fa-plus fa-lg d-inline"></a></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="overlay d-none">
        <i class="fa fa-2x fa-sync-alt fa-spin"></i>
      </div>
    </div>
  </div>
</form>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/jquery-mask/jquery.mask.min.js')}}"></script>
<script>
  $('#driver_allowance').change(function() {
    var value = $(this).val();
    switch (value) {
      case 'pribadi':
        $('#pribadi').removeClass('d-none');
        $('#truck').addClass('d-none');
        break;
      case 'truck':
        $('#truck').removeClass('d-none');
        $('#pribadi').addClass('d-none');
        break;
    
      default:
        $('#truck').addClass('d-none');
        $('#pribadi').addClass('d-none');
        break;
    }
  });
  $('#driver_allowance').val(this).trigger('change');
  function addType() {
    var length = $('#type_table tr').length;
    var html = '<tr>';
        html += '<td class="text-center align-middle">'+length+'</td>';
        html += '<td class="text-center align-middle"><div class="form-group mb-0"><input type="hidden" name="type_choose[]"/><input placeholder="Rule" name="rit[]" class="form-control" required/></div></td>';
        // html += '<td class="text-center align-middle"><div class="input-group mb-0"><div class="input-group-prepend"><select class="input-group-text" style="appearance:none; -webkit-appearance:none; -moz-appearance:none;" name="type_value" id="currency_symbol2"><option value="nominal">Rp.</option><option value="percentage">%</option></select></div><input placeholder="Value" name="rit_value[]" class="form-control" aria-label="Value" aria-describedby="currency_symbol2" required></div></td>';
        html += '<td class="align-middle"><div class="form-group mb-0"><select name="type_value" class="form-control select2" id="type_value"><option value="nominal">Nominal</option><option value="percentage">Percentage</option></select></div></td>';
        html += '<td class="text-center align-middle"><div class="input-group mb-0"><input placeholder="Nilai" name="rit_value[]" class="form-control" aria-label="Value" aria-describedby="currency_symbol2" required></div></td>';
        html += '<td class="text-center align-middle"><a href="javascript:void(0)" class="fa fa-plus fa-lg d-inline" onclick="addType()"></a> / <a href="javascript:void(0)" class="fa fa-trash fa-lg d-inline remove"></a></td>';
        html += '</tr>'
    $('#type_table').append(html);
  }
  function addRecurrence() {
    var length = $('#recurrence_table tr').length;
    var html = '<tr>';
        html += '<td class="text-center align-middle">'+length+'</td>';
        html += '<td class="text-center align-middle"><div class="form-group mb-0"><input type="hidden" name="recurrence_choose[]"/><input placeholder="Start Time" name="start[]" class="form-control timepicker" required/></div></td>';
        html += '<td class="text-center align-middle"><div class="form-group mb-0"><input placeholder="Finish Time" name="finish[]" class="form-control timepicker" required/></div></td>';
        // html += '<td class="text-center align-middle"><div class="input-group mb-0"><div class="input-group-prepend"><select class="input-group-text" style="appearance:none; -webkit-appearance:none; -moz-appearance:none;" name="type_value" id="currency_symbol"><option value="nominal">Rp.</option><option value="percentage">%</option></select></div><input placeholder="Value" name="value[]" class="form-control" aria-label="Value" aria-describedby="currency_symbol" required></div></td>';
        html += '<td class="align-middle"><div class="form-group mb-0"><select name="type_value" class="form-control select2" id="type_value"><option value="nominal">Nominal</option><option value="percentage">Percentage</option></select></div></td>';
        html += '<td class="text-center align-middle"><div class="input-group mb-0"><input placeholder="Nilai" name="rit_value[]" class="form-control" aria-label="Value" aria-describedby="currency_symbol2" required></div></td>';
        html += '<td class="text-center align-middle"><a href="javascript:void(0)" onclick="addRecurrence()" class="fa fa-plus fa-lg d-inline"></a> / <a href="#" class="fa fa-trash fa-lg d-inline remove"></a></td>';
        html += '</tr>';
    $('#recurrence_table').append(html);
    $('.timepicker').daterangepicker({
      singleDatePicker: true,
      timePicker: true,
      timePicker24Hour: true,
      timePickerIncrement: 1,
      timePickerSeconds: false,
      locale: {
        format: 'HH:mm'
      }
    }).on('show.daterangepicker', function(ev, picker) {
      picker.container.find('.calendar-table').hide();
    });
  }
  $(document).ready(function(){
    $('.select2').select2();
    $('.rupiah').mask('000.000.000.000.000.000', {reverse: true});
  });
  $('#type_table').on('click','.remove',function(){
    $(this).parents('tr').remove();
  });
  $('#recurrence_table').on('click','.remove',function(){
    $(this).parents('tr').remove();
  });
  $('.timepicker').daterangepicker({
    singleDatePicker: true,
    timePicker: true,
    timePicker24Hour: true,
    timePickerIncrement: 1,
    timePickerSeconds: false,
    locale: {
      format: 'HH:mm'
    }
  }).on('show.daterangepicker', function(ev, picker) {
    picker.container.find('.calendar-table').hide();
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
            $('.overlay').removeClass('hidden');
          }
        }).done(function(response){
            $('.overlay').addClass('hidden');
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
          $('.overlay').addClass('hidden');
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