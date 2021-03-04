@extends('admin.layouts.app')

@section('title', 'Tambah Pelanggan')
@push('breadcrump')
    <li class="breadcrumb-item"><a href="{{route('customer.index')}}">Pelanggan</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
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
    <div class="card" style="background-color:#F4F6F9;">
        <div class="card-header"  style="background-color:#FFFFFF ;">
          <h3 class="card-title">Tambah Pelanggan</h3>
          <!-- tools box -->
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-danger text-white" title="Simpan"><i class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
          </div>
          <!-- /. tools -->
        </div>
    
        <form id="form" action="{{route('customer.store')}}" class="form-horizontal" method="post" autocomplete="off">
            {{ csrf_field() }}
            <div class="card">
                <div class="card-body">
                   <div class="form-group row">
                    <label for="name" class="col-sm-2 control-label">Kode <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" id="code" name="code" placeholder="Kode" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="name" class="col-sm-2 control-label">Nama <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" id="name" name="name" placeholder="Nama" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="name" class="col-sm-2 control-label">Group Pelanggan <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" id="customergroup_id" name="customergroup_id" data-placeholder="Pilih Grup Pelanggan" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="email" class="col-sm-2 control-label">Email <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                      <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="app_logo" class="col-sm-2 control-label">Foto</label>
                    <div class="col-sm-6">
                      <input type="file" class="form-control" name="picture" id="picture" accept="image/*"/>
                    </div>
                  </div>
                </div>
            </div>
            <div class="card">
              <div class="card-header">
                <div class="card-title">Alamat Pelanggan</div>
              </div>
              <div class="card-body">
                <div class="form-group row">
                  <label for="district_id" class="col-sm-2 control-label">Provinsi <b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" id="province_id" name="province_id" placeholder="Provinsi" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="district_id" class="col-sm-2 control-label">Kota<b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" id="region_id" name="region_id" placeholder="Kota" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="district_id" class="col-sm-2 control-label">Kabupaten <b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" id="district_id" name="district_id" placeholder="Kabupaten" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="name" class="col-sm-2 control-label">Alamat <b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <textarea name="address" id="address" class="form-control" required placeholder="Alamat"></textarea>
                    <div id="map"></div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="latitude" class="col-sm-2 control-label">Latitude</label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Latitude">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="longitude" class="col-sm-2 control-label">Longitude</label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Longitude">
                  </div>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="card-header">
                <div class="card-title">Contact Pelanggan</div>
              </div>
              <div class="card-body">
                <div class="form-group row">
                  <label for="contact_name" class="col-sm-2 control-label">Contact Nama<b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" id="contact_name" name="contact_name" placeholder="Contact Nama" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="contact_phone" class="col-sm-2 control-label">Contact Telepon <b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" id="contact_phone" name="contact_phone" placeholder="Contact Telepon" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="contact_email" class="col-sm-2 control-label">Contact Email<b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" id="contact_email" name="contact_email" placeholder="Contact Email" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="name" class="col-sm-2 control-label">Contact Alamat<b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <textarea name="contact_address" id="contact_address" class="form-control" required placeholder="Contact Alamat"></textarea>
                  </div>
                </div>
              </div>
            </div>
        </form>
    </div>
  </div>
      <div class="overlay d-none">
        <i class="fa fa-refresh fa-spin"></i>
      </div> 
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
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
      $("#picture").fileinput({
      browseClass: "btn btn-danger",
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
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
        $('#region_id').select2('val','');
        $('#district_id').select2('val','');
      });
      $(document).on("change", "#region_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
        $('#district_id').select2('val','');
      });
      $(document).on("change", "#district_id", function () {
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
