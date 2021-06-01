@extends('admin.layouts.app')
@section('title',__('overtimescheme.otschem'))
@section('stylesheets')
<link href="{{ asset('adminlte/component/dataTables/css/datatables.min.css') }}" rel="stylesheet">
@endsection
@push('breadcrump')
<li class="breadcrumb-item active">{{ __('overtimescheme.otschem') }}</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">{{ __('overtimescheme.otschem') }} {{ __('general.list') }}</h3>
          <div class="pull-right card-tools">
            <a href="{{route('overtimescheme.create')}}"
              class="btn btn-{{ config('configs.app_theme') }} btn-sm text-white" data-toggle="tooltip"
              title="{{ __('general.crt') }}">
              <i class="fa fa-plus"></i>
            </a>
            <a href="#" onclick="filter()" class="btn btn-default btn-sm" data-toggle="tooltip" title="Search">
              <i class="fa fa-search"></i>
            </a>
          </div>
        </div>
        <div class="card-body">
          <table class="table table-striped table-bordered datatable" style="width: 100%">
            <thead>
              <tr>
                <th width="10">No</th>
                <th width="100">{{ __('overtimescheme.schemname') }}</th>
                <th width="100">{{ __('general.category') }}</th>
                <th width="100">{{ __('overtimescheme.worktime') }}</th>
                <th width="10">{{ __('general.act') }}</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="add-filter" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog"
  aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Filter</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>
      <div class="modal-body">
        <form id="form-search" autocomplete="off">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="name">{{ __('overtimescheme.schemename') }}</label>
                <input type="text" name="name" class="form-control" placeholder="{{ __('overtimescheme.schemename') }}">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="working_time">{{ __('overtimesheme.worktime') }}</label>
                <input type="number" name="working_time" class="form-control" placeholder="{{ __('overtimesheme.worktime') }}">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="category">{{ __('general.category') }}</label>
                <select name="category" id="category" class="form-control select2" style="width: 100%" aria-hidden="true">
                  <option value="">All</option>
                  @foreach(config('enums.allowance_category') as $key => $value)
                  <option value="{{ $key }}">{{ $value }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button form="form-search" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="Apply"><i
            class="fa fa-search"></i></button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootbox/bootbox.min.js')}}"></script>
<script type="text/javascript">
  function filter() {
    $('#add-filter').modal('show');
  }
  $('.select2').select2();
  $(function() {
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
        title:'Delete overtime scheme?',
        message:'Data that has been deleted cannot be recovered',
        callback: function(result) {
            if(result) {
              var data = { _token: "{{ csrf_token() }}" };
              $.ajax({
                url: `{{url('admin/overtimescheme')}}/${id}`,
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
    dataTable = $('.datatable').DataTable({
      stateSave:true,
      processing: true,
      serverSide: true,
      filter:false,
      info:false,
      lengthChange:true,
      responsive: true,
      order: [[ 1, "asc" ]],
      language: {
            lengthMenu: `{{ __('general.showent') }}`,
            processing: `{{ __('general.process') }}`,
            paginate: {
                previous: `{{ __('general.prev') }}`,
                next: `{{ __('general.next') }}`,
            }
        },
      ajax: {
          url: "{{route('overtimescheme.read')}}",
          type: "GET",
          data:function(data){
              var name = $('#form-search').find('input[name=name]').val();
              var category = $('#form-search').find('select[name=category]').val();
              var working_time = $('#form-search').find('input[name=working_time]').val();
              data.name = name;
              data.category = category;
              data.working_time = working_time;
          }
      },
      columnDefs: [
        {orderable: false, targets: [0,4]},
        {className: "text-center", targets: [0,4]},
        { render: function ( data, type, row ) {
          return row.working_time + ' Hours';
          },targets: [3]
        },
        { render: function ( data, type, row ) {
          return `<div class="dropdown">
                      <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                          <i class="fa fa-bars"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-right">
                          <li><a class="dropdown-item" href="{{url('admin/overtimescheme')}}/${row.id}/edit"><i class="fas fa-pencil-alt mr-2"></i> {{ __('general.edt') }}</a></li>
                          <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> {{ __('general.dlt') }}</a></li>
                      </ul>
                  </div>`
          },targets: [4]
        }
      ],
      columns: [
        { data: "no" },
        { data: "scheme_name" },
        { data: "category" },
        { data: "working_time" },
        { data: "id" },
      ]
    });
  });
</script>
@endpush