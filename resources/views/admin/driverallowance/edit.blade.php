@extends('admin.layouts.app')
@section('title',__('driverallowance.driverall'))
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{ route('driverallowance.index') }}">{{ __('driverallowance.driverall') }}</a></li>
<li class="breadcrumb-item active">{{ __('general.edt') }}</li>
@endpush

@section('content')
<form id="form" action="{{ route('driverallowance.update', ['id'=>$driver->id]) }}" class="form-horizontal" method="post" autocomplete="off">
<div class="row">
  <div class="col-lg-12">
    <div class="card card-{{ config('configs.app_theme') }} card-outline">
      <div class="card-header">
        <h3 class="card-title">{{ __('general.edt') }} {{ __('allowance.alw') }}</h3>
        <div class="pull-right card-tools">
          <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="{{ __('general.save') }}"><i
              class="fa fa-save"></i></button>
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="{{ __('general.prvious') }}"><i
              class="fa fa-reply"></i></a>
        </div>
      </div>
      <div class="card-body">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <div class="form-group row">
          <label for="driver_allowance" class="col-sm-2 col-form-label">{{ __('general.type') }}</label>
          <div class="col-sm-6">
            <select name="driver_allowance" id="driver_allowance" class="form-control select2" style="width: 100%">
              <option value="pribadi" @if ($driver->driver == 'pribadi') selected @endif>{{ __('allowance.recur') }}</option>
              <option value="truck" @if ($driver->driver == 'truck') selected @endif>{{ __('driverallowance.truck') }}</option>
            </select>
          </div>
        </div>
        <div class="form-group row">
              <label for="category" class="col-sm-2 col-form-label">{{ __('department.dep') }}</label>
              <div class="col-sm-6">
                <select name="department_id" id="department_id" class="form-control select2" style="width: 100%" aria-hidden="true">
                  @foreach ($departments as $department)
                  <option value="{{ $department->id }}" @if($department->id == $driver->department_id) selected @endif>{{ $department->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
        <div class="form-group row">
          <label for="allowance" class="col-sm-2 col-form-label">{{ __('allowance.alw') }}</label>
          <div class="col-sm-6">
            <input type="text" class="form-control" name="allowance" id="allowance" value="{{ $driver->allowance }}">
          </div>
        </div>
        <div class="form-group row">
          <label for="category" class="col-sm-2 col-form-label">{{ __('general.category') }}</label>
          <div class="col-sm-6">
            <select name="category" id="category" class="form-control select2" style="width: 100%" aria-hidden="true">
              @foreach(config('enums.allowance_category') as $key => $value)
              <option value="{{ $key }}" @if ($driver->category == $key)selected @endif>{{ $value }}</option>
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
        <h3 class="card-title">{{ __('allowance.alw') }} {{ __('general.list') }}</h3>
      </div>
      <div class="card-body">
        <div class="form-group row">
          <label for="recurrence" class="col-sm-2 col-form-label">{{ __('driverallowance.recc_day') }}</label>
          <div class="col-sm-6">
            <select name="recurrence[]" id="recurrence" class="form-control select2" style="width: 100%" multiple="multiple">
              <option value="Mon">{{ __('general.mon') }}</option>
              <option value="Tue">{{ __('general.tue') }}</option>
              <option value="Wed">{{ __('general.wed') }}</option>
              <option value="Thu">{{ __('general.thur') }}</option>
              <option value="Fri">{{ __('general.fri') }}</option>
              <option value="Sat">{{ __('general.sat') }}</option>
              <option value="Sun">{{ __('general.sun') }}</option>
            </select>
          </div>
        </div>
        <table class="table table-striped table-bordered datatable" id="recurrence_table" style="width: 100%">
          <thead>
            <tr>
              <th width="10">No</th>
              <th width="100">{{ __('general.start_time') }}</th>
              <th width="100">{{ __('general.finish_time') }}</th>
              <th width="100">{{ __('general.type') }}</th>
              <th width="100">{{ __('general.value') }}</th>
              <th width="10">{{ __('general.act') }}</th>
            </tr>
          </thead>
          <tbody>
            @if (count($list) > 0 && $driver->driverlist->first()->truck_id == null)
            @foreach ($list as $key => $item)
            {{-- @dump($item); --}}
            <tr>
              <td class="text-center align-middle">{{ $key + 1 }}</td>
              <td class="text-center align-middle">
                <div class="form-group mb-0"><input type="hidden" name="recurrence_choose[{{ $key }}]" /><input placeholder="{{ __('general.start_time') }}" name="start[{{ $key }}]" class="form-control timepicker" required value="{{ $item->start }}" /></div>
              </td>
              <td class="text-center align-middle">
                <div class="form-group mb-0"><input placeholder="{{ __('general.finish_time') }}" name="finish[{{ $key }}]" class="form-control timepicker" required value="{{ $item->finish }}" />
                </div>
              </td>
              <td class="align-middle">
                <div class="form-group mb-0">
                  <select class="form-control select2" name="type_value" id="type_value">
                    <option value="nominal" @if ($item->type_value == 'nominal') selected @endif>{{ __('general.nom') }}</option>
                    <option value="percentage" @if ($item->type_value == 'percentage') selected @endif>{{ __('general.percent') }}</option>
                  </select>
                </div>
              </td>
              <td class="text-center align-middle">
                <div class="input-group mb-0">
                  {{-- <div class="input-group-prepend"> --}}
                    {{-- <span class="input-group-text" id="currency_symbol">Rp.</span> --}}
                    {{-- <select class="input-group-text" style="appearance:none; -webkit-appearance:none; -moz-appearance:none;" name="type_value" id="currency_symbol2">
                      <option value="nominal" @if ($item->type_value == 'nominal') selected @endif>Rp.</option>
                      <option value="percentage" @if ($item->type_value == 'percentage') selected @endif>%</option>
                    </select> --}}
                  {{-- </div> --}}
                  <input placeholder="Value" name="value[{{ $key }}]" class="form-control currency" aria-label="{{ __('general.value') }}" aria-describedby="currency_symbol" required value="{{ $item->value }}">
                </div>
              </td>
              <td class="text-center align-middle"><a href="javascript:void(0)" onclick="addRecurrence()" class="fa fa-plus fa-lg d-inline"></a> / <a href="javascript:void(0)" class="fa fa-trash fa-lg d-inline remove"></a></td>
            </tr>
            @endforeach
            @else
            <tr>
              <td class="text-center align-middle">1</td>
              <td class="text-center align-middle">
                <div class="form-group mb-0"><input type="hidden" name="recurrence_choose[0]" /><input placeholder="{{ __('general.start_time') }}"
                    name="start[0]" class="form-control new-timepicker" required /></div>
              </td>
              <td class="text-center align-middle">
                <div class="form-group mb-0"><input placeholder="{{ __('general.finish_time') }}" name="finish[0]" class="form-control new-timepicker" required />
                </div>
              </td>
              <td class="text-center align-middle">
                <div class="input-group mb-0">
                  <div class="input-group-prepend">
                    {{-- <span class="input-group-text" id="currency_symbol">Rp.</span> --}}
                    <select class="input-group-text" style="appearance:none; -webkit-appearance:none; -moz-appearance:none;" name="type_value" id="currency_symbol2">
                      <option value="nominal">Rp.</option>
                      <option value="percentage">%</option>
                    </select>
                  </div>
                  <input placeholder="{{ __('general.value') }}" name="value[0]" class="form-control currency" aria-label="{{ __('general.value') }}" aria-describedby="currency_symbol" required>
                </div>
              </td>
              <td class="text-center align-middle"><a href="javascript:void(0)" onclick="addRecurrence()" class="fa fa-plus fa-lg d-inline"></a> / <a href="javascript:void(0)" class="fa fa-trash fa-lg d-inline remove"></a></td>
            </tr>
            @endif
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
        <h3 class="card-title">{{ __('allowance.alw') }} {{ __('general.list') }}</h3>
      </div>
      <div class="card-body">
        <div class="form-group row">
          <label for="type" class="col-sm-2 col-form-label">{{ __('general.type') }}</label>
          <div class="col-sm-6">
          <select name="truck_id" id="truck_id" class="form-control select2" style="width: 100%">
                @foreach ($trucks as $truck)
                  <option value="{{ $truck->id }}" @if (count($driver->driverlist) > 0 && $driver->driverlist->first()->truck_id == $truck->id) selected @endif>{{ $truck->name }}</option>
                  @endforeach
              </select>
          </div>
        </div>
        <table class="table table-striped table-bordered datatable" id="type_table" style="width: 100%; height: 100%">
          <thead>
            <tr>
              <th width="10">No</th>
              <th width="100">{{ __('driverallowance.rule') }}</th>
              <th width="100">{{ __('general.type') }}</th>
              <th width="200">{{ __('general.value') }}</th>
              <th width="10">{{ __('general.act') }}</th>
            </tr>
          </thead>
          <tbody>
            @if ($driver->driverlist->first()->truck_id != null)
            @foreach ($driver->driverlist as $key => $item)
            <tr>
              <td class="text-center align-middle">{{ $key + 1 }}</td>
              <td class="text-center align-middle">
                <div class="form-group mb-0"><input type="hidden" name="type_choose[]" /><input placeholder="{{ __('driverallowance.rule') }}" name="rit[]" class="form-control" value="{{ $item->rit }}" required /></div>
              </td>
              <td class="align-middle">
                <div class="form-group mb-0">
                  <select class="form-control select2" name="type_value" id="type_value">
                    <option value="nominal" @if ($item->type_value == 'nominal') selected @endif>{{ __('general.nom') }}</option>
                    <option value="percentage" @if ($item->type_value == 'percentage') selected @endif>{{ __('general.percent') }}</option>
                  </select>
                </div>
              </td>
              <td class="text-center align-middle">
                <div class="input-group mb-0">
                  {{-- <div class="input-group-prepend"> --}}
                    {{-- <span class="input-group-text" id="currency_symbol2">Rp.</span> --}}
                    {{-- <select class="input-group-text" style="appearance:none; -webkit-appearance:none; -moz-appearance:none;" name="type_value" id="currency_symbol2">
                      <option value="nominal" @if ($item->type_value == 'nominal') selected @endif>Rp.</option>
                      <option value="percentage" @if ($item->type_value == 'percentage') selected @endif>%</option>
                    </select>
                  </div> --}}
                  <input placeholder="Value" name="rit_value[]" class="form-control currency" aria-label="Value" value="{{ $item->value }}" aria-describedby="currency_symbol2" required>
                </div>
              </td>
              <td class="text-center align-middle"><a href="javascript:void(0)" onclick="addType()" class="fa fa-plus fa-lg d-inline"></a> / <a href="javascript:void(0)" class="fa fa-trash fa-lg d-inline remove"></a></td>
            </tr>
            @endforeach
            @else
            <tr>
              <td class="text-center align-middle">1</td>
              <td class="text-center align-middle">
                <div class="form-group mb-0"><input type="hidden" name="type_choose[]" /><input placeholder="{{ __('driverallowance.rule') }}"
                    name="rit[]" class="form-control" required /></div>
              </td>
              <td class="text-center align-middle">
                <div class="input-group mb-0">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="currency_symbol2">Rp.</span>
                  </div>
                  <input placeholder="Value" name="rit_value[]" class="form-control currency" aria-label="Value" aria-describedby="currency_symbol2" required>
                </div>
              </td>
              <td class="text-center align-middle"><a href="javascript:void(0)" onclick="addType()" class="fa fa-plus fa-lg d-inline"></a> / <a href="javascript:void(0)" class="fa fa-trash fa-lg d-inline remove"></a></td>
            </tr>
            @endif
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
<script src="{{asset('accounting/accounting.min.js')}}"></script>
<script>
  $(document).ready(function(){
    $('.select2').select2();
    @if ($day)
      $("#recurrence").select2('data', {!! json_encode($day) !!}).trigger('change');
    @endif
    $('#driver_allowance').val('{!! $driver->driver !!}').trigger('change');
    // $('body .currency').each(function() {
    //   $(this).val(accounting.formatMoney($(this).val(),'',',','.'));
    // });
    // $('body .currency').keyup(function() {
    //   $(this).val(accounting.formatMoney($(this).val(),'',',','.'));
    // })
  });
  $('#driver_allowance').change(function() {
    var value = $(this).val();
    switch (value) {
      case 'pribadi':
        $('#pribadi').removeClass('d-none');
        $('#truck').addClass('d-none');
        $('.new-timepicker').daterangepicker({
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
        $('#department_id').closest('.form-group').hide();
        break;
      case 'truck':
        $('#truck').removeClass('d-none');
        $('#pribadi').addClass('d-none');
        $('#department_id').closest('.form-group').show();
        break;
    
      default:
        $('#truck').addClass('d-none');
        $('#pribadi').addClass('d-none');
        $('#department_id').closest('.form-group').hide();
        break;
    }
  });
  function addType() {
    var length = $('#type_table tr').length;
    var html = '<tr>';
        html += '<td class="text-center align-middle">'+length+'</td>';
        html += '<td class="text-center align-middle"><div class="form-group mb-0"><input type="hidden" name="type_choose[]"/><input placeholder="{{ __('driverallowance.rule') }}" name="rit[]" class="form-control" required/></div></td>';
        // html += '<td class="text-center align-middle"><div class="input-group mb-0"><div class="input-group-prepend"><span class="input-group-text" id="currency_symbol2">Rp.</span></div><input placeholder="Value" name="rit_value[]" class="form-control currency" aria-label="Value" aria-describedby="currency_symbol2" required></div></td>';
        html += '<td class="align-middle"><div class="form-group mb-0"><select name="type_value" class="form-control select2" id="type_value"><option value="nominal">{{ __('general.nom') }}</option><option value="percentage">{{ __('general.percent') }}</option></select></div></td>';
        html += '<td class="text-center align-middle"><div class="input-group mb-0"><input placeholder="Nilai" name="rit_value[]" class="form-control" aria-label="{{ __('general.value') }}" aria-describedby="currency_symbol2" required></div></td>';
        html += '<td class="text-center align-middle"><a href="javascript:void(0)" class="fa fa-plus fa-lg d-inline" onclick="addType()"></a> / <a href="javascript:void(0)" class="fa fa-trash fa-lg d-inline remove"></a></td>';
        html += '</tr>'
    $('#type_table').append(html);
  }
  function addRecurrence() {
    var length = $('#recurrence_table tr').length - 1;
    var number = $('#recurrence_table tr').length;
    var html = '<tr>';
        html += '<td class="text-center align-middle">'+number+'</td>';
        html += '<td class="text-center align-middle"><div class="form-group mb-0"><input type="hidden" name="recurrence_choose['+length+']"/><input placeholder="{{ __('general.start_time') }}" name="start['+length+']" class="form-control timepicker" required/></div></td>';
        html += '<td class="text-center align-middle"><div class="form-group mb-0"><input placeholder="{{ __('general.finish_time') }}" name="finish['+length+']" class="form-control timepicker" required/></div></td>';
        // html += '<td class="text-center align-middle"><div class="input-group mb-0"><div class="input-group-prepend"><span class="input-group-text" id="currency_symbol">Rp.</span></div><input placeholder="Value" name="value['+length+']" class="form-control currency" aria-label="Value" aria-describedby="currency_symbol" required></div></td>';
        html += '<td class="align-middle"><div class="form-group mb-0"><select name="type_value" class="form-control select2" id="type_value"><option value="nominal">{{ __('general.nom') }}</option><option value="percentage">{{ __('general.percent') }}</option></select></div></td>';
        html += '<td class="text-center align-middle"><div class="input-group mb-0"><input placeholder="Nilai" name="value[]" class="form-control" aria-label="{{ __('general.value') }}" aria-describedby="currency_symbol" required></div></td>';
        html += '<td class="text-center align-middle"><a href="javascript:void(0)" onclick="addRecurrence()" class="fa fa-plus fa-lg d-inline"></a> / <a href="#" class="fa fa-trash fa-lg d-inline remove"></a></td>';
        html += '</tr>';
    $('#recurrence_table').append(html);
    $('input[name="start['+length+']"]').daterangepicker({
      startDate: moment(),
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
    $('input[name="finish['+length+']"]').daterangepicker({
      startDate: moment(),
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
  @if (count($list) > 0)
  @foreach($list as $key => $item)
  $('input[name="start[{{ $key }}]"]').daterangepicker({
    startDate: moment('{!! $item->start !!}', "HH:mm"),
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
  $('input[name="finish[{{ $key }}]"]').daterangepicker({
    startDate: moment('{!! $item->finish !!}', "HH:mm"),
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
  @endforeach
  @endif
  $('#type_table').on('click','.remove',function(){
    $(this).parents('tr').remove();
  });
  $('#recurrence_table').on('click','.remove',function(){
    $(this).parents('tr').remove();
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
              if (response.status == 'refresh') {
                $.gritter.add({
                    title: 'Error!',
                    text: response.message,
                    class_name: 'gritter-error',
                    time: 1000,
                });
                setTimeout(function() {
                    location.reload();
                }, 2000);
              }
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
            if (response.status == 'refresh') {
              $.gritter.add({
                  title: 'Error!',
                  text: response.message,
                  class_name: 'gritter-error',
                  time: 1000,
              });
              setTimeout(function() {
                  location.reload();
              }, 2000);
            } else {
              $.gritter.add({
                  title: 'Error!',
                  text: response.message,
                  class_name: 'gritter-error',
                  time: 1000,
              });
            }
        })
      }
  });
</script>
@endpush