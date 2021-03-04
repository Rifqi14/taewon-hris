@extends('admin.layouts.app')

@section('title', 'Edit Calendar')

@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/fullcalendar/lib/main.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}" rel="stylesheet">
<style>
  .colorpicker {
    z-index: 9999 !important;
  }
  .fc-daygrid-day {
    cursor: pointer;
    transition: .5s;
  }
  .fc-daygrid-day:hover {
    cursor: pointer;
    background: rgba(199, 234, 70, 0.2) !important;
    transition: .5s;
  }

</style>
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('calendar.index')}}">Calendar</a></li>
<li class="breadcrumb-item active">Edit</li>
@endpush

@section('content')
{{-- Form edit calendar --}}
<div class="row">
  <div class="col-lg-12">
    <div class="card card-{{ config('configs.app_theme') }} card-outline">
      <div class="card-header">
        <h3 class="card-title">Create Calendar</h3>
        <div class="pull-right card-tools">
          <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Simpan"><i
              class="fa fa-save"></i></button>
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
              class="fa fa-reply"></i></a>
        </div>
      </div>
      <div class="card-body">
        <form id="form" action="{{ route('calendar.update',['id'=>$calendar->id]) }}" class="form-horizontal"
          method="post" autocomplete="off">
          {{ csrf_field() }}
          {{ method_field('put') }}
          <div class="form-group row">
            <label class="col-md-2 col-xs-12 control-label" for="code">Calendar Code <span
                class="text-red">*</span></label>
            <div class="col-sm-6 controls">
              <input type="text" class="form-control" id="code" name="code" placeholder="Calendar Code"
                value="{{ $calendar->code }}" required>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-2 col-xs-12 control-label" for="calendar_name">Calendar Name <span
                class="text-red">*</span></label>
            <div class="col-sm-6 controls">
              <input type="text" class="form-control" id="calendar_name" name="calendar_name"
                placeholder="Calendar Name" value="{{ $calendar->name }}" required>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-xs-12 col-form-label" for="label_color">Label Color</label>
            <div class="col-sm-6">
              <div class="input-group my-colorpicker2">
                <input type="text" class="form-control" value="{{ $calendar->label_color }}" name="label_color"
                  id="label_color">
                <div class="input-group-append">
                  <span class="input-group-text input-group-addon">
                    <i></i>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-2 col-xs-12 control-label" for="calendar_desc">Calendar Description</label>
            <div class="col-sm-6 controls">
              <textarea class="form-control" id="calendar_desc" name="calendar_desc"
                placeholder="Calendar Description">{{ $calendar->description }}</textarea>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-2 col-xs-12 control-label" for="is_default">Active</label>
            <div class="col-sm-6 controls">
              <input type="checkbox" class="custom-control-input i-checks" name="is_default" @if ($calendar->is_default)
              checked @endif id="is_default">
            </div>
          </div>
        </form>
      </div>
      <div class="overlay d-none">
        <i class="fa fa-2x fa-sync-alt fa-spin"></i>
      </div>
    </div>
  </div>
</div>
{{-- End of Form edit calendar --}}

{{-- Table of List Exception --}}
<div class="row">
  <div class="col-lg-12">
    <div class="card card-{{ config('configs.app_theme') }} card-outline">
      <ul class="nav nav-tabs">
        <li class="nav-item"><a href="#exception" class="nav-link active" data-toggle="tab">List Exception</a></li>
        <li class="nav-item"><a href="#calendar" class="nav-link" data-toggle="tab">Calendar</a></li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" id="exception">
          <div class="card-header">
            <div class="pull-right card-tools">
              <a href="#" class="btn btn-{{ config('configs.app_theme') }} btn-sm text-white add_exception"
                data-toggle="tooltip" title="Tambah">
                <i class="fa fa-plus"></i>
              </a>
            </div>
            <h3 class="card-title">List Exception</h3>
          </div>
          <div class="card-body">
            {{-- DataTable List Exception --}}
            <table class="table table-striped table-bordered datatable" id="table-exception" style="width:100%">
              <thead>
                <tr>
                  <th width="5">#</th>
                  <th width="50">Date</th>
                  <th width="200">Description</th>
                  <th width="5">#</th>
                </tr>
              </thead>
            </table>
            {{-- DataTable List Exception --}}
          </div>
        </div>
        <div class="tab-pane" id="calendar">
          <div class="card-header">
            <h3 class="card-title">Calendar</h3>
          </div>
          <div class="card-body">
            <div id="calendar_exception"></div>
          </div>
        </div>
      </div>
      {{-- Overlay loading --}}
      <div class="overlay d-none">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
      {{-- End of Overlay loading --}}
    </div>
  </div>
