@extends('admin.layouts.app')

@section('title', 'Edit Outsourcing')
@push('breadcrump')
    <li class="breadcrumb-item"><a href="{{route('outsourcing.index')}}">Outsourcing</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">


<style type="text/css">    
    .card-header {
        height: 50px;
    }

    .card {
        height: 95%;
        display: flex;
    }

</style>
@endsection
@section('content')
<div class="row">
  <div class="col-lg-7">
    <div class="card card-{{config('configs.app_theme')}} card-outline">
        <div class="card-header">
          <h3 class="card-title">Edit Outsourcing</h3>
          <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
    
                </div>
        </div>
        <div class="card-body">
            <form id="form" action="{{route('outsourcing.update',['id'=>$outsourcing->id])}}" class="form-horizontal" method="post" autocomplete="off">
              {{ csrf_field() }}
              <input type="hidden" name="_method" value="put">
              <div class="d-flex">
                  <div class="form-group col-sm-6">
                    <label for="name" class="col-sm-4 col-form-label">Code <b class="text-danger">*</b></label>
                      <input type="text" class="form-control" id="code" name="code" placeholder="Code" value="{{$outsourcing->code}}" required>
                  </div>
                  <div class="form-group col-sm-6">
                    <label for="name" class="col-sm-4 col-form-label">Name <b class="text-danger">*</b></label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="{{$outsourcing->name}}" required>
                  </div>
                </div>
                <div class="d-flex">
                  <div class="form-group col-sm-6">
                    <label for="email" class="col-sm-4 col-form-label">Email <b class="text-danger">*</b></label>
                      <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{$outsourcing->email}}" required>
                  </div>
                  <div class="form-group col-sm-6">
                    <label for="no_tlpn" class="col-sm-4 col-form-label">Phone <b class="text-danger">*</b></label>
                      <input type="text" class="form-control" id="no_tlpn" name="no_tlpn" placeholder="Telpon" value="{{$outsourcing->no_tlpn}}" required>
                  </div> 
                </div>
                <div class="d-flex">
                  <div class="form-group col-sm-12">
                    <label for="text" class="form-label">Workgroup Combination <b class="text-danger">*</b></label>
                      <input type="text" class="form-control" id="workgroup_id" name="workgroup_id" placeholder="Workgroup Combination" required>
                  </div> 
                </div>              
              </div>
        <div class="overlay d-none">
          <i class="fa fa-2x fa-sync-alt fa-spin"></i>
        </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card card-{{config('configs.app_theme')}} card-outline">
      <div class="card-header">
        <h3 class="card-title">Other</h3>
        <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="Simpan"><i class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
        </div>
      </div>
      <div class="card-body">
        <div class="form-group row">
            <label for="app_logo" class="control-label">Picture</label>
            <div class="col-sm-12">
            <input type="file" class="form-control" name="image" id="picture" value={{$outsourcing->image}}/>
            </div>
        </div>
        <div class="form-group row">
            <label for="status" class="col-form-label">Status <b class="text-danger">*</b></label>
            <div class="col-sm-12">
              <select id="status" name="status" class="form-control select2" placeholder="Pilih Status"
              required>
                  <option @if($outsourcing->status == 1) selected @endif value="1">Active</option>
                  <option @if($outsourcing->status == 0) selected @endif value="2">Tidak Active</option>
              </select>
            </div>
        </div>
      </div>
      <div class="overlay d-none">
        <i class="fa fa-2x fa-sync-alt fa-spin"></i>
      </div>
    </div>
    </form>
  </div>

  <div class="col-lg-12">
    <div class="card card-{{config('configs.app_theme')}} card-outline">
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs">
                <li class="nav-item"><a class="nav-link active" href="#address" data-toggle="tab">Address</a></li>
                <li class="nav-item"><a class="nav-link" href="#pic" data-toggle="tab">PIC</a></li>
                <li class="nav-item"><a class="nav-link" href="#legalitas" data-toggle="tab">Legalitas</a></li>
                <li class="nav-item"><a class="nav-link" href="#employee" data-toggle="tab">Employee</a></li>
            </ul>    
        </div>
        <div class="tab-content">
            <div class="tab-pane active" id="address">
              <div class="card-header">
                <h3 class="card-title">List Address</h3>
                <div class="pull-right card-tools">
                  <a href="#" class="btn btn-{{config('configs.app_theme')}} btn-sm text-white add_address" data-toggle="tooltip" title="Tambah">
                  <i class="fa fa-plus"></i>
                  </a>
                </div>
              </div>
                <div class="card-body">
                    <table  class="table table-bordered table-striped" style="width:100%" id="table-address">
                        <thead>
                            <tr>
                                <th style="text-align:center" width="10">#</th>
                                <th width="200" >Alamat</th>
                                <th width="50" >Default</th>
                                <th width="10" >#</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="overlay d-none">
                    <i class="fa fa-2x fa-sync-alt fa-spin"></i>
                </div>
            </div>
            <div class="tab-pane" id="pic">
              <div class="card-header">
                <h3 class="card-title">List Contact</h3>
                <div class="pull-right card-tools">
                  <a href="#" class="btn btn-{{config('configs.app_theme')}} btn-sm text-white add_contact" data-toggle="tooltip" title="Tambah">
                  <i class="fa fa-plus"></i>
                  </a>
                </div>
              </div>
                <div class="card-body">
                    <table  class="table table-bordered table-striped" style="width:100%" id="table-pic">
                        <thead>
                            <tr>
                                <th style="text-align:center" width="10">#</th>
                                <th width="100" >Category</th>
                                <th width="100" >Name</th>
                                <th width="100" >Phone</th>
                                <th width="100" >Email</th>
                                <th width="50" >Default</th>
                                <th width="10" >#</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="tab-pane" id="legalitas">
              <div class="card-header">
                <h3 class="card-title">List Legalitas</h3>
                <div class="pull-right card-tools">
                  <a href="#" class="btn btn-{{config('configs.app_theme')}} btn-sm text-white add_document" data-toggle="tooltip" title="Tambah">
                    <i class="fa fa-plus"></i>
                    </a>
                </div>
              </div>
                <div class="card-body">
                    <table  class="table table-bordered table-striped" style="width:100%" id="table-document">
                        <thead>
                            <tr>
                                <th style="text-align:center" width="10">#</th>
                                <th width="100" >Category</th>
                                <th width="100" >Nomor Legalitas</th>
                                <th width="100" >Document Name</th>
                                <th width="100" >File Name</th>
                                <th width="100" >Description</th>
                                <th width="10" >#</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="tab-pane" id="employee">
              <div class="card-header">
                <h3 class="card-title">List Employee</h3>
              </div>
                <div class="card-body">
                    <table  class="table table-bordered table-striped" style="width:100%" id="table-product">
                        <thead>
                            <tr>
                                <th style="text-align:center" width="10">#</th>
                                <th width="100" >Employee</th>
                                <th width="100" >NIK</th>
                                <th width="100" >Position</th>
                                <th width="100" >Department</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
{{-- Modal Address --}}
<div class="modal fade" id="add_address" tabindex="-1" role="dialog"  aria-hidden="true" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Alamat</h4>
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
              <form id="form_address" class="form-horizontal" method="post"autocomplete="off">
              <div class="row">
                <div class="col-md-6">
                  <input type="hidden" name="outsourcing_id">
                      <div class="form-group col-sm-12">
                        <label for="district_id" class="control-label">Provinsi <b class="text-danger">*</b></label>
                          <input type="text" class="form-control" id="province_id" name="province_id" placeholder="Provinsi" required>
                      </div>
                      <div class="form-group col-sm-12">
                        <label for="district_id" class="control-label">Kota/Kabupaten<b class="text-danger">*</b></label>
                          <input type="text" class="form-control" id="region_id" name="region_id" placeholder="Kota" required>
                      </div>
                    <div class="form-group col-sm-12">
                      <label for="district_id" class="control-label">Kecamatan<b class="text-danger">*</b></label>
                        <input type="text" class="form-control" id="district_id" name="district_id" placeholder="Kabupaten" required>
                    </div>
                  <div class="form-group col-sm-12">
                      <label for="postal_code" class="control-label">Postal Code<b class="text-danger">*</b></label>
                      <input type="text" class="form-control" id="postal_code" name="kode_pos" placeholder="Postal Code" required>
                  </div>
                
                     {{ csrf_field() }}
                      <input type="hidden" name="_method"/>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="name" class="control-label">Alamat <b class="text-danger">*</b></label>
                    <textarea name="address" id="address" class="form-control" required placeholder="Alamat"></textarea>
                  </div>
                  <div class="form-group col-sm-6">
                      <label class="control-label row">Default</label>
                      <label><input class="form-control"  type="checkbox" name="default" @if($outsourcing->default) checked @endif> <i></i></label>
                  </div>
                   
                </div>
              </form>
              </div>
            </div>
            <div class="modal-footer">
              <button form="form_address" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="Simpan"><i class="fa fa-save"></i></button>            
            </div>
            <div class="overlay d-none">
                    <i class="fa fa-2x fa-sync-alt fa-spin"></i>
            </div>
          </div>
        </div>
    </div>
