@extends('admin.layouts.app')

@section('title', 'Detail Pegawai')
@section('stylesheets')
<style type="text/css">
    #map {
        height: 370px;
        border: 1px solid #CCCCCC;
    }

</style>
@endsection
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('employee.index')}}">Pegawai</a></li>
<li class="breadcrumb-item active">Detail</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail Pegawai</h3>
                <div class="pull-right card-tools">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-danger text-white" title="Kembali"><i
                            class="fa fa-reply"></i></a>
                </div>
            </div>
            <div class="box-body box-profile">
                <table class="table">
                    <tr>
                        <td><strong>Jabatan</strong></td>
                        <td class="text-right">{{$employee->title_name}}</td>
                    </tr>
                    <tr>
                        <td><strong>NID</strong></td>
                        <td class="text-right">{{$employee->nid}}</td>
                    </tr>
                    <tr>
                        <td width="100"><strong>Nama</strong></td>
                        <td width="150" class="text-right">{{$employee->name}}</td>
                    </tr>
                    <tr>
                        <td><strong>Tipe</strong></td>
                        <td class="text-right">{{$employee->type}}</td>
                    </tr>
                    <tr>
                        <td><strong>Tempat & Tgl Lahir</strong></td>
                        <td class="text-right">{{$employee->region->name}} , {{ Carbon\Carbon::parse($employee->birth_date)->format('d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Umur</strong></td>
                        <td class="text-right"><span class="label label-success">{{ Carbon\Carbon::parse($employee->birth_date)->age }} Tahun </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Alamat</strong></td>
                        <td class="text-right" id="address">{{$employee->address}}</td>
                    </tr>
                    <tr>
                        <td><strong>Latitude</strong></td>
                        <td class="text-right" id="lat">{{$employee->latitude}}</td>
                    </tr>
                    <tr>
                        <td><strong>Longitude</strong></td>
                        <td class="text-right" id="long">{{$employee->longitude}}</td>
                    </tr>
                </table>

            </div>
        </div>
    </div>
    <div class="col-lg-6">
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