</div>
{{-- End of Table List Exception --}}

{{-- Modal Add Exception --}}
<div class="modal fade" id="add_exception" tabindex="-1" role="dialog" aria-hidden="true" role="dialog"
  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="overlay-wrapper">
        {{-- Header & Tool --}}
        <div class="modal-header">
          <h4 class="modal-title">Add Exception</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        {{-- End of Header & Tool --}}
        <div class="modal-body">
          {{-- Form Add Exception --}}
          <form id="form_exception" class="form-horizontal" method="post" autocomplete="off">
            <input type="hidden" name="calendar_id">
            {{-- Description --}}
            <div class="form-group row">
              <label for="description" class="col-sm-3 col-form-label">Description</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="description" name="description" placeholder="Description"
                  required>
              </div>
            </div>
            {{-- .Description --}}
            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="label_exception">Label Color</label>
              <div class="col-sm-9">
                <div class="input-group my-colorpicker2">
                  <input type="text" class="form-control" value="#000" name="label_exception" id="label_exception">
                  <div class="input-group-append">
                    <span class="input-group-text input-group-addon">
                      <i></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-3 col-form-label" for="text_color">Text Color</label>
              <div class="col-sm-9">
                <div class="input-group my-colorpicker2">
                  <input type="text" class="form-control" value="#fff" name="text_color" id="text_color">
                  <div class="input-group-append">
                    <span class="input-group-text input-group-addon">
                      <i></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <hr>
            {{-- Reccurence Radio --}}
            <div class="form-check">
              <input class="form-check-input" type="radio" name="reccurence_day" id="reccurence_day"
                value="reccurence_day" onclick="reccurence_pattern()">
              <label class="form-check-label" for="reccurence_day">
                <b>Reccurence Day</b>
              </label>
            </div>
            {{-- .Reccurence Radio --}}
            {{-- Day Checkbox --}}
            <div class="row py-3">
              <div class="col-sm-6">
                <div class="form-check">
                  <input class="form-check-input check-day" type="checkbox" value="monday" id="monday" name="day[]">
                  <label class="form-check-label" for="day">
                    <b>Monday</b>
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input check-day" type="checkbox" value="tuesday" id="tuesday" name="day[]">
                  <label class="form-check-label" for="day">
                    <b>Tuesday</b>
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input check-day" type="checkbox" value="wednesday" id="wednesday"
                    name="day[]">
                  <label class="form-check-label" for="day">
                    <b>Wednesday</b>
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input check-day" type="checkbox" value="thursday" id="thursday" name="day[]">
                  <label class="form-check-label" for="day">
                    <b>Thursday</b>
                  </label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-check">
                  <input class="form-check-input check-day" type="checkbox" value="friday" id="friday" name="day[]">
                  <label class="form-check-label" for="day">
                    <b>Friday</b>
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input check-day" type="checkbox" value="saturday" id="saturday" name="day[]">
                  <label class="form-check-label" for="day">
                    <b>Saturday</b>
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input check-day" type="checkbox" value="sunday" id="sunday" name="day[]">
                  <label class="form-check-label" for="day">
                    <b>Sunday</b>
                  </label>
                </div>
              </div>
            </div>
            {{-- .Day Checkbox --}}
            <div class="form-group mb-0">
              <label class="control-label" for="recurrence_range">Range of Recurrence</label>
            </div>
            {{-- Start and Finish Date Reccurence --}}
            <div class="form-group row">
              <div class="col-sm-5 mr-5">
                <label class="control-label" for="start_range">Start</label>
                <div class="controls col-xs-10">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                      </span>
                    </div>
                    <input type="text" name="start_range" id="start_range" class="form-control datepicker"
                      placeholder="Start Date">
                  </div>
                </div>
              </div>
              <div class="col-sm-5">
                <label class="control-label" for="finish_range">Finish</label>
                <div class="controls col-xs-10">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                      </span>
                    </div>
                    <input type="text" name="finish_range" id="finish_range" class="form-control datepicker"
                      placeholder="Finish Date">
                  </div>
                </div>
              </div>
            </div>
            {{-- .Start and Finish Date Reccurence --}}
            <hr>
            {{-- Specific Day Radio --}}
            <div class="form-check">
              <input class="form-check-input" type="radio" name="reccurence_day" id="specific_day" value="specific_day"
                onclick="reccurence_pattern()">
              <label class="form-check-label" for="reccurence_day">
                <b>Specific Day</b>
              </label>
            </div>
            {{-- .Specific Day Radio --}}
            {{-- Specific Date --}}
            <div class="form-group row py-3">
              <label class="col-md-2 control-label" for="specific_date">Date</label>
              <div class="col-sm-5 controls">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="far fa-calendar-alt"></i>
                    </span>
                  </div>
                  <input type="text" name="specific_date" class="form-control datepicker" id="specific_date"
                    placeholder="Date">
                </div>
              </div>
            </div>
            {{-- .Specific Date --}}
            {{-- Start and Finish Date Specific Day --}}
            <div class="form-group mb-0">
              <label class="control-label" for="recurrence_range">Range of Recurrence</label>
            </div>
            <div class="form-group row">
              <div class="col-sm-5 mr-5">
                <label class="control-label" for="start_range">Start</label>
                <div class="controls col-xs-10">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                      </span>
                    </div>
                    <input type="text" name="start_specific" id="start_specific" class="form-control datepicker"
                      placeholder="Start Date">
                  </div>
                </div>
              </div>
              <div class="col-sm-5">
                <label class="control-label" for="finish_range">Finish</label>
                <div class="controls col-xs-10">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                      </span>
                    </div>
                    <input type="text" name="finish_specific" id="finish_specific" class="form-control datepicker"
                      placeholder="Finish Date">
                  </div>
                </div>
              </div>
            </div>
            {{-- .Start and Finish Date Specific Day --}}
            {{ csrf_field() }}
            <input type="hidden" name="_method" />
          </form>
          {{-- End of Form Add Exception --}}
        </div>
        <div class="modal-footer">
          <button form="form_exception" type="submit"
            class="btn btn-sm btn-{{ config('configs.app_theme') }} text-white" title="Simpan"><i
              class="fa fa-save"></i></button>
        </div>
        <div class="overlay d-none">
          <i class="fa fa-2x fa-sync-alt fa-spin"></i>
        </div>
      </div>
    </div>
  </div>