</div>

{{-- Modal Contact --}}
<div class="modal fade" id="add_contact" tabindex="-1" role="dialog"  aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Kontak</h4>
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
			      </div>
            <div class="modal-body">
                <form id="form_contact" class="form-horizontal" method="post"autocomplete="off">
                    <div class="row">
                      <input type="hidden" name="outsourcing_id">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="pic_name" class="control-label">Name <b class="text-danger">*</b></label>
                          <input type="text" class="form-control" id="pic_name" name="pic_name" placeholder="PIC Name" required>
                        </div>
                        <div class="form-group">
                          <label for="pic_phone" class="control-label">Phone<b class="text-danger">*</b></label>
                          <input type="text" class="form-control" id="pic_phone" name="pic_phone" placeholder="PIC Phone" required>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="pic_email" class="control-label">Email<b class="text-danger">*</b></label>
                          <input type="email" class="form-control" id="pic_email" name="pic_email" placeholder="PIC Email" required>
                        </div>
                        <div class="form-group">
                          <label for="pic_category" class="control-label">Category<b class="text-danger">*</b></label>
                            <select id="type_g" name="pic_category" class="form-control select2" placeholder="Select Category" required>
                                @foreach(config('enums.category_pic') as $key => $value))
                                <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                          </select>
                        </div>
                      </div>
                      
                      <div class="form-group col-md-12">
                        <label for="pic_address" class="control-label">Alamat <b class="text-danger">*</b></label>
                        <textarea name="pic_address" id="pic_address" class="form-control" required placeholder="Contact Alamat"></textarea>
                      </div> 
                      <div class="form-group col-sm-6">
                      <label class="control-label">Default</label>
                        <label style="margin-left:50px;"><input class="form-control"  type="checkbox" name="default" @if($outsourcing->default) checked @endif> <i></i></label>
                      </div>
                    </div> 
                    
                    {{ csrf_field() }}
                    <input type="hidden" name="_method"/>
                </form>
            </div>
            <div class="modal-footer">
                  <button form="form_contact" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="Simpan"><i class="fa fa-save"></i></button>            
            </div> 
            <div class="overlay d-none">
              <i class="fa fa-2x fa-sync-alt fa-spin"></i>
            </div>
        </div>
    </div>
