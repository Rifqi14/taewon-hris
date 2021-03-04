@extends('admin.layouts.app')
@section('title', 'Create Daily Report Driver')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{ route('dailyreportdriver.index') }}">Daily Report Driver</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush

@section('content')
<form id="form" action="{{ route('dailyreportdriver.store') }}" class="form-horizontal" method="post" autocomplete="off">
  <div class="row">
    <div class="col-lg-12">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">Personal Data </h3>
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Simpan"><i
                class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                class="fa fa-reply"></i></a>
          </div>
        </div>
        <div class="card-body">
          {{ csrf_field() }}
          <div class="form-row">
            <div class="form-group col-sm-6">
              <div class="row">
                <label class="col-sm-3 label-controls" for="code">Code</label>
                <div class="col-sm-8 controls">
                  <input type="text" class="form-control" id="code" name="code" data-placeholder="Select Driver">
                </div>
              </div>
            </div>
            <div class="form-group col-sm-6">
              <div class="row">
                <label class="col-sm-3 label-controls" for="date">Date</label>
                <div class="col-sm-8 controls">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                      </span>
                    </div>
                    <input type="text" name="date" class="form-control datepicker" placeholder="Date" required />
                  </div>
                </div>
              </div>
            </div>
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
                  <input type="text" id="police_no" name="police_no" class="form-control" placeholder="Police No" required />
                </div>
              </div>
            </div>
            <div class="form-group col-sm-6">
              <div class="row">
                <label class="col-sm-3 label-controls" for="exp_passenger">Expedition/ Passenger</label>
                <div class="col-sm-8 controls">
                  <input type="text" id="exp_passenger" name="exp_passenger" class="form-control" placeholder="Expedition/ Passenger" required />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-12 ">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">List Calculation</h3>
        </div>
        <div class="card-body">
          <table class="table table-striped table-bordered datatable" id="table-product" style="width: 100%">
            <thead>
              <tr>
                <th width="10">No</th>
                <th width="130">Destination</th>
                <th class="text-center" width="100">Departure</th>
                <th class="text-center" width="100">Arrival</th>
                <th class="text-right" width="100">Parking</th>
                <th class="text-right" width="100">Toll Money</th>
                <th class="text-right" width="100">Fuel</th>
                <th class="text-right" width="100">Etc</th>
                <th class="text-right" width="100">Total</th>
                <th width="10">Action</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
            <input type="hidden" name="subtotal" class="form-control form-control-sm currency text-right"  value="0">
            <input type="hidden" name="subtotaladditional" class="form-control form-control-sm currency text-right"  value="0">
            <input type="hidden" name="grandtotal" class="form-control form-control-sm currency text-right"  value="0">
            <tfoot>
              <tr>
                <th class="text-right" colspan="8">
                  Sub Total
                </th>
                <td class="text-right" data-subtotal>
                  0
                </td>
                <td></td>
              </tr>
              <tr>
                <td class="" colspan="9">
                </td>
                <td class="text-right">
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
    </div>
    <div class="col-lg-12 ">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">List Additional</h3>
        </div>
        <div class="card-body">
          <table class="table table-striped table-bordered datatable" id="table-additional" style="width: 100%">
            <thead>
              <tr>
                <th width="10">No</th>
                <th width="200">Additional</th>
                <th class="text-right" width="100">Total</th>
                <th class="text-center" width="5">Action</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                <th class="text-right" colspan="2">
                  Sub Total
                </th>
                <td class="text-right" data-subtotaladditional="0">
                  0
                </td>
                <td></td>
              </tr>
              <tr>
                <th class="text-right" colspan="2">
                  Grand Total
                </th>
                <td class="text-right" data-grandtotal="0">
                  0
                </td>
                <td></td>
              </tr>
              <tr>
                <td class="" colspan="3">
                </td>
                <td class="text-center">
                  <a onclick="addAdditional()" style="color: white;" class="btn btn-sm btn-primary" title="Add"><i class="fas fa-plus-square"></i></a>
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
        <div class="overlay d-none">
          <i class="fa fa-2x fa-sync-alt fa-spin"></i>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/jquery-mask/jquery.mask.min.js')}}"></script>
