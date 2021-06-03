@extends('admin.layouts.app')
@section('title',__('deliveryorder.do'))
@section('stylesheets')
<link href="{{ asset('adminlte/component/dataTables/css/datatables.min.css') }}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet">
<style type="text/css">
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
<li class="breadcrumb-item active">{{__('deliveryorder.do')}}</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">{{__('deliveryorder.dolist')}}</h3>
          <div class="pull-right card-tools">
            <a href="{{route('deliveryorder.import')}}" class="btn btn-{{ config('configs.app_theme') }} btn-sm" data-toggle="tooltip" title="{{__('general.imp')}}" style="cursor: pointer;"><i class="fa fa-file-import"></i></a>
            <a href="{{route('deliveryorder.create')}}"
              class="btn btn-{{ config('configs.app_theme') }} btn-sm text-white" data-toggle="tooltip"
              title="{{__('general.crt')}}">
              <i class="fa fa-plus"></i>
            </a>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="driver_id">{{__('deliveryorder.drivernm')}}</label>
                  <input type="text" name="driver_id" id="driver_id" class="form-control filter" placeholder="{{__('deliveryorder.drivernm')}}">
                  {{-- <select name="driver_id" id="driver_id" class="form-control select2 filter" style="width: 100%" aria-hidden="true"  data-placeholder="Driver Name">
                      <option value=""></option>
                      @foreach ($employees as $employee)
                      <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                      @endforeach
                  </select> --}}
                </div>
				        <div id="driver-container"></div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="police_no">{{__('deliveryorfer.policeno')}}</label>
                  {{-- <input type="text" name="police_no" id="police_no" class="form-control filter" placeholder="Police No" multiple> --}}
                  <select name="police_no" id="police_no" class="form-control select2 filter" style="width: 100%" aria-hidden="true" multiple  data-placeholder="{{__('deliveryorfer.policeno')}}">
                    <option value=""></option>
                    @foreach ($police_nomer as $police)
                    <option value="{{ $police->police_no }}">{{ $police->police_no }}</option>
                    @endforeach
                </select>
                </div>
              </div>
              <div class="form-row col-md-4">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="date_from">{{__('deliveryorder.from')}}</label>
                    <div class="controls">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                          </span>
                        </div>
                        <input type="text" name="date_from" id="date_from" class="form-control filter" placeholder="{{__('general.date')}}">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="date_to">{{__('deliveryorder.to')}}</label>
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
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="destination">{{__('customer.cust')}}</label>
                  {{-- <input type="text" name="destination" id="destination" class="form-control filter" placeholder="Destination"> --}}
                  {{-- <select name="destination" id="destination" class="form-control select2 filter" style="width: 100%" aria-hidden="true" multiple data-placeholder="Customer">
                    <option value=""></option>
                    @foreach ($desti as $tujuan)
                    <option value="{{ $tujuan->destination }}">{{ $tujuan->destination }}</option>
                    @endforeach
                </select> --}}
                </div>
              </div>
              {{-- <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="do_number">DO Number</label>
                  <select name="do_number" id="do_number" class="form-control select2 filter" style="width: 100%" aria-hidden="true" multiple data-placeholder="DO Number">
                      <option value=""></option>
                      @foreach ($donumbers as $donumber)
                      <option value="{{ $donumber->do_number }}">{{ $donumber->do_number }}</option>
                      @endforeach
                  </select>
                </div>
              </div> --}}
            </div>
          <table class="table table-striped table-bordered datatable" style="width: 100%">
            <thead>
              <tr>
                <th width="10">No</th>
                <th width="70">{{__('deliveryorder.deptime')}}</th>
                <th width="100">{{__('deliveryorder.arrtime')}}</th>
                <th width="100">Kloter</th>
                <th width="130" class="text-left">{{__('deliveryorder.driver')}}</th>
                <th width="100">{{__('deliveryorfer.policeno')}}</th>
                <th width="100">{{__('customer.cust')}}</th>
                <th width="10">{{__('general.act')}}</th>
              </tr>
            </thead>
          </table>
        </div>
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
        <h4 class="modal-title">{{__('general.print')}}</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <iframe id="bodyReplace" scrolling="no" allowtransparency="true" style="width: 69%; border-width: 0px; position: relative; margin: 0 auto; display: block;" onload="this.style.height=(this.contentDocument.body.scrollHeight+45) + 'px';"></iframe>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/jquery-ui/jquery-ui.min.js')}}"></script>