</div>
{{-- End of Modal Add Exception --}}
{{-- Modal Edit Exception --}}
<div class="modal fade modal-allow-overflow" id="edit-exception" tabindex="-1" role="dialog" aria-hidden="true"
  data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Exception</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body mb-1">
        <form id="form-edit" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="_method" />
          <div class="row">
            <div class="form-group row col-12">
              <label class="col-sm-3 control-label" for="exception_date">Date</label>
              <div class="col-sm-5 controls">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="far fa-calendar-alt"></i>
                    </span>
                  </div>
                  <input type="text" name="exception_date" id="exception_date" class="form-control datepicker2"
                    placeholder="Date" required />
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="form-group row col-12">
              <label class="col-sm-3 control-label" for="exception_desc">Description</label>
              <div class="col-sm-9 controls">
                <input type="text" class="form-control" name="exception_desc" id="exception_desc"
                  placeholder="Description" required>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="exception_label">Label Color</label>
            <div class="col-sm-9">
              <div class="input-group my-colorpicker2">
                <input type="text" class="form-control" name="exception_label" id="exception_label">
                <div class="input-group-append">
                  <span class="input-group-text input-group-addon">
                    <i id="label_span"></i>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="exception_text">Text Color</label>
            <div class="col-sm-9">
              <div class="input-group my-colorpicker2">
                <input type="text" class="form-control" name="exception_text" id="exception_text">
                <div class="input-group-append">
                  <span class="input-group-text input-group-addon">
                    <i id="text_span"></i>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <input type="hidden" name="calendar_id">
          <input type="hidden" name="exception_id">
        </form>
      </div>
      <div class="modal-footer">
        <button form="form-edit" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }} text-white"
          title="Simpan"><i class="fa fa-save"></i></button>
      </div>
    </div>
  </div>
