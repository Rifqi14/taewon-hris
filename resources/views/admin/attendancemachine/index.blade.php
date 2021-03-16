@extends('admin.layouts.app')

@section('title', 'Attendance Machine')
@section('stylesheets')
<link rel="stylesheet" href="{{ asset('adminlte/component/dataTables/css/datatables.min.css') }}">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active">Attendance Machine</li>
@endpush

@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Attendance Machine List</h3>
        <div class="pull-right card-tools">
          <a href="{{ route('attendancemachine.create') }}" class="btn btn-{{ config('configs.app_theme') }} btn-sm text-white" data-toggle="tooltip" title="Save"><i class="fa fa-plus"></i></a>
          <a href="#" onclick="filter()" class="btn btn-default btn-sm" data-toggle="tooltip" title="Search"><i class="fa fa-search"></i></a>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-striped table-bordered datatable" style="width: 100%">
          <thead>
            <tr>
              <th width="10">#</th>
              <th width="200">Device Serial Number</th>
              <th width="200">Point Name</th>
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
        <h4 class="modal-title">Filter</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>
      <div class="modal-body">
        <form autocomplete="off" id="form-search">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="deviceSN" class="control-label">Device Serial Number</label>
                <input type="text" name="deviceSN" id="deviceSN" class="form-control" placeholder="Device Serial Number">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="pointName" class="control-label">Point Name</label>
                <select name="pointName" id="pointName" class="form-control select2" aria-placeholder="Point Name">
                  <option value=""></option>
                  <option value="MASUK">Masuk</option>
                  <option value="KELUAR">Keluar</option>
                </select>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="submit" form="form-search" class="btn btn-{{ config('configs.app_theme') }}" title="Apply"><i class="fa fa-search"></i></button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('adminlte/component/dataTables/js/datatables.min.js') }}"></script>
<script type="text/javascript">
  function filter(){
    $('#add-filter').modal('show');
  }
  $(function() {
    dataTable = $('.datatable').DataTable({
      stateSave: true,
      processing: true,
      serverSide: true,
      filter:false,
      info:false,
      lengthChange:true,
      responsive: true,
      order: [[ 2, "asc" ]],
      ajax: {
        url: "{{ route('attendancemachine.read') }}",
        type: "GET",
        data: function(data) {
          var deviceSN      = $('#form-search').find('input[name=deviceSN]').val();
          var pointName     = $('#form-search').find('select[name=pointName]').val();
          data.deviceSN     = deviceSN;
          data.pointName    = pointName;
        }
      },
      columnDefs: [
        { orderable: false, targets: [0,3] },
        { className: 'text-center', targets: [0,3] },
        { render: function (data, type, row) {
          return `<div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item" href="{{url('admin/attendancemachine')}}/${row.id}/edit"><i class="fas fa-pencil-alt mr-2"></i> Edit</a></li>
                        <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> Delete</a></li>
                    </ul>
                  </div>`
        }, targets: [3] },
      ],
      columns: [
        { data: "no" },
        { data: "device_sn" },
        { data: "point_name" },
        { data: "id" },
      ]
    });

    $(".select2").select2();
    $('#form-search').submit(function(e){
      e.preventDefault();
      dataTable.draw();
      $('#add-filter').modal('hide');
    });
    $(document).on('click', '.delete', function(){
      var id = $(this).data('id');
      bootbox.confirm({
        buttons: {
          confirm: {
            label: '<i class="fa fa-check"></i>',
            className: 'btn-{{ config("configs.app_theme") }}'
          },
          cancel: {
            label: '<i class="fa fa-undo"></i>',
            className: 'btn-default'
          },
        },
        title: 'Delete Attendance Machine?',
        message: 'Data that has been deleted cannot be recovered',
        callback: function(result){
          if (result) {
            var data = {
              _token: "{{ csrf_token() }}"
            };
            $.ajax({
              url: `{{ url('admin/attendancemachine') }}/${id}`,
              dataType: "JSON",
              data: data,
              type: "DELETE",
              beforeSend: function() {
                $('.overlay').removeClass('d-none');
              }
            }).done(function(response){
              if (response.status) {
                $('.overlay').addClass('d-none');
                $.gritter.add({
                  title: 'Success!',
                  text: response.message,
                  class_name: 'gritter-success',
                  time: 1000,
                });
                dataTable.ajax.reload(null, false);
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
</script>
@endpush