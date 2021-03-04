@extends('admin.layouts.app')
@section('title', 'Master Reimbursement')
@section('stylesheets')
<link href="{{ asset('adminlte/component/dataTables/css/datatables.min.css') }}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet">
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

    .customcheckbox input {
        cursor: pointer;
        opacity: 0;
        scale: 1.6;
        width: 22px;
        height: 22px;
        margin: 0;
    }

    .ui-state-active{
        background: #28a745 !important;
        border-color: #28a745 !important;
    }
    .ui-menu {
        overflow: auto;
        height:200px;
    }

</style>
@endsection
@push('breadcrump')
<li class="breadcrumb-item active">Master Reimbursement</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <form id="form" class="form-horizontal" method="post">
          {{ csrf_field() }}
          <div class="card-header">
            <h3 class="card-title">Reimbursement List</h3>
            <div class="pull-right card-tools">
              <a href="{{route('reimbursement.create')}}" class="btn btn-{{ config('configs.app_theme') }} btn-sm text-white" data-toggle="tooltip" title="Add Data"><i class="fa fa-plus"></i></a>
              <a href="#" onclick="exportreimbursment()" class="btn btn-primary btn-sm text-white"><i class="fa fa-download"></i></a>
              <a href="javascript:void(0)" onclick="printpreview()" class="btn btn-sm btn-info text-white" title="Print Mass"><i class="fa fa-print"></i></a>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="driver_id">Driver Name</label>
                  <input type="text" class="form-control filter" id="driver_id" name="driver_id" placeholder="Driver Name">
                  {{-- <select name="driver_id" id="driver_id" class="form-control select2 filter" style="width: 100%" aria-hidden="true" data-placeholder="Driver Name">
                      <option value=""></option>
                      @foreach ($employees as $employee)
                      <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                      @endforeach
                  </select> --}}
                </div>
                <div class="driver-container"></div>
              </div>
              <div class="form-row col-md-6">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="date_from">From</label>
                    <div class="controls">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                          </span>
                        </div>
                        <input type="text" name="date_from" id="date_from" class="form-control datepicker filter" placeholder="Date">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="date_to">To</label>
                    <div class="controls">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                          </span>
                        </div>
                        <input type="text" name="date_to" id="date_to" class="form-control datepicker filter" placeholder="Date">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <table class="table table-striped table-bordered datatable" style="width: 100%">
              <thead>
                <tr>
                  <th width="10">No</th>
                  <th width="100">Date</th>
                  <th width="100">Driver</th>
                  <th width="100">Notes</th>
                  <th width="100" class="text-right">Total</th>
                  <th width="10">Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </form>
        <div class="overlay d-none">
          <i class="fa fa-2x fa-sync-alt fa-spin"></i>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="print-mass" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header no-print">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">Print</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <iframe id="bodyReplace" scrolling="no" allowtransparency="true"
                        style="width: 69%; border-width: 0px; position: relative; margin: 0 auto; display: block;"
                        onload="this.style.height=(this.contentDocument.body.scrollHeight+45) + 'px';"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('js/accounting/accounting.min.js')}}"></script>
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootbox/bootbox.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/jquery-ui/jquery-ui.min.js')}}"></script>
<script type="text/javascript">
  function filter() {
    $('#add-filter').modal('show');
  }
  function printpreview() {
    $('.overlay').removeClass('d-none');
    $.ajax({
        url: "{{ route('reimbursement.print') }}",
        method: 'GET',
        data: {
          date_from: $('input[name=date_from]').val(),
          date_to: $('input[name=date_to]').val()
        },
        success: function (response) {
            $('.overlay').addClass('d-none');
            dataTable.draw();
            $('.customcheckbox').removeClass('checked');
            $('.customcheckbox input').prop('checked', false);
            var iframe = document.getElementById('bodyReplace');
            iframe = iframe.contentWindow || (iframe.contentDocument.document || iframe
                .contentDocument);
            iframe.document.open();
            iframe.document.write(response);
            iframe.document.close();
        },
        error: function (response) {
          $('.overlay').addClass('d-none');
          var response = response.responseJSON;
          $.gritter.add({
              title: 'Error!',
              text: response.message,
              class_name: 'gritter-error',
              time: 1000,
          });
        }
    });
  }
  function exportreimbursment() {
    $.ajax({
        url: "{{ route('reimbursement.exportreimbursment') }}",
        type: 'POST',
        dataType: 'JSON',
        data: $("#form").serialize(),
        beforeSend: function () {
            // $('.overlay').removeClass('d-none');
            waitingDialog.show('Loading...');
        }
    }).done(function (response) {
      waitingDialog.hide();
        if (response.status) {
            $('.overlay').addClass('d-none');
            $.gritter.add({
                title: 'Success!',
                text: response.message,
                class_name: 'gritter-success',
                time: 1000,
            });
            let download = document.createElement("a");
            download.href = response.file;
            document.body.appendChild(download);
            download.download = response.name;
            download.click();
            download.remove();
        } else {
            $.gritter.add({
                title: 'Warning!',
                text: response.message,
                class_name: 'gritter-warning',
                time: 1000,
            });
        }
    }).fail(function (response) {
      waitingDialog.hide();
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
  $(function() {
    $('.select2').select2({
      allowClear: true
    });
    $('.datepicker').daterangepicker({
      singleDatePicker: true,
      timePicker: false,
      timePickerIncrement: 1,
      locale: {
      format: 'DD/MM/YYYY'
      }
    });
    $('#form-search').submit(function(e){
			e.preventDefault();
			dataTable.draw();
			$('#add-filter').modal('hide');
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
      ajax: {
          url: "{{route('reimbursement.read')}}",
          type: "GET",
          data:function(data){
            data.driver_id = $('input[name=driver_id]').val();
            data.date_from = $('input[name=date_from]').val();
            data.date_to   = $('input[name=date_to]').val();
          }
      },
      columnDefs: [
        {orderable: false, targets: [0,5]},
        {className: "text-center", targets: [0,5]},
        {className: "text-right", targets: [4]},
        { render: function ( data, type, row ) {
                        var grandtotal = accounting.formatMoney(data,'',',','.');
                        return `${grandtotal}`
				},targets: [4]
				},
        { render: function ( data, type, row ) {
          return `<div class="dropdown">
                      <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                          <i class="fa fa-bars"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-right">
                          <li><a class="dropdown-item edit" href="#" data-id="${row.id}"><i class="fas fa-pencil-alt mr-2"></i> Edit</a></li>
                          <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> Delete</a></li>
                      </ul>
                  </div>`
          },targets: [5]
        }
      ],
      columns: [
        { data: "no" },
        { data: "date" },
        { data: "driver_name" },
        { data: "notes" },
        { data: "grandtotal" },
        { data: "id" },
      ]
    });
    $(document).ready(function(){
      var employees = [
            @foreach($employees as $employee)
                "{!!$employee->name!!}",
            @endforeach
        ];
            $( "input[name=driver_id]" ).autocomplete({
        source: employees,
        minLength:0,
        appendTo: '#driver-container',
        select: function(event, response) {
            if(event.preventDefault(), 0 !== response.item.id){
                $(this).val(response.item.value);
                dataTable.draw();
            }
        }
        }).focus(function () {
            $(this).autocomplete("search");
        });
        $("input[name=driver_id]").keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                $('input[name=driver_id]').autocomplete('close');
                return false;
            }
        });
    $(document).on('change keyup keydown keypress focus', '.filter', function() {
      dataTable.draw();
    });
    });
    $(document).on('click', '.customcheckbox input', function () {
        if ($(this).is(':checked')) {
            $(this).parent().addClass('checked');
        } else {
            $(this).parent().removeClass('checked');
        }
    });
    $(document).on('change', '.checkall', function () {
        if (this.checked) {
            $('input[name^=checksalary]').prop('checked', true);
            $('input[name^=checksalary]').parent().addClass('checked');
        } else {
            $('input[name^=checksalary]').prop('checked', false);
            $('input[name^=checksalary]').parent().removeClass('checked');
        }
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
        title:'Delete driver allowance?',
        message:'Data that has been deleted cannot be recovered',
        callback: function(result) {
            if(result) {
              var data = { _token: "{{ csrf_token() }}" };
              $.ajax({
                url: `{{url('admin/reimbursement')}}/${id}`,
                dataType: 'json',
                data:data,
                type:'DELETE',
                beforeSend:function(){
                  $('.overlay').removeClass('hidden');
                }
              }).done(function(response){
                if(response.status){
                  $('.overlay').addClass('hidden');
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
                  $('.overlay').addClass('hidden');
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
    $(document).on('click','.edit',function(){
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
        title:'Edit Reimbursement?',
        message:'You will be redirect to reimbursement edit page, are you sure?',
        callback: function(result) {
            if(result) {
              document.location = "{{url('admin/reimbursement')}}/"+id+"/edit";
            }
        }
      });
    });
  });
</script>
@endpush