<script type="text/javascript">
  const eventPrint = (me) => {
    let id = $(me).data('id');
    $.ajax({
      url: "{{ url('admin/deliveryorder/print') }}/" + id,
      method: 'GET',
      beforeSend: () => {
        $('.overlay').removeClass('d-none');
      },
      success: (response) => {
        $('.overlay').addClass('d-none');
        let iframe = document.getElementById('bodyReplace');
            iframe = iframe.contentWindow || (iframe.contentDocument.document || iframe.contentDocument);
            iframe.document.open();
            iframe.document.write(response);
            iframe.document.close();
      }
    });
  }
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
		});
  $(function() {
    $('.select2').select2({
      allowClear: true
    });
    $('#date_from').daterangepicker({
      autoUpdateInput: false,
      singleDatePicker: true,
      timePicker: false,
      timePickerIncrement: 30,
      locale: {
      format: 'DD/MM/YYYY'
      }
    }, function(chosen_date) {
      $('#date_from').val(chosen_date.format('DD/MM/YYYY'));
      dataTable.draw();
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
      language: {
            lengthMenu: `{{ __('general.showent') }}`,
            processing: `{{ __('general.process') }}`,
            paginate: {
                previous: `{{ __('general.prev') }}`,
                next: `{{ __('general.next') }}`,
            }
        },
      lengthMenu: [ 100, 250, 500, 1000, 2000 ],
      pageLength: 1000,
      ajax: {
          url: "{{route('deliveryorder.read')}}",
          type: "GET",
          data:function(data){
            data.driver_id = $('input[name=driver_id]').val();
            data.police_no = $('select[name=police_no]').val();
            data.date_from = $('input[name=date_from]').val();
            data.destination = $('select[name=destination]').val();
            data.do_number = $('select[name=do_number]').val();
            data.date_to = $('input[name=date_to]').val();
          }
      },
      columnDefs: [
        {orderable: false, targets: [0,5]},
        {className: "text-center", targets: [3]},
        {className: "text-left", targets: [4]},
        { render: function(data, type, row) {
            return `${row.departure_date}<br><small>${row.departure_time}</small>`;
        }, targets:[1]},
        { render: function(data, type, row) {
            return `${row.arrived_date}<br><small>${row.arrived_time}</small>`;
        }, targets:[2]},
        { render: function(data, type, row) {
            return `<a class="edit" data-id="${row.id}" href="#">${row.driver_name}</a><br><small>${row.nid}</small>`;
        }, targets:[4]},
        { render: function(data, type, row) {
           
              return `${row.truck_name} <br><small>${row.police_no}</small>`
          
            // return `${row.type_truck}<br><small>${row.police_no}</small>`;
        }, targets:[5]},
        { render: function(data, type, row) {
            return `${row.customer}<br><small> RIT : ${row.rit}</small>`;
        }, targets:[6]},
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
          },targets: [7]
        }
      ],
      columns: [
        { data: "no" },
        { data: "departure_time" },
        { data: "arrived_time" },
        { data: "group" },
        { data: "driver_name" },
        { data: "police_no" },
        { data: "customer" },
        { data: "id" },
      ]
    });
    $(document).on('change keyup keydown keypress focus', '.filter', function() {
      dataTable.draw();
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
                url: `{{url('admin/deliveryorder')}}/${id}`,
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
        title:'Edit Delivery Order?',
        message:'You will be redirect to delivery order edit page, are you sure?',
        callback: function(result) {
            if(result) {
              document.location = "{{url('admin/deliveryorder')}}/"+id+"/edit";
            }
        }
      });
    });
  });
</script>
@endpush