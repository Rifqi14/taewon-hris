@extends('admin.layouts.app')

@section('title', 'Detail Calendar')

@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/fullcalendar/lib/main.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}" rel="stylesheet">
<style>
  #start_specific .datepicker {
    z-index: 1151 !important;
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
            <label class="col-md-2 col-xs-12 control-label" for="code">Calendar Code</label>
            <div class="col-sm-6 controls">
              <input type="text" class="form-control" id="code" name="code" placeholder="Calendar Code"
                value="{{ $calendar->code }}" readonly>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-2 col-xs-12 control-label" for="calendar_name">Calendar Name</label>
            <div class="col-sm-6 controls">
              <input type="text" class="form-control" id="calendar_name" name="calendar_name"
                placeholder="Calendar Name" value="{{ $calendar->name }}" readonly>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-xs-12 col-form-label" for="label_color">Label Color</label>
            <div class="col-sm-6">
              <div class="input-group my-colorpicker2">
                <input type="text" class="form-control" value="{{ $calendar->label_color }}" name="label_color"
                  id="label_color" readonly>
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
              <textarea class="form-control" id="calendar_desc" name="calendar_desc" placeholder="Calendar Description"
                readonly>{{ $calendar->description }}</textarea>
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
      {{-- Header & Tools --}}
      <div class="card-header">
        <h3 class="card-title">Calendar</h3>
      </div>
      {{-- End of Header & Tools --}}
      <div class="card-body">
        <div id="calendar"></div>
      </div>
      {{-- Overlay loading --}}
      {{-- End of Overlay loading --}}
    </div>
  </div>
</div>
{{-- End of Table List Exception --}}
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/fullcalendar/lib/main.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('.i-checks').iCheck({
      checkboxClass: 'icheckbox_square-green',
      radioClass: 'iradio_square-green',
    });
    $('.my-colorpicker2').each(function () {
      $(this).colorpicker();
    });
    var exception = {!! json_encode($exception) !!};
    console.log(exception);
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      headerToolbar: {
        start: 'prev,today',
        center: 'title',
        end: 'today,next'
      },
      events: exception,
      firstDay: 1
    });
    calendar.render();
  })
</script>
@endpush