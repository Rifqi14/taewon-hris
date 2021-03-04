@extends('admin.layouts.app')

@section('title', 'Create Outsourcing')
@push('breadcrump')
    <li class="breadcrumb-item"><a href="{{route('outsourcing.index')}}">Outsourcing</a></li>
    <li class="breadcrumb-item active">Create</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<!-- <style type="text/css">
  #map {
       height: 300px;
       border: 1px solid #CCCCCC;
     }
</style> -->
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card card-{{config('configs.app_theme')}} card-outline" style="background-color:#F4F6F9;">
        <div class="card-header"  style="background-color:#FFFFFF ;">
          <h3 class="card-title">Create Outsourcing</h3>
          <!-- tools box -->
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="Simpan"><i class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
          </div>
          <!-- /. tools -->
        </div>
    
        <form id="form" action="{{route('outsourcing.store')}}" class="form-horizontal" method="post" autocomplete="off">
            {{ csrf_field() }}
            <div class="card">
                <div class="card-body">
                   <div class="form-group row">
                    <label for="name" class="col-sm-2 control-label">Code <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" id="code" name="code" placeholder="Code" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="name" class="col-sm-2 control-label">Name <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="email" class="col-sm-2 control-label">Email <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                      <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="no_tlpn" class="col-sm-2 control-label">Phone <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                      <input type="phone" class="form-control" id="no_tlpn" name="no_tlpn" placeholder="No.Telpon" required>
                    </div>
                  </div>
                  <div class="form-group row">
                  <label for="workgroup_id" class="col-md-2 form-label">Workgroup Combination<b class="text-danger">*</b></label>
                    <div class="col-md-6">
                      <input type="text" id="workgroup_id" name="workgroup_id" class="form-control" placeholder="Workgroup Combination">
                    </div>
                </div>
                  <div class="form-group row">
                      <label for="status" class="col-sm-2 col-form-label">Status <b class="text-danger">*</b></label>
                      <div class="col-sm-6">
                        <select id="status" name="status" class="form-control select2" placeholder="Pilih Status"
                          required>
                            <option value="1">Active</option>
                            <option value="0">Tidak Active</option>
                        </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="app_logo" class="col-sm-2 control-label">Image</label>
                    <div class="col-sm-6">
                      <input type="file" class="form-control" name="image" id="picture" accept="image/*"/>
                    </div>
                  </div>
                </div>
            </div>
            <div class="card">
              <div class="card-header">
                <div class="card-title">Outsourcing Address</div>
              </div>
              <div class="card-body">
                <div class="form-group row">
                  <label for="district_id" class="col-sm-2 control-label">Province <b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" id="province_id" name="province_id" placeholder="Province" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="district_id" class="col-sm-2 control-label">City<b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" id="region_id" name="region_id" placeholder="City" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="district_id" class="col-sm-2 control-label">District <b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" id="district_id" name="district_id" placeholder="Sub District" required>
                  </div>
                </div>
                <!-- <div class="form-group row">
                  <label for="village_id" class="col-sm-2 control-label">Village<b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" id="village_id" name="village_id" placeholder="Village" required>
                  </div>
                </div> -->
                <div class="form-group row">
                  <label for="kode_pos" class="col-sm-2 control-label">Kode Pos</label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" id="kode_pos" name="kode_pos" placeholder="Kode Pos">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="name" class="col-sm-2 control-label">Address <b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <textarea name="address" id="address" class="form-control" required placeholder="Address"></textarea>
                    <div id="map"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="card-header">
                <div class="card-title">PIC Outsourcing</div>
              </div>
              <div class="card-body">
                <div class="form-group row">
                  <label for="pic_name" class="col-sm-2 control-label">Name<b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" id="pic_name" name="pic_name" placeholder="Name" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="pic_phone" class="col-sm-2 control-label">Phone <b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" id="pic_phone" name="pic_phone" placeholder="Phone" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="pic_email" class="col-sm-2 control-label">Email<b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" id="pic_email" name="pic_email" placeholder="Email" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="pic_address" class="col-sm-2 control-label">Address<b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <textarea name="pic_address" id="pic_address" class="form-control" required placeholder="Address"></textarea>
                  </div>
                </div>
                <div class="form-group row">
                    <label for="pic_category" class="col-sm-2 col-form-label">Category<b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                       <select id="type_g" name="pic_category" class="form-control select2" placeholder="Select Category" required>
                        <option value=""></option>
                        @foreach(config('enums.category_pic') as $key => $value))
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                       </select>
                       <!-- <input type="text" class="form-control" name="pic_category"> -->
                    </div>
                  </div>
              </div>
            </div>
        </form>
    </div>
  </div>
      <div class="overlay d-none">
        <i class="fa fa-2x fa-sync-alt fa-spin"></i>
      </div> 
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDk0A3uPdfOld8ZG1ibIZRaEktd-2Kv33E"></script>
<script>
  var map, geocoder, marker, infowindow;
  $(document).ready(function(){
      var options = {
        zoom: 10,
        center: new google.maps.LatLng(-7.217416, 112.72990470000002),
        mapTypeId: google.maps.MapTypeId.ROADMAP
      };

      map = new google.maps.Map(document.getElementById('map'), options);

      // Mengambil referensi ke form HTML
      $( "#form textarea[name=address]" ).keyup(function() {
        address = $(this).val();
        getCoordinates(address);
      });
      $('.select2').select2();
     
      $("#picture").fileinput({
       browseClass: "btn btn-{{config('configs.app_theme')}}",
          showRemove: false,
          showUpload: false,
          allowedFileExtensions: ["png", "jpg", "jpeg"],
          dropZoneEnabled: false,
          initialPreviewAsData: false,
          initialPreviewFileType: 'image',
          initialPreviewConfig: [
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
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
        $('#region_id').select2('val','');
        $('#district_id').select2('val','');
        $('#village_id').select2('val','');
      });
      $(document).on("change", "#region_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
        $('#district_id').select2('val','');
        $('#village_id').select2('val','');
      });
      $(document).on("change", "#district_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
        $('#village_id').select2('val','');
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
            $(document).on("change", "#workgroup_id", function () {
                if (!$.isEmptyObject($('#form').validate().submitted)) {
                    $('#form').validate().form();
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
  });

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

		  $('#form input[name=latitude]').attr('value',results[0].geometry.location.lat());
		  $('#form input[name=longitude]').attr('value',results[0].geometry.location.lng());
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

		  $('#form input[name=latitude]').attr('value',results[0].geometry.location.lat());
		  $('#form input[name=longitude]').attr('value',results[0].geometry.location.lng());
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