<script src="{{asset('js/accounting/accounting.min.js')}}"></script>
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
    $('#table-product').on('click','.remove',function(){
    $(this).parents('tr').remove();
    resetCount();
    });
    $('#table-additional').on('click','.remove',function(){
    $(this).parents('tr').remove();
    resetCount();
    });
    $('.datepicker').daterangepicker({
      singleDatePicker: true,
      timePicker: false,
      timePickerIncrement: 1,
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
    });
    });
    function addproduct() {
      var length = $('#table-product tr').length - 2;
      var last_tr = $('#table-product tbody tr:last-child').find('input[name="arrival_km[]"]').val()?$('#table-product tbody tr:last-child').find('input[name="arrival_km[]"]').val():0;
      var html = `<tr>
                <td class="text-center">
                  ${length}
                </td>
                <td class="p-1 text-center align-middle">
                  <div class="form-group mb-0">
                    <input type="hidden" name="product_item[]" />
                    <input placeholder="Destination" name="destination[]" class="form-control form-control-sm" required />
                  </div>
                </td>
                <td class="p-1 text-center align-middle">
                  <input placeholder="Departure" name="departure[]" class="form-control form-control-sm text-center" required/>
                  <div class="input-group input-group-sm mb-0">
                    <div class="input-group-prepend">
                      <span class="input-group-text">Km</span>
                    </div>
                    <input placeholder="Value" name="departure_km[]" class="form-control form-control-sm currency text-right" required value="${last_tr}">
                  </div>
                </td>
                <td class="p-1 text-right align-middle">
                    <input placeholder="Arrival" name="arrival[]" class="form-control form-control-sm text-center numberfield" required />
                    <div class="input-group input-group-sm mb-0">
                    <div class="input-group-prepend">
                      <span class="input-group-text">Km</span>
                    </div>
                    <input placeholder="Value" name="arrival_km[]" class="form-control form-control-sm currency text-right" required value="0">
                  </div>
                </td>
                <td class="p-1 text-center align-middle">
                  <div class="input-group input-group-sm mb-0">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Rp.</span>
                  </div>
                  <input placeholder="Value" name="parking[]" class="form-control form-control-sm currency text-right onCalculation" required  value="0">
                </div>
                </td>
                <td class="p-1 text-center align-middle">
                  <div class="input-group input-group-sm mb-0">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Rp.</span>
                  </div>
                  <input placeholder="Value" name="toll_money[]" class="form-control form-control-sm currency text-right onCalculation" required  value="0">
                </div>
                <td class="p-1 text-center align-middle">
                  <div class="input-group input-group-sm mb-0">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Rp.</span>
                  </div>
                  <input placeholder="Value" name="oil[]" class="form-control form-control-sm currency text-right onCalculation" required  value="0">
                </div>
                </td>
                <td class="p-1 text-center align-middle">
                  <div class="input-group input-group-sm mb-0">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Rp.</span>
                  </div>
                  <input placeholder="Value" name="etc[]" class="form-control form-control-sm currency text-right onCalculation" required  value="0">
                </div>
                </td>
                <td class="p-1 text-center align-middle">
                  <div></div>
                  <div class="input-group input-group-sm mb-0">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Rp.</span>
                  </div>
                  <input placeholder="Value" name="total[]" class="form-control form-control-sm currency text-right" required readonly value="0">
                </div>
                </td>
                <td class="text-center">
                  <a style="color: white;" class="btn btn-sm btn-danger remove" title="Remove"><i class="fa fa-minus-circle"></i></a>
                </td>
              </tr>`;
    $('#table-product').append(html);
    $('input[name="departure[]"]').daterangepicker({
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
    $('input[name="arrival[]"]').daterangepicker({
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
    }
    function addAdditional() {
      var length = $('#table-additional tr').length - 2;
      var number = $('#table-additional tr').length;
      var html = `<tr>
                <td class="text-center">
                  ${length}
                </td>
                <td class="p-1 text-center align-middle">
                  <div class="form-group mb-0">
                    <input type="hidden" name="product_additional[]" />
                    <input placeholder="Additional" name="additional_name[]" class="form-control form-control-sm" required />
                  </div>
                </td>
                <td class="p-1 text-center align-middle">
                  <div class="input-group input-group-sm mb-0">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Rp.</span>
                  </div>
                  <input placeholder="Value" name="additional_total[]" class="form-control form-control-sm currency text-right onCalculation" required>
                </div>
                </td>
                <td class="text-center">
                  <a style="color: white;" class="btn btn-sm btn-danger remove" title="Remove"><i class="fa fa-minus-circle"></i></a>
                </td>
              </tr>`;
      $('#table-additional').append($(html));
    }
    $(document).on('keyup',".onCalculation",function(){
      var parking = $(this).closest('tbody tr').find('input[name="parking[]"]').val() * 1;
      var toll_money = $(this).closest('tbody tr').find('input[name="toll_money[]"]').val() * 1;
      var oil = $(this).closest('tbody tr').find('input[name="oil[]"]').val() * 1;
      var etc = $(this).closest('tbody tr').find('input[name="etc[]"]').val() * 1;
      var total = parking + toll_money + oil + etc;
      $(this).closest('tbody tr').find('input[name="total[]"]').val(total);
      resetCount();
    });
    
    function resetCount()
    {

      var subtotal		= 0;
      var subtotalAdditional		= 0;
      $('#table-product > tbody  > tr').each(function(index, tr) { 
        subtotal   	+= $(tr).find('input[name="total[]"]').val() * 1;
      });
      $('#table-product > tfoot').find('td[data-subtotal]').html(accounting.formatMoney(subtotal, '', ',', '.'));
      $("input[name='subtotal']").val(subtotal);

      {{-- Additional --}}
      $('#table-additional > tbody  > tr').each(function(index, tr) { 
        subtotalAdditional   	+= $(tr).find('input[name="additional_total[]"]').val() * 1;
      });
      $('#table-additional > tfoot').find('td[data-subtotaladditional]').html(accounting.formatMoney(subtotalAdditional, '', ',', '.'));
      $("input[name='subtotaladditional']").val(subtotalAdditional);

      grandTotal = subtotal + subtotalAdditional;
      $('#table-additional > tfoot').find('td[data-grandtotal]').html(accounting.formatMoney(grandTotal, '', ',', '.'));
      $("input[name='grandtotal']").val(grandTotal);
    }
</script>
@endpush