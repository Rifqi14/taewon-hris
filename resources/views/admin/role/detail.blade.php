@extends('admin.layouts.app')

@section('title', 'Detail Role')
@section('stylesheets')
<style type="text/css">
    .overlay-wrapper {
        position: relative;
    }

</style>
@endsection
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('role.index')}}">Role</a></li>
<li class="breadcrumb-item active">Detail</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-{{config('configs.app_theme')}} card-outline">
            <div class="card-header">
                <h3 class="card-title">Detail Role</h3>
                <!-- tools box -->
                <div class="pull-right card-tools">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                            class="fa fa-reply"></i></a>
                </div>
                <!-- /. tools -->
            </div>
            <div class="card-body">
                <form id="form" action="#" class="form-horizontal" method="post" autocomplete="off">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="put">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="name" class="col-sm-2 control-label text-right">Name <b
                                    class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <p class="form-control-static text-left">{{$role->name}}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="display_name" class="col-sm-2 control-label text-right">Display Name <b
                                    class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <p class="form-control-static text-left">{{$role->display_name}}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description" class="col-sm-2 control-label text-right">Description</label>
                            <div class="col-sm-6">
                                <p class="form-control-static text-left">{{$role->description}}</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- Custom Tabs -->
        <div class="card card-{{config('configs.app_theme')}} card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs">
                    <li class="nav-item"><a class="nav-link active" href="#menuweb" data-toggle="tab">Menu Web</a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="menuweb">
                    <div class="card-body">
                        <div class="overlay-wrapper">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="text-align:center" width="10">#</th>
                                        <th width="250">Menu Name</th>
                                        <th width="50" style="text-align:center">#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $no = 1;
                                    @endphp
                                    @foreach($rolemenus as $rolemenu)
                                    <tr>
                                        <td style="text-align:center">{{$no++}}</td>
                                        <td>
                                            @if($rolemenu->parent_id)
                                            &nbsp;&nbsp;&nbsp;&nbsp;{{$rolemenu->menu_name}}
                                            @else
                                            <b>{{$rolemenu->menu_name}}</b>
                                            @endif
                                        </td>
                                        <td style="text-align:center"><input type="checkbox" value="{{$rolemenu->id}}"
                                                class="i-checks updatemenu" @if($rolemenu->role_access) checked @endif
                                            autocomplete="off"/></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="overlay d-none">
                                <i class="fa fa-2x fa-sync-alt fa-spin"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
        $('.updatemenu').on('ifChanged', function () {
            $.ajax({
                url: "{{url('admin/rolemenu/update')}}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: this.value,
                    role_access: this.checked ? 1 : 0
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $('#menuweb .overlay').removeClass('d-none');
                }
            }).done(function (response) {
                $('#menuweb .overlay').addClass('d-none');
                if (response.status) {
                    $.gritter.add({
                        title: 'Success!',
                        text: response.message,
                        class_name: 'gritter-success',
                        time: 1000,
                    });
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
                var response = response.responseJSON;
                $('#menuweb  .overlay').addClass('hidden');
                $.gritter.add({
                    title: 'Error!',
                    text: response.message,
                    class_name: 'gritter-error',
                    time: 1000,
                });
            })
        });
    });

</script>
@endpush
