@extends('admin.layouts.app')
@section('title', 'Edit Delivery Order')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{ route('deliveryorder.index') }}">Delivery Order</a></li>
<li class="breadcrumb-item active">edit</li>
@endpush

@section('content')
<form id="form" action="{{ route('deliveryorder.update', ['id'=>$deliveryorder->id]) }}" class="form-horizontal" method="post" autocomplete="off">
  <div class="row">
    <div class="col-lg-12">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">List Delivery Order</h3>
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Simpan"><i class="fa fa-save"></i></button>
            <a href="javascript:void(0)" onclick="backurl()" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
          </div>
        </div>
        <div class="card-body">
          {{ csrf_field() }}
          {{ method_field('put') }}
          <div class="form-row">
            {{-- <div class="form-group col-sm-6">
              <div class="row">
                <label class="col-sm-3 label-controls" for="date">Date</label>
                <div class="col-sm-8 controls">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                      </span>
                    </div>
                    <input type="text" name="date" class="form-control datepicker" placeholder="Date" required value="{{ date('d/m/Y H:i:s', strtotime($deliveryorder->date)) }}"/>
                  </div>
                </div>
              </div>
            </div> --}}
            <div class="form-group col-sm-6">
              <div class="row">
                <label class="col-sm-3 label-controls" for="type_truck">Type Truck</label>
                <div class="col-sm-8 controls">
                  <select name="type_truck" id="type_truck" class="form-control select2" style="width: 100%" required>
                    <option value="Fuso" @if ($deliveryorder->type_truck == 'Fuso') selected @endif>Fuso</option>
                    <option value="Colt Diesel" @if ($deliveryorder->type_truck == 'Colt Diesel') selected @endif>Colt Diesel</option>
                  </select>
                </div>
              </div>
            </div>
            {{-- <div class="form-group col-sm-6">
              <div class="row">
                <label class="col-sm-3 label-controls" for="date">D.O Number</label>
                <div class="col-sm-8 controls">
                  <input type="text" name="do_number" class="form-control" placeholder="D.O Number" required value="{{ $deliveryorder->do_number }}"/>
                </div>
              </div>
            </div> --}}
            <div class="form-group col-sm-6">
              <div class="row">
                <label class="col-sm-3 label-controls" for="driver_id">Driver Name</label>
                <div class="col-sm-8 controls">
                  <input type="text" class="form-control" id="driver_id" name="driver_id" data-placeholder="Select Driver">
                </div>
              </div>
            </div>
            <div class="form-group col-sm-6">
              <div class="row">
                <label class="col-sm-3 label-controls" for="date">Police No</label>
                <div class="col-sm-8 controls">
                  <input type="text" id="police_no" name="police_no" class="form-control" placeholder="Police No" required value="{{ $deliveryorder->police_no }}"/>
                </div>
              </div>
            </div>
            <div class="form-group col-sm-6">
              <div class="row">
                <label class="col-sm-3 label-controls" for="customer">Customer</label>
                <div class="col-sm-8 controls">
                  <input type="text" id="customer" name="customer" class="form-control" placeholder="Customer"/>
                </div>
              </div>
            </div>
            <div class="form-group col-sm-6">
              <div class="row">
                <label class="col-sm-3 label-controls" for="departure_time">Departure Time</label>
                <div class="col-sm-8 controls">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                      </span>
                    </div>
                    <input type="text" name="departure_time" class="form-control datepicker" placeholder="Departure Time" required value="{{ date('d/m/Y H:i:s', strtotime($deliveryorder->departure_time)) }}" />
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group col-sm-6">
              <div class="row">
                <label class="col-sm-3 label-controls" for="arrived_time">Arrived Time</label>
                <div class="col-sm-8 controls">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                      </span>
                    </div>
                    <input type="text" name="arrived_time" class="form-control datepicker" placeholder="Arrived Time" required value="{{ date('d/m/Y H:i:s', strtotime($deliveryorder->arrived_time)) }}" />
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group col-sm-6">
              <div class="row">
                <label class="col-sm-3 label-controls" for="kloter">Kloter</label>
                <div class="col-sm-8 controls">
                  <input type="number" id="kloter" name="kloter" class="form-control" placeholder="Kloter" required value="{{ $deliveryorder->group }}"/>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    {{-- <div class="col-lg-12 ">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">List Item</h3>
        </div>
        <div class="card-body">
          <table class="table table-striped table-bordered datatable" id="table-product" style="width: 100%">
            <thead>
              <tr>
                <th width="10">PO Number</th>
                <th width="100">Item</th>
                <th class="text-right" width="50">Size</th>
                <th class="text-right" width="50">Qty</th>
                <th width="120">Remarks</th>
                <th width="10">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($deliveryorder->deliveryorderdetail as $key => $item)
              <tr>
                <td class="text-center align-middle">
                  <div class="form-group mb-0">
                    <input type="hidden" name="product_item[]" />
                    <input placeholder="PO Number" name="po_number[]" class="form-control" required value="{{$item->po_number}}"/>
                  </div>
                </td>
                <td class="text-center align-middle">
                  <input placeholder="Item" name="item_name[]" class="form-control" required value="{{$item->item_name}}"/>
                </td>
                <td class="text-right align-middle">
                    <input placeholder="Size" name="size[]" class="form-control text-right numberfield" required value="{{$item->size}}"/>
                </td>
                <td class="text-right">
                  <input type="text" class="form-control text-right numberfield" placeholder="Qty" name="qty[]" required value="{{$item->qty}}"/>
                </td>
                <td class="">
                  <input placeholder="Remarks" id="remarks[]" name="remarks[]" class="form-control"/value="{{$item->remarks}}">
                </td>
                <td class="text-center">
                  <a style="color: white;" class="btn btn-sm btn-danger remove" title="Remove"><i class="fa fa-minus-circle"></i></a>
                </td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td class="" colspan="5">
                </td>
                <td class="text-center">
                  <a onclick="addproduct()" style="color: white;" class="btn btn-sm btn-primary" title="Add"><i class="fas fa-plus-square"></i></a>
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
        <div class="overlay d-none">
          <i class="fa fa-2x fa-sync-alt fa-spin"></i>
        </div>
      </div>
    </div> --}}
  </div>
