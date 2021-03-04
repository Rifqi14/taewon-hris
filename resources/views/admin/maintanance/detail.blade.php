@extends('admin.layouts.app')
@section('title', 'Detail Maintenance')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{ route('maintenance.index') }}">Maintenance</a></li>
<li class="breadcrumb-item active">Detail</li>
@endpush

@section('content')
  <div class="row">
    <div class="col-lg-12">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">List Maintenance</h3>
           <div class="pull-right card-tools">
          </div>
        </div>
        <div class="card-body">
          {{ csrf_field() }}
            <div class="d-flex">
              <div class="form-group col-sm-4">
                  <label class="col-sm-3 label-controls" for="type_truck">Vehicle <b class="text-danger">*</b></label>
                    <input type="text" name="vehicle_id" id="vehicle_id" class="form-control" placeholder="Vehicle" required readonly>
                    <input type="hidden" name="vehicle_name" id="vehicle_name" class="form-control" readonly required>
              </div>
              <div class="form-group col-sm-4">
                  <label class="col-sm-5" for="vendor">Vehicle Category</label>
              <input type="text" name="vehicle_category" id="vehicle_category" class="form-control" placeholder="Category" required  readonly value="{{$maintanance->vehicle_category}}"/>
              </div>
              
              <div class="form-group col-sm-4">
                  <label class="col-sm-3" for="vendor">License No</label>
              <input type="text" name="vehicle_no" id="vehicle_no" class="form-control" placeholder="License No" required readonly value="{{$maintanance->vehicle_no}}"/>
              </div>
            </div>
            <div class="d-flex">
                <div class="form-group col-sm-4">
                    <label class="col-sm-3 label-controls" for="license_no">Vendor</label>
                      <input type="text" name="vendor" id="vendor" class="form-control" placeholder="Vendor" required readonly value="{{$maintanance->vendor }}">
                </div>
                
                <div class="form-group col-sm-4">
                    <label class="col-sm-3 label-controls" for="license_no">Driver <b class="text-danger">*</b></label>
                      <input type="text" name="driver" id="driver" class="form-control" placeholder="Driver" required readonly  value="{{$maintanance->driver}}">
                </div>
                <div class="form-group col-sm-4">
                    <label class="col-sm-5 label-controls" for="license_no">Technician <b class="text-danger">*</b></label>
                      <input type="text" name="technician" id="technician" class="form-control" placeholder="Technician" required readonly value="{{$maintanance->technician }}">
                </div>
                
            </div>
            <div class="d-flex">
              <div class="form-group col-sm-4">
                    <label class="col-sm-3 label-controls" for="date">Date <b class="text-danger">*</b></label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                          </span>
                        </div>
                      <input type="text" name="date" class="form-control datepicker" placeholder="Date" required readonly value="{{$maintanance->date}}" />
                      </div>
                </div>
                <div class="form-group col-sm-4">
                    <label class="col-sm-3" for="date">Km <b class="text-danger">*</b></label>
                <input type="text" name="km" class="form-control" placeholder="Km" required readonly value="{{$maintanance->km}}" />
                </div>
                <div class="form-group col-sm-4">
                    <label class="col-sm-3 label-controls">Status</label>
                    <div class="row" style="margin-left:10px; margin-top:10px;">
                      @if($maintanance->status == 1)
                          <span class="badge badge-info" style="height:20px; width:50px;">Publish</span>
                      @else
                          <span class="badge badge-warning">Draft</span>
                      @endif
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
    <div class="col-lg-12">
      <div class="card card-{{ config('configs.app_theme') }} card-outline" style="height:96%;">
        <div class="card-header" style="height:55px">
          <h3 class="card-title">List Item</h3>
        </div>
        <div class="card-body">
          <table class="table table-striped table-bordered datatable" id="table-product" style="width: 100%">
            <thead>
              <tr>
                <th width="150">Item</th>
                <th width="50" style="text-align: center">Qty</th>
                <th width="100" style="text-align: right">Cost</th>
                <th width="100" style="text-align: right">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              @foreach($maintananceitems as $maintenanceitem)
              <tr>
                <td>
                  {{ $maintenanceitem->item}}
                </td>
                <td class="text-center" width="100">
                  {{$maintenanceitem->qty}}
                </td>
                <td class="text-right">
                  {{$maintenanceitem->cost}}
                </td>
                <td class="text-right">
                  {{$maintenanceitem->subtotal}}
                </td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td></td>
                <td></td>
                <td colspan="1"  class="text-right">Total</td>
                <td class="text-right">
                  {{$maintanance->total}}
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
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/jquery-mask/jquery.mask.min.js')}}"></script>
<script>
  $(document).ready(function(){
    calculate();
    $(document).on("input keydown keyup mousedown mouseup select contextmenu drop", ".numberfield", function () {
        if (/^\d*$/.test(this.value)) {
            this.oldValue = this.value;
            this.oldSelectionStart = this.selectionStart;
            this.oldSelectionEnd = this.selectionEnd;
        } else if (this.hasOwnProperty("oldValue")) {
            this.value = this.oldValue;
            this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
        }
    });
    $(document).on('keyup',".qty , .cost",function(){
          var cost = $(this).closest('tr').find(".cost").val();
          var qty = $(this).closest('tr').find(".qty").val();
          var subtotal = cost * qty;
          $(this).closest('tr').find('.subtotal').attr('value',subtotal);
          calculate();
          
    });
    $('input[name=status]').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
    $('.select2').select2();
    $("#vehicle_id" ).select2({
      ajax: {
        url: "{{route('maintanance.readvehicle')}}",
        type:'GET',
        dataType: 'json',
        data: function (term,page) {
          return {
            // department_id:69,
            // title_id:22,
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
              vehicle_name: `${item.name}`,
              category_name: `${item.category_name}`,
              license_no: `${item.license_no}`,
              vendor: `${item.vendor}`,
              driver: `${item.driver}`
            });
          });
          return {
            results: option, more: more,
          };
        },
      },
      allowClear: true,
    });
    @if ($maintanance->asset)
		$("#vehicle_id").select2('data',{id:{{$maintanance->asset->id}},text:'{{$maintanance->asset->name}}'}).trigger('change');
    @endif
    
     $(document).on('change', '#vehicle_id', function () {
        var vehicle_id = $('#vehicle_id').select2('data').id;
        var vehicle_name = $('#vehicle_id').select2('data').vehicle_name;
        var category_name = $('#vehicle_id').select2('data').category_name;
        var license_no = $('#vehicle_id').select2('data').license_no;
        var vendor = $('#vehicle_id').select2('data').vendor;
        var driver = $('#vehicle_id').select2('data').driver;
        $('#vehicle_id').val(`${vehicle_id}`);
        $('#vehicle_name').val(`${vehicle_name}`);
        $('#vehicle_category').val(`${category_name}`);
        $('#vehicle_no').val(`${license_no}`);
        $('#vendor').val(`${vendor}`);
        $('#driver').val(`${driver}`);
      });
      
      $('#vehicle_id').on('select2:clear', function () {
        $('#vehicle_id').select2('val', '');
        $('#vehicle_name').val('');
        $('#vehicle_category').val('');
        $('#vehicle_no').val('');
        $('#vendor').val('');
        $('#driver').val('');
      });
    
    $('#table-product').on('click','.remove',function(){
     $(this).parents('tr').remove();
     calculate();
    });
    $('.datepicker').daterangepicker({
      singleDatePicker: true,
      timePicker: false,
      timePicker24Hour: false,
      timePickerIncrement: 1,
      timePickerSeconds: false,
      locale: {
      format: 'YYYY/MM/DD'
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
      var length = $('#table-product tr').length;
      var html = `<tr>
                <td>
                  <input type="text" class="form-control" placeholder="Item" name="item[]" required />
                </td>
                <td class="text-center" width="100">
                  <input type="text" class="form-control text-center numberfield qty" placeholder="Qty" name="qty[]" value="0" required />
                </td>
                <td class="text-right">
                  <input placeholder="Cost" id="cost[]" name="cost[]" class="form-control text-right cost numberfield" value="0" required/>
                </td>
                <td class="text-right">
                  <input readonly placeholder="Cost" id="subtotal" name="subtotal[]" class="form-control text-right subtotal numberfield" value="0" required/>
                </td>
                <td class="text-center">
                  <a style="color: white;" class="btn btn-sm btn-danger remove" title="Remove"><i class="fa fa-minus-circle"></i></a>
                </td>
              </tr>`;
      $('#table-product').append($(html));
    
    }

    function calculate() {
      var total = 0;
      $('#table-product').find("tbody>tr").each(function(key, row){

          var subtotal = parseInt($(this).find('.subtotal').val());
          total += subtotal;

      });
      
      $('#total').html(total);
      $('#form input[name=total]').val(total);
    }
    

</script>
@endpush