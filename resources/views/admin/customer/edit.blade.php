@extends('admin.layouts.app')

@section('title', 'Edit Pelanggan')
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('customer.index')}}">Pelanggan</a></li>
<li class="breadcrumb-item active">Edit</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">


<style type="text/css">
  #map {
    height: 300px;
    border: 1px solid #CCCCCC;
  }
</style>
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Edit Pelanggan</h3>
        <!-- tools box -->
        <div class="pull-right card-tools">
          <button form="form" type="submit" class="btn btn-sm btn-danger text-white" title="Simpan"><i
              class="fa fa-save"></i></button>
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
              class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="card-body">
        <form id="form" action="{{route('customer.update',['id'=>$customer->id])}}" class="form-horizontal"
          method="post" autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="put">
          <div class="well well-sm">
            <div class="form-group row">
              <label for="name" class="col-sm-2 control-label">Kode <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="code" name="code" placeholder="Kode"
                  value="{{$customer->code}}" required>
              </div>
            </div>
            <div class="form-group row">
              <label for="name" class="col-sm-2 control-label">Nama <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="name" name="name" placeholder="Nama"
                  value="{{$customer->name}}" required>
              </div>
            </div>
            <div class="form-group row">
              <label for="name" class="col-sm-2 control-label">Group Pelanggan <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="customergroup_id" name="customergroup_id"
                  data-placeholder="Pilih Grup Pelanggan" required>
              </div>
            </div>
          </div>
          <div class="well well-sm">
            <div class="form-group row">
              <label for="email" class="col-sm-2 control-label">Email <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="email" class="form-control" id="email" name="email" placeholder="Email"
                  value="{{$customer->email}}" required>
              </div>
            </div>

            <div class="form-group row">
              <label for="app_logo" class="col-sm-2 control-label">Foto</label>
              <div class="col-sm-6">
                <input type="file" class="form-control" name="picture" id="picture" value={{$customer->picture}} />
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="overlay d-none">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
    </div>
    <div class="card card-danger card-outline card-outline-tabs">
      <div class="card-header p-0 border-bottom-0">
        <div class="pull-right card-tools">
          <a href="#" onclick="address()" class="btn btn-warning btn-sm text-white" data-toggle="tooltip"
            title="Tambah">
            <i class="fa fa-plus"></i>
          </a>
          <a href="#" onclick="contact()" class="btn btn-danger btn-sm text-white" data-toggle="tooltip" title="Tambah">
            <i class="fa fa-plus"></i>
          </a>
          <a href="#" onclick="filter()" class="btn btn-default btn-sm" data-toggle="tooltip" title="Search">
            <i class="fa fa-search"></i>
          </a>
        </div>
        <ul class="nav nav-tabs">
          <li class="nav-item"><a class="nav-link active" href="#site" data-toggle="tab">Alamat</a></li>
          <li class="nav-item"><a class="nav-link" href="#log" data-toggle="tab">Contact</a></li>
        </ul>

      </div>
      <div class="tab-content">
        <div class="tab-pane active" id="site">
          <div class="card-body">
            <table class="table table-bordered table-striped" style="width:100%" id="table-site">
              <thead>
                <tr>
                  <th style="text-align:center" width="10">#</th>
                  <th width="100">Alamat</th>
                  <th width="10">#</th>
                </tr>
              </thead>
            </table>
          </div>
          <div class="overlay d-none">
            <i class="fa fa-2x fa-sync-alt fa-spin"></i>
          </div>
        </div>
        <div class="tab-pane" id="log">
          <div class="card-body">
            <table class="table table-bordered table-striped" style="width:100%" id="table-log">
              <thead>
                <tr>
                  <th style="text-align:center" width="10">#</th>
                  <th width="100">Name</th>
                  <th width="100">Phone</th>
                  <th width="100">Email</th>
                  <th width="100">Address</th>
                  <th width="10">#</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="add_address" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog"
  aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Tambah Alamat</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>
      <div class="modal-body">
        <form id="form_address" action="{{route('customeraddress.store')}}" class="form-horizontal" method="post"
          autocomplete="off">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-md-12">
              <input type="hidden" name="customer_id" value="{{ $customer->id }}">
              <div class="form-group row">
                <label for="district_id" class="col-sm-2 control-label">Provinsi <b class="text-danger">*</b></label>
                <div class="col-sm-12">
                  <input type="text" class="form-control" id="province_id" name="province_id" placeholder="Provinsi"
                    required>
                </div>
              </div>
              <div class="form-group row">
                <label for="district_id" class="col-sm-2 control-label">Kota<b class="text-danger">*</b></label>
                <div class="col-sm-12">
                  <input type="text" class="form-control" id="region_id" name="region_id" placeholder="Kota" required>
                </div>
              </div>
              <div class="form-group row">
                <label for="district_id" class="col-sm-2 control-label">Kabupaten<b class="text-danger">*</b></label>
                <div class="col-sm-12">
                  <input type="text" class="form-control" id="district_id" name="district_id" placeholder="Kabupaten"
                    required>
                </div>
              </div>
              <div class="form-group row">
                <label for="name" class="col-sm-2 control-label">Alamat <b class="text-danger">*</b></label>
                <div class="col-sm-12">
                  <textarea name="address" id="address" class="form-control" required
                    placeholder="Alamat">{{$customer->address}}</textarea>
                  <div id="map"></div>
                </div>
              </div>
              <div class="form-group row">
                <label for="name" class="col-sm-2 control-label">Latitude</label>
                <div class="col-sm-12">
                  <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Latitude"
                    value="{{$customer->latitude}}">
                </div>
              </div>
              <div class="form-group row">
                <label for="name" class="col-sm-2 control-label">Longitude</label>
                <div class="col-sm-12">
                  <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Longitude"
                    value="{{$customer->longitude}}">
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button form="form_address" type="submit" class="btn btn-sm btn-danger text-white" title="Simpan"><i
            class="fa fa-save"></i></button> </div>
    </div>
  </div>