</form>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/jquery-mask/jquery.mask.min.js')}}"></script>
<script>
  $(document).ready(function(){
    $('.select2').select2();
    $('.rupiah').mask('000.000.000.000.000.000', {reverse: true});
    $("#driver_id" ).select2({
      ajax: {
        url: "{{route('employees.select')}}",
        type:'GET',
        dataType: 'json',
        data: function (term,page) {
          return {
            path:'Driver',
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
              text: item.name,
            });
          });
          return {
            results: option, more: more,
          };
        },
      },
      allowClear: true,
    });
    @if($deliveryorder->driver_id)
      $("#driver_id").select2('data',{id:{{$deliveryorder->driver_id}},text:'{{$deliveryorder->driver->name}}'}).trigger('change');
    @endif
    $("#customer" ).select2({
      ajax: {
        url: "{{route('partner.select')}}",
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
              text: item.name,
            });
          });
          return {
            results: option, more: more,
          };
        },
      },
      allowClear: true,
    });
    @if($deliveryorder->partner_id)
      $("#customer").select2('data',{id:{{$deliveryorder->partner_id}},text:'{{$deliveryorder->partner->name}}'}).trigger('change');
    @endif
    $('#table-product').on('click','.remove',function(){
    $(this).parents('tr').remove();
    });
    $('.datepicker').daterangepicker({
      singleDatePicker: true,
      timePicker: true,
      timePickerIncrement: 1,
      locale: {
      format: 'DD/MM/YYYY HH:mm:ss'
      }
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
          title:'Save the update?',
          message:'Are you sure to save the changes?',
          callback: function(result) {
            if(result) {
              $.ajax({
                url:$('#form').attr('action'),
                method:'post',
                data: new FormData($('#form')[0]),
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend:function(){
                  $('.overlay').removeClass('hidden');
                }
              }).done(function(response){
                  $('.overlay').addClass('hidden');
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
                $('.overlay').addClass('hidden');
                var response = response.responseJSON;
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
      }
    });
    });
    function addproduct() {
      var length = $('#table-product tr').length;
      var html = `<tr>
                <td class="text-center align-middle">
                  <div class="form-group mb-0">
                    <input type="hidden" name="product_item[]" />
                    <input placeholder="PO Number" name="po_number[]" class="form-control" required />
                  </div>
                </td>
                <td class="text-center align-middle">
                  <input placeholder="Item" name="item_name[]" class="form-control" required />
                </td>
                <td class="text-right align-middle">
                    <input placeholder="Size" name="size[]" class="form-control text-right numberfield" required />
                </td>
                <td class="text-right">
                  <input type="text" class="form-control text-right numberfield" placeholder="Qty" name="qty[]" required />
                </td>
                <td class="">
                  <input placeholder="Remarks" id="remarks[]" name="remarks[]" class="form-control"/>
                </td>
                <td class="text-center">
                  <a style="color: white;" class="btn btn-sm btn-danger remove" title="Remove"><i class="fa fa-minus-circle"></i></a>
                </td>
              </tr>`;
      $('#table-product').append($(html));
    }
</script>
@endpush