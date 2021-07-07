@extends('admin.layouts.app')

@section('title', __('leavereport.detaillv'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('leavereport.index')}}">{{ __('leavereport.leaverpt') }}</a></li>
<li class="breadcrumb-item active">Detail</li>
@endpush

@section('content')
<form id="form" class="form-horizontal no-gutters" method="post" enctype="multipart/form-data">
  <div class="row">
    {{ csrf_field() }}
    {{ method_field('put') }}
    <div class="col-lg-8">
      <div class="row">
        <div class="col-lg-12">
          <div class="card card-{{ config('configs.app_theme') }} card-outline">
            <div class="card-header">
              <div class="card-title">Data {{ __('employee.employ') }}</div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>{{ __('employee.employ') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('employee.employ') }}" id="employee" name="employee" value="{{ $leave->employee->name }}" readonly>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>NID</label>
                    <input type="hidden" class="form-control" placeholder="Employee ID" id="employee_id" name="employee_id" value="{{ $leave->employee->id }}" readonly>
                    <input type="text" class="form-control" placeholder="NID ID" id="nid" name="nid" value="{{ $leave->employee->nid }}" readonly>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>{{ __('position.pos') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('position.pos') }}" id="title" name="title" value="{{ $leave->employee->title->name }}" readonly>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>{{ __('department.dep') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('department.dep') }}" id="department" name="department" value="{{ $leave->employee->department->name }}" readonly>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="card card-{{ config('configs.app_theme')}} card-outline">
            <div class="card-header">
              <h3 class="card-title">{{ __('leavereport.listleav') }}</h3>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <input type="hidden" name="total_days" id="total_days" value="{{ $leave->duration }}">
                <table id="leave-list" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th style="text-align: right" width="25">No</th>
                      <th style="text-align: center">{{ __('general.date') }}</th>
                      <th style="text-align: center">{{ __('attendancelog.time') }}</th>
                      <th style="text-align: center">{{ __('general.type') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @if (count($leave->log) > 0)
                    @foreach ($leave->log as $key => $value)
                    <tr data-key="{{ $key }}" data-date="{{ $value->date }}" data-time_start="{{ $value->start }}" data-time_finish="{{ $value->finish }}" data-type="{{ $value->type }}">
                      <td class="text-center">{{ ($key + 1) }}</td>
                      <td class="text-center">{{ changeDateFormat('d/m/Y', $value->date) }}</td>
                      <td class="text-center">{{ $value->start . ' - ' . $value->finish }}</td>
                      <td class="text-center">
                        @switch($value->type)
                        @case('fullday')
                        Full Day
                        @break
                        @default
                        Hours
                        @endswitch
                      </td>
                    </tr>
                    @endforeach
                    @endif
                  </tbody>
                  <tfoot>
                    <tr>
                      <th colspan="2" class="text-right">Total ({{ __('general.day') }})</th>
                      <th colspan="3" data-total_days="{{ $leave->duration }}">{{ $leave->duration }} {{ __('general.day') }}</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <div class="card-title">{{ __('general.other') }}</div>
          <div class="pull-right card-tools">
            <a href="javascript:void(0)" onclick="backurl()" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label>Status</label>
                <input type="text" class="form-control" placeholder="Status" id="status" name="status" value="{{ $leave->status == 1 ? "Approved" : "Waiting Approval" }}" readonly>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label class="control-label" for="leave_type">{{ __('leave.leavetp') }}</label>
                <input type="text" class="form-control" placeholder="{{ __('leave.leavetp') }}" id="leave_type" name="leave_type" @if ($leave->leavesetting) value="{{ $leave->leavesetting->leave_name }}" @endif readonly>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label class="control-label" for="remaining">{{ __('leavereport.remain') }}</label>
                <input type="text" class="form-control" placeholder="{{ __('leavereport.remain') }}" id="remaining" name="remaining" @if ($balance->first()->balance == -1)
                value="∞"
                @else
                value="{{ $balance->first()->remaining_balance }}"
                @endif readonly>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label for="document">{{ __('leavereport.supdoc') }}</label>
                <div class="custom-file">
                  <input type="file" class="form-control" name="document" id="document" accept="image/jpeg,image/png,application/pdf" readonly />
                </div>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label class="control-label" for="notes">{{ __('general.notes') }}</label>
                <textarea class="form-control" id="notes" name="notes" rows="5" placeholder="{{ __('general.notes') }}" readonly>{{ $leave->notes }}</textarea>
              </div>
            </div>
          </div>
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
<script src="{{asset('adminlte/component/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
<script type="text/javascript">
  var document_name = null;
  @if ($leave->supporting_document)
  document_name = {!! $leave->supporting_document !!}
  @endif
  var url = '/media/leavedocument/' + document_name;
  var ext = url.split('.');
  var initialPreviewPdf = {type: "pdf", size: 5000, caption: "Supporting_document.pdf", url: url, key: 1, downloadUrl: false, showRemove: false}; // disable download
  var initialPreviewImage = {caption: "Supporting_document.jpg", size: 827000, width: "120px", url: url, key: 1, showRemove: false};
  var initialPreview = [];
  switch (ext[1]) {
    case 'pdf':
      initialPreview = initialPreviewPdf
      break;
  
    default:
      initialPreview = initialPreviewImage
      break;
  }
  var availableDates = <?= json_encode($leave->log) ?>;
  $(function() {
    if (document_name) {  
      $("#document").fileinput({
        browseClass: "btn btn-{{ config('configs.app_theme') }}",
        showRemove: false,
        showUpload: false,
        allowedFileExtensions: ["png", "jpg", "jpeg", "pdf"],
        dropZoneEnabled: false,
        theme: 'explorer-fas',
        initialPreview: [
          url
        ],
        initialPreviewAsData: true,
        initialPreviewConfig: [
          initialPreview
        ]
      });
    } else {
      $("#document").fileinput({
        browseClass: "btn btn-{{ config('configs.app_theme') }}",
        showRemove: false,
        showUpload: false,
        allowedFileExtensions: ["png", "jpg", "jpeg", "pdf"],
        dropZoneEnabled: false,
        theme: 'explorer-fas',
      });
    }

    $(document).on("change", "#document", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
    });
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
              one_year:`${item.one_year}`
            });
          });
          return {
            results: option, more: more,
          };
        },
      },
    });
    @if ($leave->employee)
		$("#employee").select2('data',{id:{{$leave->employee->id}},text:'{{$leave->employee->name}}'}).trigger('change');
		@endif
    @if ($leave->leave_setting_id)
    $("#leave_type").select2('data',{id:{{$leave->leave_setting_id}},text:'{{$leave->leavesetting->leave_name}}'}).trigger('change');
    @endif
  
    $('#employee').on('select2:clear', function () {
      $('#employee_id').select2('val', '');
      $('#department').val('');
      $('#title').val('');
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

  function showList() {
    $('#list-leave').find('.modal-title').html('Add Date');
    $('input[name=status_list]').val('add');
    $('#list-leave').find('button[type=submit]').html(`<b><i class="fa fa-plus"></i></b>`);
    $('#list-leave').modal('show');
  }

  $('#form_date').validate({
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
      var start_date = $('#range_date_start').val(),
        field_start_date = start_date.split('/'),
        date_1 = field_start_date[0],
        month_1 = (parseInt(field_start_date[1]) - 1),
        year_1 = field_start_date[2],
        start = moment([parseInt(year_1), parseInt(month_1), parseInt(date_1)]);
      var finish_date = $('#range_date_finish').val(),
        field_finish_date = finish_date.split('/'),
        date_2 = field_finish_date[0],
        month_2 = (parseInt(field_finish_date[1]) - 1),
        year_2 = field_finish_date[2],
        finish = moment([parseInt(year_2), parseInt(month_2), parseInt(date_2)]),
        diff = finish.diff(start, 'days');
      if (`${year_1}${month_1}${date_1}` > `${year_2}${month_2}${date_2}` || ($('#range_time_start').val() < '09:00' || $('#range_time_finish').val() > '17:00')) {
        $.gritter.add({
          title: 'Error!',
          text: 'Invalid date',
          class_name: 'gritter-error',
          time: 1000,
        });

        return;
      }
      if ($('input[name=status_list]').val() == 'update') {
        var key = $('input[name=key_list]').val(),
          list_type = $('#list_type').val(),
          time_start = $('#range_time_start').val(),
          time_finish = $('#range_time_finish').val();

        availableDates[key].type = list_type;
        availableDates[key].start_time = time_start;
        availableDates[key].finish_time = time_finish;

        generateHTML(availableDates);

        $('#list_type').find(':selected').removeAttr('selected');
        $('#range_date_start').val(moment().format('DD/MM/YYYY'));
        $('#range_date_finish').val(moment().format('DD/MM/YYYY'));
        $('#range_time_start').val('09:00');
        $('#range_time_finish').val('17:00');

        $('#list-leave').modal('hide');

        return;
      }
      $.ajax({
        url:$('#form_date').attr('action'),
        method:'post',
        data: {
          status: `update`,
          _token: $('input[name="_token"]').val(),
          employee_id: $('#employee_id').val(),
          range_date_start: start.format('YYYY-MM-DD'),
          range_date_finish: finish.format('YYYY-MM-DD'),
          routine: `1 day`,
          range_time_start: $('#range_time_start').val(),
          range_time_finish: $('#range_time_finish').val(),
          type: $('#list_type').val(),
          availableDates: availableDates
        },
        dataType: 'json',
        beforeSend:function(){
            $('.overlay').removeClass('d-none');
        }
      }).done(function(response){
            $('.overlay').addClass('d-none');
            availableDates = [];
            $.each(response, function(key, value) {
              availableDates.push(value);
            });

            generateHTML(response);

            $('#list_type').find(':selected').removeAttr('selected');
            $('#range_date_start').val(moment().format('DD/MM/YYYY'));
            $('#range_date_finish').val(moment().format('DD/MM/YYYY'));
            $('#range_time_start').val('09:00');
            $('#range_time_finish').val('17:00');

            $('#list-leave').modal('hide');
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

  function removeLeave(that) {
    let _token   = $('meta[name="csrf-token"]').attr('content');
		$.ajax({
      url: "{{ route('leave.removedate') }}",
      method: "POST",
      data: {
        dates: availableDates,
        selected_date: $(that).data('selected_date'),
        _token: _token,
      },
      dataType: 'json'
    })
    .done(function(response) {
      if ($('#leave-list > tbody').find('tr:not(.no-data)').length <= 0) {
        var html = `<tr class="no-data"><td class="text-center" colspan="5">No data.</td></tr>`;
        $('#leave-list > tbody').append(html);
        return;
      }

      availableDates = [];
      $.each(response, function(key, value) {
        availableDates.push(value);
      });

      generateHTML(response);
    });
	}

  function editLeave(that) {
		var date = $(that).parents('tr').data('date').split('-'),
			day = date[2],
			month = (parseInt(date[1]) - 1),
			year = date[0],
			date = moment([parseInt(year), parseInt(month), parseInt(day)]),
			time_start = $(that).parents('tr').data('time_start'),
			time_finish = $(that).parents('tr').data('time_finish'),
			type = $(that).parents('tr').data('type'),
			key = $(that).parents('tr').data('key');
		$('#list_type').val(type).trigger('change');
		$('#range_date_start').val(date.format('DD/MM/YYYY'));
		$('#range_date_finish').val(date.format('DD/MM/YYYY'));
		$('#range_date_start').prop('disabled', true);
		$('#range_date_finish').prop('disabled', true);
		$('#range_time_start').val(time_start);
		$('#range_time_finish').val(time_finish);
		if (type == 'hours') {
			$('#range_time_start').prop('readonly', false);
			$('#range_time_finish').prop('readonly', false);
		}
		$('#list-leave').find('.modal-title').html('Update Date');
		$('input[name=status_list]').val('update');
		$('input[name=key_list]').val(key);
		$('#list-leave').find('button[type=submit]').html(`<i class="fa fa-plus"></i>`);
		$('#list-leave').modal('show');
	}

  function generateHTML(data) {
		var html = '',
			total_time = 0;

		$.each(data, function(key, value) {
			var no = (parseInt(key) + 1),
				date = value.date.split('-'),
				day = date[2],
				month = date[1],
				year = date[0],
				time_start = value.start_time,
				time_finish = value.finish_time;

			switch (value.type) {
				case "hours":
					var type_txt = "Hours";
					var time_s = new Date(`${year}-${month}-${day} ${time_start}:00`).getHours();
					var time_f = new Date(`${year}-${month}-${day} ${time_finish}:00`).getHours();
					total_time += (time_f - time_s);
					break;

				default:
					var type_txt = "Full Day";
					total_time += 8;
					break;
			}

			html += `<tr data-key="${key}" data-date="${value.date}" data-time_start="${time_start}" data-time_finish="${time_finish}" data-type="${value.type}">
				<td class="text-center">${no}</td>
				<td class="text-center">${day}/${month}/${year}</td>
				<td class="text-center">${time_start} - ${time_finish}</td>
				<td class="text-center">${type_txt}</td>
				<td class="text-center"><a href="javascript:;" title="Edit Data"><i class="fas fa-edit" onclick="editLeave(this)"></i></a> / <a href="javascript:;" class="link-red" title="Delete Data"><i class="fa fa-trash" onclick="removeLeave(this)" data-key="${key}" data-selected_date="${value.date}"></i></a></td>
			</tr>`;
		});

		total_time = (parseInt(total_time) / 8);

		$('#leave-list > tbody').find('tr').remove();
		$('#leave-list > tbody').append(html);
		$('#leave-list > tfoot').find('th[data-total_days]').attr('data-total_days', total_time);
		$('#leave-list > tfoot').find('th[data-total_days]').html(`${total_time} Days`);
		$('input[name=total_days]').val(total_time);
	}
</script>
@endpush