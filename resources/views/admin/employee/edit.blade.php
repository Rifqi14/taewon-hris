@extends('admin.layouts.app')

@section('title', 'Ubah Pegawai')
@push('breadcrump')
    <li class="breadcrumb-item"><a href="{{route('employee.index')}}">Pegawai</a></li>
    <li class="breadcrumb-item active">Ubah</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
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
          <h3 class="card-title">Ubah Pegawai</h3>
          <!-- tools box -->
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-danger text-white" title="Simpan"><i class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
          </div>
          <!-- /. tools -->
        </div>
        <div class="card-body">
            <form id="form" action="{{route('employee.update',['id'=>$employee->id])}}" class="form-horizontal" method="post" autocomplete="off">
               {{ csrf_field() }}
               <input type="hidden" name="_method" value="put">
               <div class="well well-sm">
                <div class="form-group row">
                  <label for="name" class="col-sm-2 col-form-label">Jabatan <b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" id="title_id" name="title_id" data-placeholder="Pilih Jabatan" required>
                  </div>
                </div>
                  <div class="form-group row">
                    <label for="nid" class="col-sm-2 col-form-label">NID <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                    <input type="text" class="form-control" id="nid" name="nid" placeholder="NID" value="{{$employee->nid}}" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Nama <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" id="name" name="name" placeholder="Nama" value="{{$employee->name}}" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Tipe <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                       <select id="type" name="type" class="form-control select2" placeholder="Pilih Tipe" required>
                          <option value=""></option>
                          <option value="permanent" @if($employee->type == 'permanent') selected @endif>Pegawai Tetap</option>
                          <option value="internship" @if($employee->type == 'internship') selected @endif>Magang</option>
                       </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Jenis Kelamin <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                       <select id="type" name="gender" class="form-control select2" placeholder="Pilih Jenis Kelamin" required>
                          <option value=""></option>
                          <option value="m" @if($employee->gender == 'm') selected @endif>Laki - Laki</option>
                          <option value="f" @if($employee->gender == 'f') selected @endif>Perempuan</option>
                       </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Tempat Lahir <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" placeholder="Tempat Lahir" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Tanggal Lahir <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                    <input type="text" class="form-control" id="birth_date" name="birth_date" placeholder="Tanggal Lahir" value="{{$employee->birth_date}}" required>
                    </div>
                  </div>
                </div>
                <div class="well well-sm">
                  <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Telepon <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" id="phone" name="phone" placeholder="Telepon" value="{{$employee->phone}}" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Alamat <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                    <textarea name="address" id="address" class="form-control" required placeholder="Alamat">{{$employee->address}}</textarea>
                      <div id="map"></div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Latitude</label>
                    <div class="col-sm-6">
                    <input type="text" class="form-control" id="latitude" name="latitude" value="{{$employee->latitude}}" placeholder="Latitude">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Longitude</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" id="longitude" name="longitude" value="{{$employee->longitude}}"placeholder="Longitude">
                    </div>
                  </div>
                </div>
              </form>
        </div>
        <div class="overlay d-none">
          <i class="fa fa-refresh fa-spin"></i>
        </div>
    </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDk0A3uPdfOld8ZG1ibIZRaEktd-2Kv33E"></script>
<script>
  var map, geocoder, marker, infowindow;
  $(document).ready(function(){
    var address = $('#address').val(),
            lat = $('#latitude').val()||-7.217416,
            long = $('#longitude').val()||112.72990470000002;
    var options = {
      zoom: 10,
      center: new google.maps.LatLng(lat, long),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    map = new google.maps.Map(document.getElementById('map'), options);
    setCoordinates(address,lat,long);
    // Mengambil referensi ke form HTML
    $( "#form textarea[name=address]" ).keyup(function() {
      address = $(this).val();
      getCoordinates(address);
    });
      $("input[name=nid]").inputmask("Regex", { regex: "[A-Za-z0-9]*" });
      $('.select2').select2({
        allowClear:true
      });
      $('input[name=birth_date]').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
      })
      $('input[name=birth_date]').on('change', function(){
        if (!$.isEmptyObject($(this).closest("form").validate().submitted)) {
          $(this).closest("form").validate().form();
        }
      });
      $( "#title_id" ).select2({
        ajax: {
          url: "{{route('title.select')}}",
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
      $("#title_id").select2('data',{id:{{$employee->title_id}},text:'{{$employee->title_name}}'}).trigger('change');
      $(document).on("change", "#title_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
        // $('#district_id').select2('val','');
      });
      $( "#place_of_birth" ).select2({
        ajax: {
          url: "{{route('region.select')}}",
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

      $("#place_of_birth").select2('data',{id:{{$employee->place_of_birth}},text:'{{$employee->region->type.' '.$employee->region->name}}'}).trigger('change');
      $(document).on("change", "#place_of_birth", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
        // $('#district_id').select2('val','');
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
