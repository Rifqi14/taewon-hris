@extends('admin.layouts.app')

@section('title', __('penaltyconfig.pnltycon'))
@section('stylesheets')
<link rel="stylesheet" href="{{ asset('adminlte/component/dataTables/css/datatables.min.css') }}">
<style type="text/css">
  .customcheckbox {
    width: 22px;
    height: 22px;
    background: url("/img/green.png") no-repeat;
    background-position-x: 0%;
    background-position-y: 0%;
    cursor: pointer;
    margin: 0 auto;
  }

  .customcheckbox.checked {
    background-position: -48px 0;
  }

  .customcheckbox:hover {
    background-position: -24px 0;
  }

  .customcheckbox.checked:hover {
    background-position: -48px 0;
  }

  .customcheckbox input {
    cursor: pointer;
    opacity: 0;
    scale: 1.6;
    width: 22px;
    height: 22px;
    margin: 0;
  }
</style>
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{ route('penaltyconfig.index') }}">{{ __('penaltyconfig.pnltycon') }}</a></li>
<li class="breadcrumb-item active">{{ __('general.edt') }}</li>
@endpush

@section('content')
<form action="{{ route('penaltyconfig.update', ['id'=>$penaltyConfig->id]) }}" id="form" autocomplete="off" method="POST">
  <div class="row">
    <div class="col-lg-8">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title" style="padding-bottom: .8rem">{{ __('general.edt') }} {{ __('penaltyconfig.pnltycon') }}</h3>
        </div>
        <div class="card-body">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="workgroupID" class="control-label">{{ __('workgroup.workgrp') }} <b class="text-danger">*</b></label>
                <input type="text" name="workgroupID" id="workgroupID" class="form-control" data-placeholder="{{ __('general.chs') }} {{ __('workgroup.workgrp') }}" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="leaveSettingID" class="control-label">{{ __('penaltyconfig.leavetp') }} <b class="text-danger">*</b></label>
                <input type="text" name="leaveSettingID" id="leaveSettingID" class="form-control" data-placeholder="{{ __('general.chs') }} {{ __('penaltyconfig.leavetp') }}" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="type" class="control-label">{{ __('penaltyconfig.pnlty') }} {{ __('general.type') }} <b class="text-danger">*</b></label>
                <select name="type" id="type" class="form-control select2" data-placeholder="{{ __('general.chs') }} {{ __('general.type') }}" required>
                  @foreach (config('enums.penalty_config_type') as $key => $item)
                  <option value="{{ $key }}" @if ($penaltyConfig->type==$key) selected @endif>{{ $item }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">{{ __('general.other') }}</h3>
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }} text-white" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="{{ __('general.prvious') }}"><i class="fa fa-reply"></i></a>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="notes" class="control-label">{{ __('general.notes') }}</label>
                <textarea name="notes" id="notes" style="height: 120px" class="form-control" placeholder="{{ __('general.notes') }}">{{ $penaltyConfig->notes }}</textarea>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label for="status" class="control-label">Status</label>
                <select name="status" id="status" class="form-control select2" data-placeholder="Select Status" required>
                  <option value="ACTIVE" @if ($penaltyConfig->status == 'ACTIVE') selected @endif>{{ __('general.actv') }}</option>
                  <option value="NON-ACTIVE" @if ($penaltyConfig->status == 'NON-ACTIVE') selected @endif>{{ __('general.noactv') }}</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-12 allowance-section d-none">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-titl">{{ __('allowance.alw') }}</h3>
        </div>
        <div class="card-body">
          <table class="table table-striped table-bordered datatable" id="allowance-table" style="width: 100%">
            <thead>
              <tr>
                <th width="10">No</th>
                <th width="200">{{ __('allowance.alw') }}</th>
                <th width="200">{{ __('general.category') }}</th>
                <th width="200">{{ __('groupallowance.grpalw') }}</th>
                <th width="10">
                  <div class="customcheckbox">
                    <input type="checkbox" name="checkall" class="checkall" id="checkall" onclick="checkAll(this)">
                  </div>
                </th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
    <div class="overlay d-none">
      <i class="fa fa-2x fa-sync-alt fa-spin"></i>
    </div>
  </div>
