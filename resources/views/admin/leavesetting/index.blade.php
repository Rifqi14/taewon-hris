@extends('admin.layouts.app')

@section('title',__('leavesetting.leaveset'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active">{{ __('leavesetting.leaveset') }}</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title">{{ __('general.list') }} {{ __('leavesetting.leaveset') }}</div>
          <div class="pull-right card-tools">
            <a href="{{route('leavesetting.create')}}"
              class="btn btn-{{ config('configs.app_theme')}} btn-sm text-white" data-toggle="tooltip" title="{{ __('general.crt') }}">
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
                <th width="5">#</th>
                <th width="100">{{ __('leavesetting.leavename') }}</th>
                <th width="50">{{ __('leavesetting.balyear') }}</th>
                <th width="100">{{ __('general.desc') }}</th>
                <th width="5">{{ __('general.act') }}</th>
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
                <label class="control-label" for="leave_name">{{ __('leavesetting.leavename') }}</label>
                <input type="text" name="leave_name" class="form-control" placeholder="{{ __('leavesetting.leavename') }}">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="balance">{{ __('leavesetting.balyear') }}</label>
                <input type="text" name="balance" class="form-control" placeholder="{{ __('leavesetting.balyear') }}">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="description">{{ __('general.desc') }}</label>
                <select name="description" id="description" class="form-control select2" style="width: 100%"
                  aria-hidden="true">
                  <option value="">All</option>
                  <option value="Paid Leave">{{ __('leavesetting.paid') }}</option>
                  <option value="Unpaid Leave">{{ __('leavesetting.unpaid') }}</option>
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
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootbox/bootbox.min.js')}}"></script>
<script type="text/javascript">
  function filter(){
		$('#add-filter').modal('show');
	}
  $(function () {
    dataTable = $('.datatable').DataTable({
      stateSave:true,
      processing:true,
      serverSide:true,
      filter:false,
      info:false,
      lengthChange:true,
      responsive:true,
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
        url: "{{route('leavesetting.read')}}",
        type: "GET",
        data:function(data){
          var leave_type = $('#form-search').find('input[name=leave_type]').val();
          var balance = $('#form-search').find('input[name=balance]').val();
          var description = $('#form-search').find('select[name=description]').val();
          data.leave_type = leave_type;
          data.balance = balance;
          data.description = description;
        }
      },
      columnDefs:[
        { orderable: false,targets:[0] },
        { className: "text-right", targets: [0] },
        { className: "text-center", targets: [2,3,4] },
        { render: function(data, type, row) {
          if (row.balance == -1) {
            return `&infin; days`;
          } else {
            return `${row.balance} days`;
          }
          }, targets:[2]},
        { render: function ( data, type, row ) {
          var html = '';
          for (i = 1; i <= row.level; i++) {    
            if (row.parent_id == 0) {
              html += '';
            } else {
              html += '&emsp;';
            }
          }
          return `<p>${html}${row.leave_name}</p>`;
          },targets: [1]
        },
        { render: function(data, type, row) {
            if (row.description == 1) {
              return `<span class="badge badge-success">{{ __('leavesetting.paid') }}</span>`;
            } else {
              return `<span class="badge badge-danger">{{ __('leavesetting.unpaid') }}</span>`;
            }
          }, targets:[3]},
        { render: function ( data, type, row ) {
          return `<div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                      <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                      <li><a class="dropdown-item" href="{{url('admin/leavesetting')}}/${row.id}/edit"><i class="fas fa-pencil-alt mr-2"></i> {{ __('general.edt') }}</a></li>
                      <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> {{ __('general.dlt') }}</a></li>
                    </ul>
                  </div>`
          },targets: [4]
        }
      ],
      columns: [
        { data: "no" },
        { data: "leave_name" },
        { data: "balance" },
        { data: "description" },
        { data: "id" },
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
        title:'Delete Leave Balance?',
        message:'Data that has been deleted cannot be recovered',
        callback: function(result) {
          if(result) {
            var data = {
              _token: "{{ csrf_token() }}"
            };
            $.ajax({
              url: `{{url('admin/leavesetting')}}/${id}`,
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
  });
  $(document).ready(function() {
    $('.select2').select2();
  });
</script>
@endpush