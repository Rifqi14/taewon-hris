@extends('admin.layouts.app')

@section('title',__('penaltyconfig.pnltycon'))
@section('stylesheets')
<link rel="stylesheet" href="{{ asset('adminlte/component/dataTables/css/datatables.min.css') }}">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active">{{ __('penaltyconfig.pnltycon') }}</li>
@endpush

@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card card-{{ config('configs.app_theme') }} card-outline">
      <div class="card-header">
        <h3 class="card-title">{{ __('penaltyconfig.pnltycon') }}</h3>
        <div class="pull-right card-tools">
          <a href="{{ route('penaltyconfig.create') }}" class="btn btn-{{ config('configs.app_theme') }} btn-sm text-white" data-toggle="tooltip" title="{{ __('general.crt') }}"><i class="fa fa-plus"></i></a>
          <a href="#" onclick="filter()" class="btn btn-default btn-sm" data-toggle="tooltip" title="{{ __('general.srch') }}"><i class="fa fa-search"></i></a>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-striped table-bordered datatable" id="penalty-config-table" style="width: 100%">
          <thead>
            <tr>
              <th width="5">#</th>
              <th width="200">{{ __('workgroup.workgrp') }}</th>
              <th width="200">{{ __('penaltyconfig.leavetp') }}</th>
              <th width="50">{{ __('general.type') }}</th>
              <th width="10">Status</th>
              <th width="10">#</th>
            </tr>
          </thead>
        </table>
      </div>
      <div class="overlay d-none">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="add-filter" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">{{ __('general.filter') }}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>
      <div class="modal-body">
        <form autocomplete="off" id="form-search">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="workgroupID" class="control-label">{{ __('workgroup.workgrp') }}</label>
                <input type="text" name="workgroupID" id="workgroupID" class="form-control" placeholder="{{ __('workgroup.workgrp') }}">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="leaveSettingID" class="control-label">{{ __('penaltyconfig.leavetp') }}</label>
                <input type="text" name="leaveSettingID" id="leaveSettingID" class="form-control" placeholder="{{ __('penaltyconfig.leavetp') }}">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="type" class="control-label">{{ __('general.type') }}</label>
                <select name="type" id="type" class="form-control select2" placeholder="{{ __('general.type') }}">
                  <option value=""></option>
                  @foreach (config('enums.penalty_config_type') as $key => $item)
                  <option value="{{ $key }}">{{ $item }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button form="form-search" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="Apply"><i class="fa fa-search"></i></button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('adminlte/component/dataTables/js/datatables.min.js') }}"></script>
<script type="text/javascript">
  function filter() {
    $('#add-filter').modal('show');
  }
  $(function() {
    $(".select2").select2();
    $("#workgroupID").select2({
      ajax: {
        url: "{{ route('workgroup.select') }}",
        type: "GET",
        dataType: "JSON",
        data: function (term, page) {
          return { name: term, page:page, limit: 30};
        },
        results: function (data, page) {
          var more = (page * 30) < data.total;
          var option = [];
          $.each(data.rows, function(index, item) {
            option.push({
              id: item.id,
              text: `${item.name}`
            });
          });
          return { results: option, more: more };
        },
      },
      allowClear: true,
    });
    $("#leaveSettingID").select2({
      ajax: {
        url: "{{ route('leavesetting.select') }}",
        type: "GET",
        dataType: "JSON",
        data: function (term, page) {
          return { name: term, page:page, limit: 30};
        },
        results: function (data, page) {
          var more = (page * 30) < data.total;
          var option = [];
          $.each(data.rows, function(index, item) {
            option.push({
              id: item.id,
              text: `${item.leave_name}`
            });
          });
          return { results: option, more: more };
        },
      },
      allowClear: true,
    });

    dataTable = $('.datatable').DataTable({
      stateSave: true,
      processing: true,
      serverSide: true,
      filter: false,
      info: false,
      lengthChange: true,
      responsive: true,
      order: [[ 1, "asc"]],
      language: {
            lengthMenu: `{{ __('general.showent') }}`,
            processing: `{{ __('general.process') }}`,
            paginate: {
                previous: `{{ __('general.prev') }}`,
                next: `{{ __('general.next') }}`,
            }
        },
      ajax: {
        url: "{{ route('penaltyconfig.read') }}",
        type: "GET",
        data: function(data) {
          var workgroupID     = $('#form-search').find('input[name=workgroupID]').val();
          var leaveSettingID  = $('#form-search').find('input[name=leaveSettingID]').val();
          var type  = $('#form-search').find('select[name=type]').val();
          data.workgroupID    = workgroupID;
          data.leaveSettingID = leaveSettingID;
          data.type           = type;
        }
      },
      columnDefs: [
        { orderable: false, targets: [0, 4, 5] },
        { className: 'text-right', targets: [0] },
        { className: 'text-center', targets: [4,5] },
        { render: function ( data, type, row ) {
          return row.workgroup ? row.workgroup.name : '-';
        }, targets: [1] },
        { render: function ( data, type, row ) {
          return row.status === 'ACTIVE' ? '<span class="badge badge-success">{{ __('general.actv') }}</span>' : '<span class="badge badge-danger">{{ __('general.noactv') }}</span>'
        }, targets: [4] },
        { render: function ( data, type, row ) {
          return `<div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                      <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                      <li><a class="dropdown-item" href="{{url('admin/penaltyconfig')}}/${row.id}/edit"><i class="fas fa-pencil-alt mr-2"></i> {{ __('general.edt') }}</a></li>
                      <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> {{ __('general.dlt') }}</a></li>
                    </ul>
                  </div>`
        }, targets: [5] }
      ],
      columns: [
        { data: "no" },
        { data: "workgroup_id" },
        { data: "leave_name" },
        { data: "type" },
        { data: "status" },
        { data: "no" },
      ]
    });

    $('#form-search').submit(function(e){
			e.preventDefault();
			dataTable.draw();
			$('#add-filter').modal('hide');
		});
    $(document).on('click','.delete',function(){
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
				title:'Delete penalty config?',
				message:'Data that has been deleted cannot be recovered',
				callback: function(result) {
					if(result) {
						var data = {
							_token: "{{ csrf_token() }}"
						};
						$.ajax({
							url: `{{url('admin/penaltyconfig')}}/${id}`,
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
								dataTable.ajax.reload( null, false );
							}
							else{
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
						})
					}
				}
			});
		});
  });
</script>
@endpush