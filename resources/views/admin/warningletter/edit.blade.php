@extends('admin.layouts.app')

@section('title',__('warningletter.wl'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('warningletter.index')}}">{{__('warningletter.wl')}}</a></li>
<li class="breadcrumb-item active">{{__('general.edt')}}</li>
@endpush

@section('content')
<form id="form" action="{{ route('warningletter.update', ['id' => $warningletter->id]) }}" class="form-horizontal no-gutters" method="post" enctype="multipart/form-data" autocomplete="off">
  <div class="row">
    {{ csrf_field() }}
    {{ method_field('put') }}
    <div class="col-lg-8">
      <div class="row">
        <div class="col-lg-12">
          <div class="card card-{{ config('configs.app_theme') }} card-outline">
            <div class="card-header">
              <div class="card-title">{{__('employee.empdata')}}</div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>{{__('employee.employ')}}</label>
                    <input type="text" class="form-control" placeholder="{{__('employee.employ')}}" id="employee" name="employee"value="{{ $warningletter->employee->name }}" required>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>NIK Taewon</label>
                    <input type="hidden" class="form-control" placeholder="Employee ID" id="employee_id" name="employee_id" value="{{ $warningletter->employee->id }}" readonly>
                    <input type="text" class="form-control" placeholder="NIK Taewon" id="nid" name="nid" value="{{ $warningletter->employee->nid }}" readonly>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>{{__('employee.position')}}</label>
                    <input type="text" class="form-control" placeholder="{{__('employee.position')}}" id="title" name="title" value="{{ $warningletter->employee->title->name }}" readonly>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>{{__('department.dep')}}</label>
                    <input type="text" class="form-control" placeholder="{{__('department.dep')}}" id="department" name="department" value="{{ $warningletter->employee->department->name }}" readonly>
                  </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                      <label>{{__('employee.jd')}}</label>
                      <input type="text" class="form-control" placeholder="{{__('employee.jd')}}" id="join_date" name="join_date" value="{{ $warningletter->employee->join_date }}" readonly>
                    </div>
                  </div>
              </div>
            </div>
            <div class="overlay d-none">
              <i class="fa fa-2x fa-sync-alt fa-spin"></i>
            </div>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="card card-{{ config('configs.app_theme')}} card-outline">
            <div class="card-header">
              <h3 class="card-title">{{__('warningletter.reason')}}</h3>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <textarea name="reason" class="form-control" id="" cols="20" rows="5">{{ $warningletter->notes }}</textarea>
              </div>
            </div>
            <div class="overlay d-none">
              <i class="fa fa-2x fa-sync-alt fa-spin"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <div class="card-title">{{__('general.other')}}</div>
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" id="simpan" title="{{__('general.save')}}"><i class="fa fa-save"></i></button>
            <a href="javascript:void(0)" onclick="backurl()" class="btn btn-sm btn-default" title="{{__('general.prvious')}}"><i class="fa fa-reply"></i></a>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            {{-- <div class="col-sm-12">
              <div class="form-group">
                <label>Status</label>
                <input type="text" class="form-control" placeholder="Status" id="status" name="status" readonly value="Waiting Approval">
              </div>
            </div> --}}
            <div class="col-sm-12">
              <div class="form-group">
                <label class="control-label" for="number_warning_letter">{{__('warningletter.number')}}</label>
                <input type="text" class="form-control" placeholder="{{__('warningletter.number')}}" id="number_warning_letter" value="{{ $warningletter->number_warning_letter }}" name="number_warning_letter" readonly>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label for="document">{{__('warningletter.effdate')}}</label>
                <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label>{{__('general.from')}}</label>
                        <input type="text" class="form-control datepicker" placeholder="{{__('general.from')}}" id="from" name="from" value="{{ date('d/m/Y',strtotime($warningletter->from)) }}">
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label>{{__('general.to')}}</label>
                        <input type="text" class="form-control datepicker" placeholder="{{__('general.to')}}" id="to" name="to" value="{{ date('d/m/Y',strtotime($warningletter->to)) }}" readonly>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
            <div style="height: 157px;"></div>
          </div>
        </div>
        <div class="overlay d-none">
          <i class="fa fa-2x fa-sync-alt fa-spin"></i>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script type="text/javascript">
  var availableDates = [];
  $(function() {
    $('.select2').select2();
    $('#employee').select2({
      ajax: {
        url: "{{route('leave.selectemployee')}}",
        type:'GET',
        dataType: 'json',
        data: function (term,page) {
          return {
            name:term,
            page:page,
            limit:30,
          };
        },
        results: function (data,page) {
          var more = (page * 30) < data.total;
          var option = [];
          $.each(data.rows,function(index,item){
            option.push({
              id:item.id,
              text: `${item.name}`,
              employee_id: `${item.id}`,
              nid: `${item.nid}`,
              department_id:`${item.department_id}`,
              department_name:`${item.department_name}`,
              title_id:`${item.title_id}`,
              title_name:`${item.title_name}`,
              join_date:`${item.join_date}`,
              first_month:`${item.first_month}`,
              third_month:`${item.third_month}`,
              sixth_month:`${item.sixth_month}`,
              one_year:`${item.one_year}`,
              calendar_id: `${item.calendar_id}`
            });
          });
          return {
            results: option, more: more,
          };
        },
      },
    });

    @if ($warningletter->employee)
    $("#employee").select2('data',{id:{{$warningletter->employee->id}},text:'{{$warningletter->employee->name}}'}).trigger('change');
    @endif
    $(document).on("change", "#employee", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
            $('#form').validate().form();
        }
    });
  
    $(document).on('change', '#employee', function () {
      var department_id = $('#employee').select2('data').department_id;
      var department_name = $('#employee').select2('data').department_name;
      var title_id = $('#employee').select2('data').title_id;
      var title_name = $('#employee').select2('data').title_name;
      var employee_id = $('#employee').select2('data').id;
      var nid = $('#employee').select2('data').nid;
      var join_date = $('#employee').select2('data').join_date;
      $('#nid').val(`${nid}`);
      $('#employee_id').val(`${employee_id}`);
      $('#department').val(`${department_name}`);
      $('#title').val(`${title_name}`);
      $('#join_date').val(`${join_date}`);
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
    $('.datepicker').daterangepicker({
			singleDatePicker: true,
			timePicker: false,
			locale: {
				format: 'DD/MM/YYYY'
			}
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
  });
</script>
@endpush