</div>
{{-- End of Modal Edit Exception --}}
{{-- Modal Add Calendar --}}
<div class="modal fade modal-allow-overflow" id="add-calendar" tabindex="-1" role="dialog" aria-hidden="true"
  data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Calendar</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body mb-1">
        <form id="form-calendar" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          <div class="form-group row">
            <label class="col-sm-3 control-label" for="calendar_date">Date</label>
            <div class="col-sm-5 controls">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <i class="far fa-calendar-alt"></i>
                  </span>
                </div>
                <input type="text" name="calendar_date" id="calendar_date" class="form-control" placeholder="Date"
                  readonly required />
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-3 control-label" for="calendar_desc_add">Description</label>
            <div class="col-sm-9 controls">
              <input type="text" class="form-control" name="calendar_desc_add" id="calendar_desc_add"
                placeholder="Description" required>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="calendar_label">Label Color</label>
            <div class="col-sm-9">
              <div class="input-group my-colorpicker2">
                <input type="text" class="form-control" name="calendar_label" id="calendar_label">
                <div class="input-group-append">
                  <span class="input-group-text input-group-addon">
                    <i id="label_span"></i>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="calendar_text">Text Color</label>
            <div class="col-sm-9">
              <div class="input-group my-colorpicker2">
                <input type="text" class="form-control" name="calendar_text" id="calendar_text">
                <div class="input-group-append">
                  <span class="input-group-text input-group-addon">
                    <i id="text_span"></i>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <input type="hidden" name="id_calendar">
        </form>
      </div>
      <div class="modal-footer">
        <button form="form-calendar" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }} text-white"
          title="Add"><i class="fa fa-save"></i></button>
      </div>
    </div>
  </div>
