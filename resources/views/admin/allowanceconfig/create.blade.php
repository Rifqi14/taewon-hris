@extends('admin.layouts.app')

@section('title', 'Create Allowance Config')
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
<li class="breadcrumb-item"><a href="{{ route('allowanceconfig.index') }}">Penalty Config</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush

@section('content')
<form action="{{ route('allowanceconfig.store') }}" id="form" autocomplete="off" method="POST">
  @csrf
  <div class="row">
    <div class="col-lg-8">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title" style="padding-bottom: .8rem">Allowance Config Data</h3>
        </div>
        <div class="card-body">
          @csrf
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="workgroupID" class="control-label">Workgroup <b class="text-danger">*</b></label>
                <input type="text" name="workgroup_id" id="workgroup_id" class="form-control" data-placeholder="Select Workgroup" required>
                <input type="hidden" name="workgroup" id="workgroup">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="allowance_id" class="control-label">Allowance <b class="text-danger">*</b></label>
                <input type="text" name="allowance_id" id="allowance_id" class="form-control" data-placeholder="Select Allowance Type" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="type" class="control-label">Type <b class="text-danger">*</b></label>
                <select name="type" id="type" class="form-control select2" data-placeholder="Select Type" required>
                  @foreach (config('enums.penalty_config_type') as $key => $item)
                  <option value="{{ $key }}">{{ $item }}</option>
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
          <h3 class="card-title">Other</h3>
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }} text-white" title="Save"><i class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Back"><i class="fa fa-reply"></i></a>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="notes" class="control-label">Notes</label>
                <textarea name="notes" id="notes" style="height: 120px" class="form-control" placeholder="Notes"></textarea>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label for="status" class="control-label">Status</label>
                <select name="status" id="status" class="form-control select2" data-placeholder="Select Status" required>
                  <option value="ACTIVE">Active</option>
                  <option value="NON-ACTIVE">Non Active</option>
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
          <h3 class="card-titl">Allowance</h3>
        </div>
        <div class="card-body">
          <table class="table table-striped table-bordered datatable" id="allowance-table" style="width: 100%">
            <thead>
              <tr>
                <th width="10">No</th>
                <th width="200">Allowance</th>
                <th width="200">Category</th>
                <th width="200">Group</th>
                <th width="10">
                  <div class="customcheckbox">
                    <input type="checkbox" name="checkall" class="checkall" id="checkall">
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
  $(document).ready(function() {
    $('.select2').select2();
    $('#workgroup_id').select2({
      ajax: {
        url: "{{ route('allowanceconfig.selectworkgroup') }}",
        type: "GET",
        dataType: "JSON",
        data: function(term, page) {
          return { 
            
            name: term,
            page: page,
            limit: 30 };
        },
        results: function(data, page) {
          var more = (page * 30) < data.total;
          var option = [];
          $.each(data.rows, function(index, item) {
            option.push({
              id: item.id,
              text: `${item.workgroup_name}`,
              workgroup_id : item.workgroup_id
            });
          });
          return { results: option, more: more, };
        },
      },
      allowClear: true,
    });
    $('#allowance_id').select2({
      ajax: {
        url: "{{ route('allowanceconfig.selectallowance') }}",
        type: "GET",
        dataType: "JSON",
        data: function(term, page) {
          return { 
            workgroup_id : $('#workgroup').val(),
            name: term,
            page: page,
            limit: 30 };
        },
        results: function(data, page) {
          var more = (page * 30) < data.total;
          var option = [];
          $.each(data.rows, function(index, item) {
            option.push({
              id: item.id,
              text: `${item.allowance}`,
            });
          });
          return { results: option, more: more, };
        },
      },
      allowClear: true,
    });
    
    $(document).on("change", "#workgroup_id", function () {
      var workgroup_id = $(this).select2('data').workgroup_id;
      $('#workgroup').val(`${workgroup_id}`);
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
      $('#allowance_id').select2('val','');
    });
    $(document).on("change", "#allowance_id", function () {
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
        url: "{{ route('allowance.read') }}",
        type: "GET",
        data: function(data) {
          
        }
      },
      columnDefs: [
        { orderable: false, targets: [0, 4] },
        { className: 'text-right', targets: [0] },
        { className: 'text-center', targets: [4] },
        { render: function( data, type, row ) {
          return `<label class="customcheckbox checked"><input value="${row.id}" type="checkbox" name="allowanceID[]" checked><span class="checkmark"></span></label>`
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

    $(document).on('change', '#type', function() {
      if (this.value == BASIC) {
        $('.allowance-section').addClass('d-none');
      } else {
        $('.allowance-section').removeClass('d-none');
      }
    }).trigger('change');
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
  });
</script>
@endpush