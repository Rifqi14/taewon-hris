@extends('admin.layouts.app')

@section('title', 'Edit Principle')
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('principle.index')}}">Principle</a></li>
<li class="breadcrumb-item active">Edit</li>
@endpush
@section('stylesheets')
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer/theme.min.css')}}" rel="stylesheet">
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
                <h3 class="card-title">Edit Principle</h3>
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
                <form id="form" action="{{ route('principle.update',['id'=>$principle->id]) }}" class="form-horizontal" method="post"
                    autocomplete="off">
                    {{ csrf_field() }}
                    {{ method_field('put') }}
                    <div class="well well-sm">
                        <div class="form-group row">
                            <label for="name" class="col-sm-2 col-form-label">Nama <b class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Nama"
                                    required value="{{ $principle->name }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="phone" class="col-sm-2 col-form-label">Phone <b class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Telepon"
                                    required value="{{ $principle->phone }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-6">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{ $principle->email }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address" class="col-sm-2 col-form-label">Alamat <b
                                    class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <textarea name="address" id="address" class="form-control"
                                    placeholder="Alamat" required>{{ $principle->address }}</textarea>
                                <div id="map"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="latitude" class="col-sm-2 col-form-label">Latitude</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="latitude" name="latitude"
                                    placeholder="Latitude" value="{{ $principle->latitude }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="longtitude" class="col-sm-2 col-form-label">Longitude</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="longitude" name="longitude"
                                    placeholder="Longitude" value="{{ $principle->longitude }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="image" class="col-sm-2 col-form-label">Photo</label>
                            <div class="col-sm-6">
                                <input type="file" class="form-control" name="image" id="image" accept="image/*"/>
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
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/fas/theme.min.js')}}"></script>
<script type="text/javascript"
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDk0A3uPdfOld8ZG1ibIZRaEktd-2Kv33E"></script>
<script>
    var map, geocoder, marker, infowindow;
    $(document).ready(function () {
        var address = $('#address').val(),
            lat = $('#latitude').val()||-7.217416,
            long = $('#longitude').val()||112.72990470000002;
        var options = {
            zoom: 10,
            center: new google.maps.LatLng(-7.217416, 112.72990470000002),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        map = new google.maps.Map(document.getElementById('map'), options);
        setCoordinates(address,lat,long);

        // Mengambil referensi ke form HTML
        $("#form textarea[name=address]").keyup(function () {
            address = $(this).val();
            getCoordinates(address);
        });
        $('.select2').select2({
            allowClear: true
        });

        //image input
        $("#image").fileinput({
          browseClass: "btn btn-danger",
          showRemove: false,
          showUpload: false,
          allowedFileExtensions: ["png", "jpg"],
          dropZoneEnabled: false,
          initialPreview: '<img src="{{asset($principle->image)}}" class="kv-preview-data file-preview-image">',
          initialPreviewAsData: false,
          initialPreviewFileType: 'image',
          initialPreviewConfig: [
          {caption: "{{$principle->image}}", downloadUrl: "{{asset($principle->image)}}", size:"{{ File::size(public_path($principle->image))}}",url: false}
          ],
          theme:'fas'
        });

        $(document).on("change", "#image", function () {
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
                if (element.is(':file')) {
                    error.insertAfter(element.parent().parent().parent());
                } else
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else
                if (element.attr('type') == 'checkbox') {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function () {
                $.ajax({
                    url: $('#form').attr('action'),
                    method: 'post',
                    data: new FormData($('#form')[0]),
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    beforeSend: function () {
                        $('.overlay').removeClass('hidden');
                    }
                }).done(function (response) {
                    $('.overlay').addClass('hidden');
                    if (response.status) {
                        document.location = response.results;
                    } else {
                        $.gritter.add({
                            title: 'Warning!',
                            text: response.message,
                            class_name: 'gritter-warning',
                            time: 1000,
                        });
                    }
                    return;
                }).fail(function (response) {
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
        if (!geocoder) {
            geocoder = new google.maps.Geocoder();
        }

        // Membuat objek GeocoderRequest
        var geocoderRequest = {
            latLng: latLng
        }

        // Membuat rekues Geocode
        geocoder.geocode(geocoderRequest, function (results, status) {

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
                google.maps.event.addListener(marker, 'drag', function () {

                    updateMarkerPosition(marker.getPosition());
                });

                // membuat konten InfoWindow ke alamat
                // dan posisi yang ditemukan
                var content = '<strong>' + results[0].formatted_address + '</strong><br />';
                content += 'Lat: ' + results[0].geometry.location.lat() + '<br />';
                content += 'Lng: ' + results[0].geometry.location.lng();

                $('#form input[name=latitude]').attr('value', results[0].geometry.location.lat());
                $('#form input[name=longitude]').attr('value', results[0].geometry.location.lng());
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
        if (!geocoder) {
            geocoder = new google.maps.Geocoder();
        }

        // Membuat objek GeocoderRequest
        var geocoderRequest = {
            address: address
        }

        // Membuat rekues Geocode
        geocoder.geocode(geocoderRequest, function (results, status) {

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
                google.maps.event.addListener(marker, 'drag', function () {

                    updateMarkerPosition(marker.getPosition());
                });

                // membuat konten InfoWindow ke alamat
                // dan posisi yang ditemukan
                var content = '<strong>' + results[0].formatted_address + '</strong><br />';
                content += 'Lat: ' + results[0].geometry.location.lat() + '<br />';
                content += 'Lng: ' + results[0].geometry.location.lng();

                $('#form input[name=latitude]').attr('value', results[0].geometry.location.lat());
                $('#form input[name=longitude]').attr('value', results[0].geometry.location.lng());
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