</form>
@endsection
@push('scripts')
<script src="{{ asset('adminlte/component/validate/jquery.validate.min.js') }}"></script>
<script src="{{ asset('adminlte/component/dataTables/js/datatables.min.js') }}"></script>
<script>
  const BASIC = 'BASIC';
  var data = [];

  function checkAll(data) {
    $.ajax({
			url: `{{ route('penaltyconfig.updateall') }}`,
			method: 'post',
			data: {
				_token: "{{ csrf_token() }}",
				penaltyConfigID: `{{ $penaltyConfig->id }}`,
				status: data.checked ? 1 : 0,
			},
			dataType: 'json',
			beforeSend: function() {
				$('.overlay').removeClass('d-none');
			}
		}).done(function(response) {
			$('.overlay').addClass('d-none');
			if (response.status) {
				$.gritter.add({
					title: 'Success!',
					text: response.message,
					class_name: 'gritter-success',
					time: 1000,
				});
			} else {
				$.gritter.add({
					title: 'Warning!',
					text: response.message,
					class_name: 'gritter-warning',
					time: 1000,
				});
			}
			return;
		}).fail(function(response) {
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
  function updateAllowance(data) {
    var penaltyConfigID, allowanceID, status;
		if (data.checked) {
			penaltyConfigID	= `{{ $penaltyConfig->id }}`;
			allowanceID		  =	data.value;
			status					= 1;
		} else {
			penaltyConfigID	= `{{ $penaltyConfig->id }}`;
			allowanceID		  =	data.value;
			status					= 0;
		}
		$.ajax({
			url: `{{ route('penaltyconfig.updateallowance') }}`,
			method: 'post',
			data: {
				_token: "{{ csrf_token() }}",
				penaltyConfigID: penaltyConfigID,
				allowanceID: allowanceID,
				status: status,
			},
			dataType: 'json',
			beforeSend: function() {
				$('.overlay').removeClass('d-none');
			}
		}).done(function(response) {
			$('.overlay').addClass('d-none');
			if (response.status) {
				$.gritter.add({
					title: 'Success!',
					text: response.message,
					class_name: 'gritter-success',
					time: 1000,
				});
			} else {
				$.gritter.add({
					title: 'Warning!',
					text: response.message,
					class_name: 'gritter-warning',
					time: 1000,
				});
			}
			return;
		}).fail(function(response) {
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
  $(document).ready(function() {
    $('.select2').select2();
    $('#workgroupID').select2({
      ajax: {
        url: "{{ route('workgroup.select') }}",
        type: "GET",
        dataType: "JSON",
        data: function(term, page) {
          return { name: term, page: page, limit: 30 };
        },
        results: function(data, page) {
          var more = (page * 30) < data.total;
          var option = [];
          $.each(data.rows, function(index, item) {
            option.push({
              id: item.id,
              text: `${item.name}`
            });
          });
          return { results: option, more: more, };
        },
      },
      allowClear: true,
    });
    $('#leaveSettingID').select2({
      multiple: true,
      ajax: {
        url: "{{ route('leavesetting.select') }}",
        type: "GET",
        dataType: "JSON",
        data: function(term, page) {
          return { name: term, page: page, limit: 30 };
        },
        results: function(data, page) {
          var more = (page * 30) < data.total;
          var option = [];
          $.each(data.rows, function(index, item) {
            option.push({
              id: item.id,
              text: `${item.leave_name}`
            });
          });
          return { results: option, more: more, };
        },
      },
      allowClear: true,
    });
    $(document).on("change", "#workgroupID", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
    });
    $(document).on("change", "#leaveSettingID", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
    });

    dataTableAllowance = $('.datatable').DataTable({
      stateSave: true,
      processing: true,
      serverSide: true,
      filter: false,
      info: false,
      lengthChange: false,
      responsive: true,
      paginate: false,
      order: [[ 1, "asc"]],
      ajax: {
        url: "{{ route('penaltyconfig.readallowance') }}",
        type: "GET",
        data: function(data) {
          data.penaltyConfigID = `{{ $penaltyConfig->id }}`
        }
      },
      columnDefs: [
        { orderable: false, targets: [0, 4] },
        { className: 'text-right', targets: [0] },
        { className: 'text-center', targets: [4] },
        { render: function( data, type, row ) {
          return row.detail.length > 0 ? `<label class="customcheckbox checked"><input value="${row.id}" type="checkbox" name="allowanceID[]" onclick="updateAllowance(this)" checked><span class="checkmark"></span></label>` : `<label class="customcheckbox"><input value="${row.id}" type="checkbox" name="allowanceID[]" onclick="updateAllowance(this)"><span class="checkmark"></span></label>`
        }, targets: [4] }
      ],
      columns: [
        { data: 'no' },
        { data: 'allowance' },
        { data: 'category' },
        { data: 'groupallowance' },
        { data: 'id' },
      ]
    });

    $(document).on('change', '#type', function() {
      if (this.value == BASIC) {
        $('.allowance-section').addClass('d-none');
      } else {
        $('.allowance-section').removeClass('d-none');
      }
    });
    $('#type').trigger('change');
		$('input[name=checkall]').prop('checked', true);
		$('input[name=checkall]').parent().addClass('checked');
		$(document).on('click', '.customcheckbox input', function() {
			if ($(this).is(':checked')) {
				$(this).parent().addClass('checked');
			} else {
				$(this).parent().removeClass('checked');
			}
		});
		$(document).on('change', '.checkall', function() {
			if (this.checked) {
				$('input[name^=allowanceID]').prop('checked', true);
				$('input[name^=allowanceID]').parent().addClass('checked');
			} else {
				$('input[name^=allowanceID]').prop('checked', false);
				$('input[name^=allowanceID]').parent().removeClass('checked');
			}
		});


    $("#form").validate({
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
        if (element.is(':file')) {
          error.insertAfter(element.parent().parent().parent());
        } else if(element.parent('.input-group').length) {
          error.insertAfter(element.parent());
        } else if (element.attr('type') == 'checkbox') {
          error.insertAfter(element.parent());
        } else {
          error.insertAfter(element);
        }
      },
      submitHandler: function() {
        $.ajax({
          url: $('#form').attr('action'),
          method: 'POST',
          data: new FormData($('#form')[0]),
          processData: false,
          contentType: false,
          dataType: 'JSON',
          beforeSend: function () {
            $('.overlay').removeClass('d-none');
          }
        }).done(function (response) {
          $('.overlay').addClass('d-none');
          if (response.status) {
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
        }).fail(function(response) {
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
    
    $('#workgroupID').select2('data', {id: `{{ $penaltyConfig->workgroup_id }}`, text: `{!! $penaltyConfig->workgroup->name !!}`}).trigger('change');
    @if (count($penaltyConfig->leave))
      @foreach ($penaltyConfig->leave as $key => $value)
        data.push({id: `{{ $value->id }}`, text: `{!! $value->leave_name !!}`});
      @endforeach
    @endif
    $('#leaveSettingID').select2('data', data).trigger('change');
  });
</script>
@endpush