</div>
{{-- End of Modal Edit Exception --}}
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/fullcalendar/lib/main.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
<script type="text/javascript">
  function reccurence_pattern() {
    var val = $('input[name="reccurence_day"]:checked').val();
    if(val == 'reccurence_day')
    {
      $("input.check-day").removeAttr("disabled");
      $('#start_range').attr("required", true);
      $('#finish_range').attr("required", true);
      $('#specific_date').removeAttr("required");
      $('#start_specific').removeAttr("required");
      $('#finish_specific').removeAttr("required");
    }
    if(val == 'specific_day')
    {
      $("input.check-day").prop("checked", false);
      $("input.check-day").attr("disabled", true);
      $('#start_range').removeAttr("required");
      $('#finish_range').removeAttr("required");
      $('#specific_date').attr("required", true);
      $('#start_specific').attr("required", true);
      $('#finish_specific').attr("required", true);
    }
  }

  function changeDateFormat(date) {
    var piece = date.split('-');
    var newdate = piece[1]+'/'+piece[2]+'/'+piece[0];

    return new Date(newdate);
  }

  function cancel_edit() {
    $('#edit-exception').modal('hide');
    $('#form-edit').find('input[name=exception_id]').val();
    $('#form-edit').find('#exception_desc').val('');
    $('#form-edit').find('#exception_date').daterangepicker({
      startDate:moment(), endDate:moment(), singleDatePicker:true, locale:{format:'DD/MM/YYYY'}
    });
  }

  $(function() {
    $('.datepicker').daterangepicker({
        singleDatePicker: true,
        timePicker: false,
        timePickerIncrement: 30,
        locale: {
        format: 'DD/MM/YYYY'
        }
    });
    // Datepirkcer for Search
    $('.datepicker2').daterangepicker({
        singleDatePicker: true,
        timePicker: false,
        timePickerIncrement: 30,
        locale: {
        format: 'DD/MM/YYYY'
        }
    });
  });
  $(document).ready(function() {
    $('.i-checks').iCheck({
      checkboxClass: 'icheckbox_square-green',
      radioClass: 'iradio_square-green',
    });
    var calendarEl = document.getElementById('calendar_exception');
    var id = {!! $calendar->id !!};
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      headerToolbar: {
        start: 'prev,today',
        center: 'title',
        end: 'today,next'
      },
      events: `{{url('admin/calendarexc')}}/${id}/calendar`,
      firstDay: 1,
      dateClick: function (info) {
        $('#add-calendar .modal-title').html('Add Calendar');
        $('#add-calendar').modal('show');
        $('#form-calendar')[0].reset();
        $('#form-calendar .invalid-feedback').each(function () { $(this).remove(); });
        $('#form-calendar .form-group').removeClass('has-error').removeClass('has-success');
        $('#form-calendar input[name=id_calendar]').attr('value',{{ $calendar->id }});
        $('#form-calendar input[name=calendar_date]').attr('value', info.dateStr);
        $('#form-calendar input[name=calendar_desc_add]').attr('placeholder', 'Description');
        $('#form-calendar input[name=calendar_label]').attr('value', '#000');
        $('#form-calendar input[name=calendar_text]').attr('value', '#fff');
        $('#form-calendar #label_span').css('background-color', '#000');
        $('#form-calendar #text_span').css('background-color', '#fff');
        $('#form-calendar').attr('action',"{{ route('calendarexc.addcalendar') }}");
      }
    });
    calendar.render();
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
      var currentTab = $(e.target).text();
      switch (currentTab) {
        case 'List Exception':
          $('#table-exception').css("width", "100%")
          $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
          break;
        case 'Calendar':
        calendar.refetchEvents();
        calendar.render();
          break;
      
        default:
          break;
      }
    })
    $('.my-colorpicker2').colorpicker();
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
						document.location = response.results;
					} else {
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
    $('#form_exception').validate({
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
          url:$('#form_exception').attr('action'),
          method:'post',
          data: new FormData($('#form_exception')[0]),
          processData: false,
          contentType: false,
          dataType: 'json',
          beforeSend:function(){
              $('.overlay').removeClass('d-none');
          }
        }).done(function(response){
              $('.overlay').addClass('d-none');
              if(response.status){
                dataTableExc.draw();
                calendar.refetchEvents();
                calendar.render();
                $('#add_exception').modal('hide');
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
        })
      }
    });
    $('#form-calendar').validate({
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
          url:$('#form-calendar').attr('action'),
          method:'post',
          data: new FormData($('#form-calendar')[0]),
          processData: false,
          contentType: false,
          dataType: 'json',
          beforeSend:function(){
              $('.overlay').removeClass('d-none');
          }
        }).done(function(response){
              $('.overlay').addClass('d-none');
              if(response.status){
                $('#add-calendar').modal('hide');
                dataTableExc.draw();
                calendar.refetchEvents();
                calendar.render();
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
        })
      }
    });
    dataTableExc = $('#table-exception').DataTable({
        stateSave:true,
        processing: true,
        serverSide: true,
        filter:false,
        info:false,
        lengthChange:true,
        responsive: true,
        order: [[ 1, "asc" ]],
        ajax: {
            url: "{{route('calendarexception.read')}}",
            type: "GET",
            data:function(data){
              var qty_absent = $('#form-search').find('input[name=qty_absent]').val();
              data.qty_absent = qty_absent;
              data.calendar_id = {{$calendar->id}};
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [0] },
            { className: "text-center", targets: [3] },
            { render: function ( data, type, row ) {
                return `<div class="dropdown">
                    <button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item editexception" href="#" data-id="${row.id}"><i class="fa fa-pencil-alt mr-2"></i> Edit</a></li>
                        <li><a class="dropdown-item deleteexception" href="#" data-id="${row.id}"><i class="fa fa-trash mr-2"></i> Delete</a></li>
                    </ul>
                    </div>`
            },targets: [3]
            }
        ],
        columns: [
            { data: "no" },
            { data: "date_exception" },
            { data: "description" },
            { data: "id" },
        ]
    });
    $('.add_exception').on('click', function() {
      $('#form_exception')[0].reset();
      $("input.check-day").attr("disabled", true);
      $('#form_exception').attr('action',"{{ route('calendarexc.store') }}");
      $('#form_exception input[name=_method]').attr('value', 'POST');
      $('#form_exception input[name=calendar_id]').attr('value', {{ $calendar->id }});
      $('#form_exception input[name=description]').attr('value', '');
      $('#add_exception .modal-title').html('Add Exception');
      $('#add_exception').modal('show');
      $('#form_exception').find('.datepicker').daterangepicker({
        startDate: moment(),
        endDate: moment(),
        singleDatePicker: true,
        timePicker: false,
        timePickerIncrement: 30,
        locale: {
          format: 'DD/MM/YYYY'
        }
      });
      $('.datepicker').on('showCalendar.daterangepicker', function (ev, picker) {
        if (picker.element.offset().top - $(window).scrollTop() + picker.container.outerHeight() > $(window).height()) {
          picker.drops = 'up';
        } else {
          picker.drops = 'down';
        }
        picker.move();
      });
    });
    $('#form-edit').validate({
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
          url:$('#form-edit').attr('action'),
          method:'post',
          data: new FormData($('#form-edit')[0]),
          processData: false,
          contentType: false,
          dataType: 'json',
          beforeSend:function(){
              $('.overlay').removeClass('d-none');
          }
        }).done(function(response){
              $('.overlay').addClass('d-none');
              if(response.status){
                $('#edit-exception').modal('hide');
                dataTableExc.draw();
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
        })
      }
    });
    $(document).on('click', '.deleteexception', function() {
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
        title:'Delete list exception?',
        message:'Data that has been deleted cannot be recovered',
        callback: function(result) {
          if(result) {
            var data = {
              _token: "{{ csrf_token() }}",
              id: id
            };
            $.ajax({
              url: `{{url('admin/calendarexc')}}/${id}`,
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
                dataTableExc.ajax.reload( null, false );
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
    $(document).on('click','.editexception',function(){
      var id = $(this).data('id');
      $.ajax({
        url:`{{url('admin/calendarexc')}}/${id}/edit`,
        method:'GET',
        dataType:'json',
        beforeSend:function(){
          $('#box-menu .overlay').removeClass('d-none');
        },
      }).done(function(response){
        $('#box-menu .overlay').addClass('d-none');
        if(response.status){
          var date = changeDateFormat(response.data.date_exception);
          $('#edit-exception .modal-title').html('Edit Exception');
          $('#edit-exception').modal('show');
          $('#form-edit')[0].reset();
          $('#form-edit .invalid-feedback').each(function () { $(this).remove(); });
          $('#form-edit .form-group').removeClass('has-error').removeClass('has-success');
          $('#form-edit input[name=_method]').attr('value','PUT');
          $('#form-edit input[name=calendar_id]').attr('value',{{ $calendar->id }});
          $('#form-edit input[name=exception_id]').attr('value',id);
          $('#form-edit input[name=exception_date]').daterangepicker({startDate:date, endDate:date, singleDatePicker:true, locale:{format:'DD/MM/YYYY'}});
          $('#form-edit input[name=exception_desc]').attr('value',response.data.description);
          $('#form-edit input[name=exception_label]').attr('value',response.data.label_color);
          $('#form-edit input[name=exception_text]').attr('value',response.data.text_color);
          $('#form-edit #label_span').css('background-color',response.data.label_color);
          $('#form-edit #text_span').css('background-color',response.data.text_color);
          $('#form-edit').attr('action',`{{url('admin/calendarexc/')}}/${response.data.id}`);
        }          
      }).fail(function(response){
        var response = response.responseJSON;
        $('#box-menu .overlay').addClass('d-none');
        $.gritter.add({
          title: 'Error!',
          text: response.message,
          class_name: 'gritter-error',
          time: 1000,
        });
      });	
    });
  })
</script>
@endpush