@extends('admin.layouts.app')

@section('title', 'Detail Principle')
@section('stylesheets')
<style type="text/css">
    .overlay-wrapper{
      position:relative;
    }
    #map {
        height: 370px;
        border: 1px solid #CCCCCC;
    }
</style>
@endsection
@push('breadcrump')
    <li class="breadcrumb-item"><a href="{{route('principle.index')}}">Principle</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail Principle</h3>
            </div>
            <div class="card-body card-profile text-center">
                <img class="profile-user-img img-responsive img-circle" src="{{asset($principle->image)}}" alt="User profile picture">
                <h3 class="profile-username">{{$principle->name}}</h3>
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item d-flex">
                        <b class="mr-auto">Telephone</b> <span class="pull-right">{{$principle->phone}}</span>
                    </li>
                    <li class="list-group-item d-flex">
                        <b class="mr-auto">Email</b> <span class="pull-right">{{$principle->email}}</span>
                    </li>
                    <li class="list-group-item d-flex">
                        <b class="mr-auto">Alamat</b> <span class="pull-right" id="address">{{ $principle->address }}</span>
                    </li>
                    <li class="list-group-item d-flex">
                        <b class="mr-auto">latitude</b> <span class="pull-right" id="lat">{{$principle->latitude}}</span>
                    </li>
                    <li class="list-group-item d-flex">
                        <b class="mr-auto">Longitude</b> <span class="pull-right" id="long">{{$principle->longitude}}</span>
                    </li>
                    <li class="list-group-item d-flex">
                        <b class="mr-auto">Dibuat</b> <span class="pull-right" >{{$principle->created_at}}</span>
                    </li>
                </ul>
            </div>
            <div class="overlay d-none">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div id="map"></div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript"
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDk0A3uPdfOld8ZG1ibIZRaEktd-2Kv33E"></script>
<script>
    var map, geocoder, marker, infowindow;
    $(document).ready(function () {
        var lat = $("#lat").html()||-7.217416,
            long = $("#long").html()||112.72990470000002,
            address = $("#address").html(),

            options = {
                zoom: 15,
                center: new google.maps.LatLng(lat, long),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

        map = new google.maps.Map(document.getElementById('map'), options);

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
      draggable:false,
          });
        }

        // Menentukan posisi marker ke lokasi returned location

    marker.setPosition(new google.maps.LatLng(latitude, longitude));

        // Mengecek apakah terdapat InfoWindow object
        if (!infowindow) {
          // Membuat InfoWindow baru
          infowindow = new google.maps.InfoWindow();
        }
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
</script>
@endpush