</div>
{{-- Modal Document --}}
<div class="modal fade" id="add_document" tabindex="-1" role="dialog"  aria-hidden="true" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
          <div class="overlay-wrapper">
            <div class="modal-header">
                <h4 class="modal-title">Add Legalitas</h4>
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_document" class="form-horizontal" method="post"autocomplete="off">
                  <div class="d-flex">
                      <input type="hidden" name="outsourcing_id">
                      <div class="form-group col-sm-6">
                        <label for="category" class="control-label">Category <b class="text-danger">*</b></label>
                          <select id="category" name="category" class="form-control select2" data-placeholder="Select Category" required>
                              <option value=""></option>
                              @foreach(config('enums.document_category') as $key => $document_category)
                                <option value="{{$key}}">{{$document_category}}</option>
                              @endforeach
                          </select>
                      </div>
                      <div class="form-group col-sm-6">
                        <label for="phone" class="control-label">Phone<b class="text-danger">*</b></label>
                          <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone" required>
                      </div>
                  </div>
                  <div class="form-group col-sm-12">
                    <label for="name" class="control-label">Name<b class="text-danger">*</b></label>
                      <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                  </div>
                  <div class="form-group col-sm-12">
                    <label for="file" class="control-label">File</label>
                      <input type="file" value="file.jpg" class="form-control" name="file" id="file" accept="image/*"/>
                  </div>
                  <div class="form-group col-sm-12">
                    <label for="description" class="control-label">Description <b class="text-danger">*</b></label>
                    <textarea name="description" id="description" class="form-control" required placeholder="Description"></textarea>
                  </div>
                  {{ csrf_field() }}
                  <input type="hidden" name="_method"/>
                </form>
            </div>
            <div class="modal-footer">
              <button form="form_document" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="Simpan"><i class="fa fa-save"></i></button>            
            </div>
            <div class="overlay d-none">
                <i class="fa fa-2x fa-sync-alt fa-spin"></i>
            </div>
          </div>
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
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDk0A3uPdfOld8ZG1ibIZRaEktd-2Kv33E"></script>
<script>
  
  var map, geocoder, marker, infowindow;
  $(document).ready(function(){
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        var currentTab = $(e.target).text();
          switch (currentTab)   {
            case 'Alamat' :
                $('#table-address').css("width", '100%')
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
                break ;
            case 'Kontak' :
              $('#table-pic').css("width", '100%')
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
                break ;
            case 'Legalitas' :
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
                break ;
            default:
          };
      });
      $('.select2').select2();
      // Mengambil referensi ke form HTML
      $('input[name=default]').iCheck({
          checkboxClass: 'icheckbox_square-green',
          radioClass: 'iradio_square-green',
      });
    
    
    // Mengambil referensi ke form HTML

      $("#picture").fileinput({
          browseClass: "btn btn-{{config('configs.app_theme')}}",
          showRemove: false,
          showUpload: false,
          allowedFileExtensions: ["png", "jpg", "jpeg"],
          dropZoneEnabled: false,
          initialPreview: '<img src="{{asset($outsourcing->image)}}" class="kv-preview-data file-preview-image">',
          initialPreviewAsData: false,
          initialPreviewFileType: 'image',
          initialPreviewConfig: [
          {caption: "{{$outsourcing->image}}", downloadUrl: "{{asset($outsourcing->image)}}", size:"{{ @File::size(public_path($outsourcing->image))}}",url: false}
          ],
          theme:'explorer-fas'
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
      $("#workgroup_id").select2({
                ajax: {
                    url: "{{route('workgroup.select')}}",
                    type: 'GET',
                    dataType: 'json',
                    data: function (term, page) {
                        return {
                            name: term,
                            page: page,
                            limit: 30,
                        };
                    },
                    results: function (data, page) {
                        var more = (page * 30) < data.total;
                        var option = [];
                        $.each(data.rows, function (index, item) {
                            option.push({
                                id: item.id,
                                text: `${item.name}`
                            });
                        });
                        return {
                            results: option,
                            more: more,
                        };
                    },
                },
                allowClear: true,
            });
            @if($outsourcing->workgroup_id)
			$("#workgroup_id").select2('data',{id:{{$outsourcing->workgroup_id}},text:'{{$outsourcing->workgroup->name}}'}).trigger('change');
			@endif
            $(document).on("change", "#workgroup_id", function () {
                if (!$.isEmptyObject($('#form').validate().submitted)) {
                    $('#form').validate().form();
                }
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
        $('#village_id').select2('val','');
      });
      $(document).on("change", "#region_id", function () {
        if (!$.isEmptyObject($('#form_address').validate().submitted)) {
          $('#form_address').validate().form();
        }
        $('#district_id').select2('val','');
        $('#village_id').select2('val','');
      });
      $(document).on("change", "#district_id", function () {
        if (!$.isEmptyObject($('#form_address').validate().submitted)) {
          $('#form_address').validate().form();
        }
        $('#village_id').select2('val','');
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
               $('.overlay').removeClass('d-none');
            }
          }).done(function(response){
                $('.overlay').addClass('d-none');
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
              $('.overlay').addClass('d-none');
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
               $('.overlay').removeClass('d-none');
            }
          }).done(function(response){
                $('.overlay').addClass('d-none');
                if(response.status){
                  $('#add_contact').modal('hide');
                  dataTableContact.draw();
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
              $('.overlay').addClass('d-none');
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
               $('.overlay').removeClass('d-none');
            }
          }).done(function(response){
                $('.overlay').addClass('d-none');
                if(response.status){
                  $('#add_address').modal('hide');
                  dataTableAddress.draw();
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
              $('.overlay').addClass('d-none');
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
       $("#form_document").validate({
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
            url:$('#form_document').attr('action'),
            method:'post',
            data: new FormData($('#form_document')[0]),
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend:function(){
               $('.overlay').removeClass('d-none');
            }
          }).done(function(response){
                $('.overlay').addClass('d-none');
                if(response.status){
                  $('#add_document').modal('hide');
                  dataTableDocument.draw();
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
              $('.overlay').addClass('d-none');
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
      dataTableAddress = $('#table-address').DataTable( {
        stateSave:true,
        processing: true,
        serverSide: true,
        filter:false,
        info:false,
        lengthChange:true,
        responsive: true,
        order: [[ 3, "asc" ]],
        ajax: {
            url: "{{route('outsourcingaddress.read')}}",
            type: "GET",
            data:function(data){
                var address = $('#form-search').find('input[name=address]').val();
                data.address = address;
                data.outsourcing_id = {{$outsourcing->id}};
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [] },
            { className: "text-center", targets: [0,2,3] },
            { render : function(data, type, row){
                return `${row.address},</br>
                ${row.district_name}, ${row.region_name}, ${row.province_name}, ${row.kode_pos}`
            },
            targets: [1]},
            {
                render: function (data, type, row) {
                    if (row.default == 1) {
                        return `<i class="fa fa-check text-green">`
                    } else {
                        return `<i class="fa fa-times text-red">`
                    }
                },
                targets: [2]
            },
            { render: function ( data, type, row ) {
                return `<div class="dropdown">
                    <button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item editaddress" href="#" data-id="${row.id}"><i class="fa fa-pencil-alt mr-2"></i> Edit</a></li>
                        <li><a class="dropdown-item deleteaddress" href="#" data-id="${row.id}"><i class="fa fa-trash mr-2"></i> Delete</a></li>                    
                    </ul>
                    </div>`
            },targets: [3]
            }
        ],
        columns: [
            { data: "no" },
            { data: "address" },
            { data: "default"},
            { data: "id" }
        ]
      });
      $('.add_address').on('click',function(){
        $('#form_address')[0].reset();
        $('#form_address').attr('action',"{{route('outsourcingaddress.store')}}");
        $('#form_address input[name=_method]').attr('value','POST');
        $('#form_address input[name=outsourcing_id]').attr('value',{{ $outsourcing->id }});
        $('#form_address input[name=province_id]').select2('val','');
        $('#form_address input[name=region_id]').select2('val','');
        $('#form_address input[name=district_id]').select2('val','');
        // $('#form_address input[name=village_id]').select2('val','');
        $('#form_address input[name=kode_pos]').attr('value','');
        // $('#form_address select[name=village_id]').select2('val','');
        $('#form_address textarea[name=address]').html('');
        $('#form_address').find('input[name=default]').prop('checked',false);
        $('#form_address').find('input[name=default]').iCheck('update');
        $('#form_address .invalid-feedback').each(function () { $(this).remove(); });
        $('#form_address .form-group').removeClass('has-error').removeClass('has-success');
        $('#add_address .modal-title').html('Add Address');
        $('#add_address').modal('show');
      });
       $(document).on('click','.editaddress',function(){
        var id = $(this).data('id');
        $.ajax({
            url:`{{url('admin/outsourcingaddress')}}/${id}/edit`,
            method:'GET',
            dataType:'json',
            beforeSend:function(){
                $('#box-menu .overlay').removeClass('d-none');
            },
        }).done(function(response){
            $('#box-menu .overlay').addClass('d-none');
            if(response.status){
                $('#add_address .modal-title').html('Update Address');
                $('#add_address').modal('show');
                $('#form_address')[0].reset();
                $('#form_address .invalid-feedback').each(function () { $(this).remove(); });
                $('#form_address .form-group').removeClass('has-error').removeClass('has-success');
                $('#form_address input[name=_method]').attr('value','PUT');
                $('#form_address input[name=outsourcing_id]').attr('value',{{$outsourcing->id}});
                $("#province_id").select2('data',{id:response.data.province_id,text:response.data.province.name});
                $("#region_id").select2('data',{id:response.data.region_id,text:response.data.region.name});
                $("#district_id").select2('data',{id:response.data.district_id,text:response.data.district.name});
                $('#form_address input[name=kode_pos]').attr('value',response.data.kode_pos);
                $('#form_address textarea[name=address]').html(response.data.address);
                if(response.data.default == 1){
                    $('#form_address').find('input[name=default]').prop('checked',true);
                    $('#form_address').find('input[name=default]').iCheck('update');
                  } else {
                    $('#form_address').find('input[name=default]').prop('checked',false);
                    $('#form_address').find('input[name=default]').iCheck('update');
                  }
                $('#form_address').attr('action',`{{url('admin/outsourcingaddress/')}}/${response.data.id}`);
            }          
        }).fail(function(response){
            var response = response.responseJSON;
            $('#box-menu .overlay').addClass('d-none');
            $.gritter.add({
                title: 'Error!',
                text: response.message,
                class_name: 'gritter-error',
                time: 1000,
            });
        })	
      });
      dataTableContact = $('#table-pic').DataTable( {
          stateSave:true,
          processing: true,
          serverSide: true,
          filter:false,
          info:false,
          lengthChange:true,
          responsive: true,
          order: [[ 6, "asc" ]],
          ajax: {
              url: "{{route('outsourcingpic.read')}}",
              type: "GET",
              data:function(data){
                  var pic_name = $('#form-search').find('input[name=pic_name]').val();
                  data.pic_name = pic_name;
                  data.outsourcing_id = {{$outsourcing->id}};
              }
          },
          columnDefs:[
              {
                  orderable: false,targets:[0]
              },
              { className: "text-right", targets: [0] },
              { className: "text-center", targets: [5,6] },
              {
                render: function (data, type, row) {
                    if (row.default == 1) {
                        return `<i class="fa fa-check text-green">`
                    } else {
                        return `<i class="fa fa-times text-red">`
                    }
                },
                targets: [5]
            },
              { render: function ( data, type, row ) {
                  return `<div class="dropdown">
                      <button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                      <i class="fa fa-bars"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-right">
                          <li><a class="dropdown-item editcontact" href="#"data-id="${row.id}"><i class="fa fa-pencil-alt mr-2"></i> Edit</a></li>
                          <li><a class="dropdown-item deletecontact" href="#" data-id="${row.id}"><i class="fa fa-trash mr-2"></i> Delete</a></li>
                      </ul>
                      </div>`
              },targets: [6]
              }
          ],
          columns: [
              { data: "no" },
              { data: "pic_category" },
              { data: "pic_name" },
              { data: "pic_phone" },
              { data: "pic_email" },
              { data: "default"},
              { data: "id" },
          ]
      });
      $(document).on('click','.deleteaddress',function(){
          var id = $(this).data('id');
          bootbox.confirm({
        buttons: {
          confirm: {
            label: '<i class="fa fa-check"></i>',
            className: 'btn-{{config('configs.app_theme')}}'
          },
          cancel: {
            label: '<i class="fa fa-undo"></i>',
            className: 'btn-default'
          },
        },
        title:'Menghapus Data Alamat Outsourcing?',
        message:'Data yang telah dihapus tidak dapat dikembalikan',
        callback: function(result) {
            if(result) {
              var data = {
                              _token: "{{ csrf_token() }}",
                              id: id
                          };
              $.ajax({
                url: `{{url('admin/outsourcingaddress')}}/${id}`,
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
                      dataTableAddress.ajax.reload( null, false );
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
      $('.add_contact').on('click',function(){
        $('#form_contact')[0].reset();
        $('#form_contact').attr('action',"{{route('outsourcingpic.store')}}");
        $('#form_contact input[name=_method]').attr('value','POST');
        $('#form_contact input[name=outsourcing_id]').attr('value',{{ $outsourcing->id }});
        $('#form_contact input[name=pic_name]').attr('value','');
        $('#form_contact input[name=pic_phone]').attr('value','');
        $('#form_contact input[name=pic_email]').attr('value','');
        $('#form_contact select[name=pic_category]').select2('val','');
        $('#form_contact textarea[name=pic_address]').html('');
        $('#form_contact').find('input[name=default]').prop('checked',false);
        $('#form_contact').find('input[name=default]').iCheck('update');
        $('#form_contact .invalid-feedback').each(function () { $(this).remove(); });
        $('#form_contact .form-group').removeClass('has-error').removeClass('has-success');
        $('#add_contact .modal-title').html('Add PIC');
        $('#add_contact').modal('show');
      });
      $(document).on('click','.editcontact',function(){
        var id = $(this).data('id');
        $.ajax({
            url:`{{url('admin/outsourcingpic')}}/${id}/edit`,
            method:'GET',
            dataType:'json',
            beforeSend:function(){
                $('#box-menu .overlay').removeClass('d-none');
            },
        }).done(function(response){
            $('#box-menu .overlay').addClass('d-none');
            if(response.status){
                $('#add_contact .modal-title').html('Edit PIC');
                $('#add_contact').modal('show');
                $('#form_contact')[0].reset();
                $('#form_contact .invalid-feedback').each(function () { $(this).remove(); });
                $('#form_contact .form-group').removeClass('has-error').removeClass('has-success');
                $('#form_contact input[name=_method]').attr('value','PUT');
                $('#form_contact input[name=outsourcing_id]').attr('value',{{$outsourcing->id}});
                $('#form_contact input[name=pic_name]').attr('value',response.data.pic_name);
                $('#form_contact input[name=pic_phone]').attr('value',response.data.pic_phone);
                $('#form_contact input[name=pic_email]').attr('value',response.data.pic_email);
                $('#form_contact select[name=pic_category]').select2('val',response.data.pic_category);
                $('#form_contact textarea[name=pic_address]').html(response.data.pic_address);
                if(response.data.default == 1){
                    $('#form_contact').find('input[name=default]').prop('checked',true);
                    $('#form_contact').find('input[name=default]').iCheck('update');
                  } else {
                    $('#form_contact').find('input[name=default]').prop('checked',false);
                    $('#form_contact').find('input[name=default]').iCheck('update');
                  }
                $('#form_contact').attr('action',`{{url('admin/outsourcingpic/')}}/${response.data.id}`);
            }          
        }).fail(function(response){
            var response = response.responseJSON;
            $('#box-menu .overlay').addClass('d-none');
            $.gritter.add({
                title: 'Error!',
                text: response.message,
                class_name: 'gritter-error',
                time: 1000,
            });
        })	
      });
      $(document).on('click','.deletecontact',function(){
          var id = $(this).data('id');
          bootbox.confirm({
        buttons: {
          confirm: {
            label: '<i class="fa fa-check"></i>',
            className: 'btn-{{config('configs.app_theme')}}'
          },
          cancel: {
            label: '<i class="fa fa-undo"></i>',
            className: 'btn-default'
          },
        },
        title:'Menghapus Data PIC?',
        message:'Data yang telah dihapus tidak dapat dikembalikan',
        callback: function(result) {
            if(result) {
              var data = {
                              _token: "{{ csrf_token() }}",
                              id: id
                          };
              $.ajax({
                url: `{{url('admin/outsourcingpic')}}/${id}`,
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
                      dataTableContact.ajax.reload( null, false );
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

      dataTableDocument = $('#table-document').DataTable( {
          stateSave:true,
          processing: true,
          serverSide: true,
          filter:false,
          info:false,
          lengthChange:true,
          responsive: true,
          order: [[ 6, "asc" ]],
          ajax: {
              url: "{{route('outsourcingdocument.read')}}",
              type: "GET",
              data:function(data){
                  var name = $('#form-search').find('input[name=name]').val();
                  data.name = name;
                  data.outsourcing_id = {{$outsourcing->id}};
              }
          },
          columnDefs:[
              {
                  orderable: false,targets:[0]
              },
              { className: "text-right", targets: [0] },
              { className: "text-center", targets: [6] },
              { render: function ( data, type, row ) {
                  return `<div class="dropdown">
                      <button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                      <i class="fa fa-bars"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-right">
                          <li><a class="dropdown-item editdocument" href="#" data-id="${row.id}"><i class="fa fa-pencil-alt mr-2"></i> Edit</a></li>
                          <li><a class="dropdown-item deletedocument" href="#" data-id="${row.id}"><i class="fa fa-trash mr-2"></i> Delete</a></li>
                      </ul>
                      </div>`
              },targets: [6]
              }
          ],
          columns: [
              { data: "no" },
              { data: "category" },
              { data: "phone" },
              { data: "name" },
              { data: "file" },
              { data: "description"},
              { data: "id" },
          ]
      });
      $('.add_document').on('click',function(){
        $('#form_document')[0].reset();
        $('#form_document').attr('action',"{{route('outsourcingdocument.store')}}");
        $('#form_document input[name=_method]').attr('value','POST');
        $('#form_document input[name=outsourcing_id]').attr('value',{{ $outsourcing->id }});
        $('#form_document select[name=category]').select2('val','');
        $('#form_document input[name=phone]').attr('value','');
        $('#form_document input[name=name]').attr('value','');
        $('#form_document input[name=file]').attr('value','');
        $('#form_document textarea[name=description]').html('');
        $('#form_document .invalid-feedback').each(function () { $(this).remove(); });
        $('#form_document .form-group').removeClass('has-error').removeClass('has-success');
        $('#add_document .modal-title').html('Add Legalitas');
        $('#add_document').modal('show');
      });
       $(document).on('click','.editdocument',function(){
        var id = $(this).data('id');
        $.ajax({
            url:`{{url('admin/outsourcingdocument')}}/${id}/edit`,
            method:'GET',
            dataType:'json',
            beforeSend:function(){
                $('#box-menu .overlay').removeClass('d-none');
            },
        }).done(function(response){
            $('#box-menu .overlay').addClass('d-none');
            if(response.status){
                $('#add_document .modal-title').html('Edit Legalitas');
                $('#add_document').modal('show');
                $('#form_document')[0].reset();
                $('#form_document .invalid-feedback').each(function () { $(this).remove(); });
                $('#form_document .form-group').removeClass('has-error').removeClass('has-success');
                $('#form_document input[name=_method]').attr('value','PUT');
                $('#form_document input[name=outsourcing_id]').attr('value',{{$outsourcing->id}});
                $('#form_document input[name=phone]').attr('value',response.data.phone);
                $('#form_document input[name=name]').attr('value',response.data.name);
                $('#form_document input[name=file]').attr('value',response.data.file);
                $('#form_document select[name=category]').select2('val',response.data.category);
                $('#form_document textarea[name=description]').html(response.data.description);
                $('#form_document').attr('action',`{{url('admin/outsourcingdocument/')}}/${response.data.id}`);
            }          
        }).fail(function(response){
            var response = response.responseJSON;
            $('#box-menu .overlay').addClass('d-none');
            $.gritter.add({
                title: 'Error!',
                text: response.message,
                class_name: 'gritter-error',
                time: 1000,
            });
        })	
      });
      $(document).on('click','.deletedocument',function(){
          var id = $(this).data('id');
          bootbox.confirm({
        buttons: {
          confirm: {
            label: '<i class="fa fa-check"></i>',
            className: 'btn-{{config('configs.app_theme')}}'
          },
          cancel: {
            label: '<i class="fa fa-undo"></i>',
            className: 'btn-default'
          },
        },
        title:'Menghapus Data Dokumen?',
        message:'Data yang telah dihapus tidak dapat dikembalikan',
        callback: function(result) {
            if(result) {
              var data = {
                              _token: "{{ csrf_token() }}",
                              id: id
                          };
              $.ajax({
                url: `{{url('admin/outsourcingdocument')}}/${id}`,
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
                      dataTableDocument.ajax.reload( null, false );
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
      
      dataTableProduct = $('#table-product').DataTable({
            stateSave: true,
            processing: true,
            serverSide: true,
            filter: false,
            info: false,
            lengthChange: true,
            responsive: true,
            order: [
                [4, "asc"]
            ],
            ajax: {
                url: "{{route('outsourcingemployee.read')}}",
                type: "GET",
                data: function (data) {
                    var name = $('#form-search').find('input[name=name]').val();
                    data.name = name;
                    data.outsourcing_id = {{$outsourcing->id}};
                }
            },
            columnDefs: [{
                    orderable: false,
                    targets: [0]
                },
                {
                    className: "text-right",
                    targets: [0]
                },
    
            ],
            columns: [{
                    data: "no"
                },
                {
                    data: "name"
                },
                {
                    data: "nik"
                },
                {
                    data: "title_name"
                },
                {
                    data: "department_name"
                },
            ]
      });

      $("#file").fileinput({
          browseClass: "btn btn-{{config('configs.app_theme')}}",
          showRemove: false,
          showUpload: false,
          allowedFileExtensions: ["png", "jpg", "jpeg", "pdf"],
          dropZoneEnabled: false,
          initialPreviewAsData: false,
          initialPreviewFileType: 'image',
          theme:'explorer-fas'
      });
  });
</script>
@endpush