</div>

<div class="modal fade" id="add_contact" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog"
  aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Tambah Contact</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>
      <div class="modal-body">
        <form id="form_contact" action="{{route('customercontact.store')}}" class="form-horizontal" method="post"
          autocomplete="off">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-md-12">
              <input type="hidden" name="customer_id" value="{{ $customer->id }}">
              <div class="form-group row">
                <label for="contact_name" class="control-label">Contact Nama <b class="text-danger">*</b></label>
                <div class="col-sm-12">
                  <input type="text" class="form-control" id="contact_name" name="contact_name"
                    placeholder="Contact Nama" required>
                </div>
              </div>
              <div class="form-group row">
                <label for="contact_phone" class="control-label">Contact Phone<b class="text-danger">*</b></label>
                <div class="col-sm-12">
                  <input type="text" class="form-control" id="contact_phone" name="contact_phone"
                    placeholder="Contact Phone" required>
                </div>
              </div>
              <div class="form-group row">
                <label for="contact_email" class="control-label">Contact Email<b class="text-danger">*</b></label>
                <div class="col-sm-12">
                  <input type="email" class="form-control" id="contact_email" name="contact_email"
                    placeholder="Contact Email" required>
                </div>
              </div>
              <div class="form-group row">
                <label for="contact_address" class="control-label">Contact Alamat <b class="text-danger">*</b></label>
                <div class="col-sm-12">
                  <textarea name="contact_address" id="contact_address" class="form-control" required
                    placeholder="Contact Alamat"></textarea>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button form="form_contact" type="submit" class="btn btn-sm btn-danger text-white" title="Simpan"><i
            class="fa fa-save"></i></button> </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script type="text/javascript"
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDk0A3uPdfOld8ZG1ibIZRaEktd-2Kv33E"></script>
<script>
  function address(){
    $('#add_address').modal('show');
  }
  function contact(){
    $('#add_contact').modal('show');
}
  var map, geocoder, marker, infowindow;
  $(document).ready(function(){
    
    
    // Mengambil referensi ke form HTML
    
      $("#picture").fileinput({
          browseClass: "btn btn-danger",
          showRemove: false,
          showUpload: false,
          allowedFileExtensions: ["png", "jpg", "jpeg"],
          dropZoneEnabled: false,
          initialPreview: '<img src="{{asset($customer->picture)}}" class="kv-preview-data file-preview-image">',
          initialPreviewAsData: false,
          initialPreviewFileType: 'image',
          initialPreviewConfig: [
          {caption: "{{$customer->picture}}", downloadUrl: "{{asset($customer->picture)}}", size:"{{ @File::size(public_path($customer->picture))}}",url: false}
          ],
          theme:'explorer-fas'
      });
      $( "#customergroup_id" ).select2({
        ajax: {
          url: "{{route('customergroup.select')}}",
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
                text: `${item.customergroup_name}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $("#customergroup_id").select2('data',{id:{{$customer->customergroup_id}},text:'{{$customer->customergroup->customergroup_name}}'}).trigger('change');
      $(document).on("change", "#customergroup_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
       $( "#province_id" ).select2({
        ajax: {
          url: "{{route('province.select')}}",
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
                text: `${item.name}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $( "#region_id" ).select2({
        ajax: {
          url: "{{route('region.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              province_id:$('#province_id').val(),
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
                text: `${item.type} ${item.name}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $( "#district_id" ).select2({
        ajax: {
          url: "{{route('district.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              region_id:$('#region_id').val(),
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
                text: `${item.name}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $(document).on("change", "#province_id", function () {
        if (!$.isEmptyObject($('#form_address').validate().submitted)) {
          $('#form_address').validate().form();
        }
        $('#region_id').select2('val','');
        $('#district_id').select2('val','');
      });
      $(document).on("change", "#region_id", function () {
        if (!$.isEmptyObject($('#form_address').validate().submitted)) {
          $('#form_address').validate().form();
        }
        $('#district_id').select2('val','');
      });
      $(document).on("change", "#district_id", function () {
        if (!$.isEmptyObject($('#form_address').validate().submitted)) {
          $('#form_address').validate().form();
        }
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
          })
        }
      });
       $("#form_contact").validate({
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
            url:$('#form_contact').attr('action'),
            method:'post',
            data: new FormData($('#form_contact')[0]),
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend:function(){
               $('.overlay').removeClass('hidden');
            }
          }).done(function(response){
                $('.overlay').addClass('hidden');
                if(response.status){
                  $('#add_contact').modal('hide');
                  dataTableLog.draw();
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
          })
        }
      });
       $("#form_address").validate({
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
            url:$('#form_address').attr('action'),
            method:'post',
            data: new FormData($('#form_address')[0]),
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend:function(){
               $('.overlay').removeClass('hidden');
            }
          }).done(function(response){
                $('.overlay').addClass('hidden');
                if(response.status){
                  $('#add_address').modal('hide');
                  dataTableLog.draw();
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
          })
        }
      });
      dataTableSite = $('#table-site').DataTable( {
        stateSave:true,
        processing: true,
        serverSide: true,
        filter:false,
        info:false,
        lengthChange:true,
        responsive: true,
        order: [[ 2, "asc" ]],
        ajax: {
            url: "{{route('customeraddress.read')}}",
            type: "GET",
            data:function(data){
                var address = $('#form-search').find('input[name=address]').val();
                data.address = address;
                data.customer_id = {{$customer->id}};
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [0] },
            { className: "text-center", targets: [2] },
            { render: function ( data, type, row ) {
                return `<div class="dropdown">
                    <button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item" href="{{url('admin/customeraddress')}}/${row.id}/edit"><i class="fa fa-pencil-alt mr-2"></i> Edit</a></li>
                        <li><a class="dropdown-item deleteaddress" href="#" data-id="${row.id}"><i class="fa fa-trash mr-2"></i> Delete</a></li>                    
                    </ul>
                    </div>`
            },targets: [2]
            }
        ],
        columns: [
            { data: "no" },
            { data: "address" },
            { data: "id" },
        ]
      });
      dataTableLog = $('#table-log').DataTable( {
          stateSave:true,
          processing: true,
          serverSide: true,
          filter:false,
          info:false,
          lengthChange:true,
          responsive: true,
          order: [[ 5, "asc" ]],
          ajax: {
              url: "{{route('customercontact.read')}}",
              type: "GET",
              data:function(data){
                  var contact_name = $('#form-search').find('input[name=contact_name]').val();
                  data.contact_name = contact_name;
                  data.customer_id = {{$customer->id}};
              }
          },
          columnDefs:[
              {
                  orderable: false,targets:[0]
              },
              { className: "text-right", targets: [0] },
              { className: "text-center", targets: [5] },
              { render: function ( data, type, row ) {
                  return `<div class="dropdown">
                      <button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                      <i class="fa fa-bars"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-right">
                          <li><a class="dropdown-item" href="{{url('admin/customercontact')}}/${row.id}/edit"><i class="fa fa-pencil-alt mr-2"></i> Edit</a></li>
                          <li><a class="dropdown-item deletecontact" href="#" data-id="${row.id}"><i class="fa fa-trash mr-2"></i> Delete</a></li>
                      </ul>
                      </div>`
              },targets: [5]
              }
          ],
          columns: [
              { data: "no" },
              { data: "contact_name" },
              { data: "contact_phone" },
              { data: "contact_email" },
              { data: "contact_address" },
              { data: "id" },
          ]
      });
      $(document).on('click','.deleteaddress',function(){
          var id = $(this).data('id');
          bootbox.confirm({
        buttons: {
          confirm: {
            label: '<i class="fa fa-check"></i>',
            className: 'btn-danger'
          },
          cancel: {
            label: '<i class="fa fa-undo"></i>',
            className: 'btn-default'
          },
        },
        title:'Menghapus Data Kategori Produk?',
        message:'Data yang telah dihapus tidak dapat dikembalikan',
        callback: function(result) {
            if(result) {
              var data = {
                              _token: "{{ csrf_token() }}",
                              id: id
                          };
              $.ajax({
                url: `{{url('admin/customeraddress')}}/${id}`,
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
                  $('.overlay').addClass('hidden');
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

      $(document).on('click','.deletecontact',function(){
          var id = $(this).data('id');
          bootbox.confirm({
        buttons: {
          confirm: {
            label: '<i class="fa fa-check"></i>',
            className: 'btn-danger'
          },
          cancel: {
            label: '<i class="fa fa-undo"></i>',
            className: 'btn-default'
          },
        },
        title:'Menghapus Data Contact?',
        message:'Data yang telah dihapus tidak dapat dikembalikan',
        callback: function(result) {
            if(result) {
              var data = {
                              _token: "{{ csrf_token() }}",
                              id: id
                          };
              $.ajax({
                url: `{{url('admin/customercontact')}}/${id}`,
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
                  $('.overlay').addClass('hidden');
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
      

      var address = $('#address').val(),
            lat = $('#latitude').val()||-7.217416,
            long = $('#longitude').val()||112.72990470000002;
      var options = {
        zoom: 10,
        center: new google.maps.LatLng(lat, long),
        mapTypeId: google.maps.MapTypeId.ROADMAP
      };

      map = new google.maps.Map(document.getElementById('map'), options);
       $( "#form_address textarea[name=address]" ).keyup(function() {
      address = $(this).val();
      getCoordinates(address);
    });

      setCoordinates(address,lat,long);
      
  });
    


  function setCoordinates(address,latitude,longitude) {
    // Mengecek apakah terdapat 'geocoded object'. Jika tidak maka buat satu.

          map.setCenter(new google.maps.LatLng(latitude, longitude));

        // Mengecek apakah terdapat objek marker
        if (!marker) {
          // Membuat objek marker dan menambahkan ke peta
          marker = new google.maps.Marker({
            map: map,
      draggable:true,
          });
        }

        // Menentukan posisi marker ke lokasi returned location

    marker.setPosition(new google.maps.LatLng(latitude, longitude));

        // Mengecek apakah terdapat InfoWindow object
        if (!infowindow) {
          // Membuat InfoWindow baru
          infowindow = new google.maps.InfoWindow();
    }
    google.maps.event.addListener(marker, 'drag', function() {

    updateMarkerPosition(marker.getPosition());
    });
        // membuat konten InfoWindow ke alamat
        // dan posisi yang ditemukan
        var content = '<strong>' + address + '</strong><br/>';
        content += 'Lat: ' + latitude + '<br />';
        content += 'Lng: ' + longitude;

        // Menambahkan konten ke InfoWindow
        infowindow.setContent(content);

        // Membuka InfoWindow
        infowindow.open(map, marker);

    // Membuat rekues Geocode

  }

  function updateMarkerPosition(latLng) {
	  if(!geocoder) {
        geocoder = new google.maps.Geocoder();
      }

      // Membuat objek GeocoderRequest
      var geocoderRequest = {
        latLng: latLng
      }

      // Membuat rekues Geocode
      geocoder.geocode(geocoderRequest, function(results, status) {

        // Mengecek apakah ststus OK sebelum proses
        if (status == google.maps.GeocoderStatus.OK) {

          // Menengahkan peta pada lokasi

          // Mengecek apakah terdapat objek marker
          if (!marker) {
            // Membuat objek marker dan menambahkan ke peta
            marker = new google.maps.Marker({
              map: map,
			  draggable: true
            });
          }

          // Menentukan posisi marker ke lokasi returned location
          marker.setPosition(results[0].geometry.location);

          // Mengecek apakah terdapat InfoWindow object
          if (!infowindow) {
            // Membuat InfoWindow baru
            infowindow = new google.maps.InfoWindow();
          }
		  google.maps.event.addListener(marker, 'drag', function() {

			updateMarkerPosition(marker.getPosition());
		  });

          // membuat konten InfoWindow ke alamat
          // dan posisi yang ditemukan
          var content = '<strong>' + results[0].formatted_address + '</strong><br />';
          content += 'Lat: ' + results[0].geometry.location.lat() + '<br />';
          content += 'Lng: ' + results[0].geometry.location.lng();

		  $('#form_address input[name=latitude]').attr('value',results[0].geometry.location.lat());
		  $('#form_address input[name=longitude]').attr('value',results[0].geometry.location.lng());
          // Menambahkan konten ke InfoWindow
          infowindow.setContent(content);

          // Membuka InfoWindow
          infowindow.open(map, marker);

        }

      });
	}

  // Membuat sebuah fungsi yang mengembalikan koordinat alamat
  function getCoordinates(address) {
      // Mengecek apakah terdapat 'geocoded object'. Jika tidak maka buat satu.
      if(!geocoder) {
        geocoder = new google.maps.Geocoder();
      }

      // Membuat objek GeocoderRequest
      var geocoderRequest = {
        address: address
      }

      // Membuat rekues Geocode
      geocoder.geocode(geocoderRequest, function(results, status) {

        // Mengecek apakah ststus OK sebelum proses
        if (status == google.maps.GeocoderStatus.OK) {

          // Menengahkan peta pada lokasi
          map.setCenter(results[0].geometry.location);

          // Mengecek apakah terdapat objek marker
          if (!marker) {
            // Membuat objek marker dan menambahkan ke peta
            marker = new google.maps.Marker({
              map: map,
			  draggable: true
            });
          }

          // Menentukan posisi marker ke lokasi returned location
          marker.setPosition(results[0].geometry.location);

          // Mengecek apakah terdapat InfoWindow object
          if (!infowindow) {
            // Membuat InfoWindow baru
            infowindow = new google.maps.InfoWindow();
          }
		  google.maps.event.addListener(marker, 'drag', function() {

			updateMarkerPosition(marker.getPosition());
		  });

          // membuat konten InfoWindow ke alamat
          // dan posisi yang ditemukan
          var content = '<strong>' + results[0].formatted_address + '</strong><br />';
          content += 'Lat: ' + results[0].geometry.location.lat() + '<br />';
          content += 'Lng: ' + results[0].geometry.location.lng();

		  $('#form_address input[name=latitude]').attr('value',results[0].geometry.location.lat());
		  $('#form_address input[name=longitude]').attr('value',results[0].geometry.location.lng());
		  //$('#alamat_pelanggan_tmp').attr('value',results[0].formatted_address);
          // Menambahkan konten ke InfoWindow
          infowindow.setContent(content);

          // Membuka InfoWindow
          infowindow.open(map, marker);

        }

      });

    }
</script>
@endpush