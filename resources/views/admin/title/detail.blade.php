@extends('admin.layouts.app')

@section('title', 'Detail Jabatan')

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('title.index')}}">{{ __('config.pos') }}</a></li>
<li class="breadcrumb-item active">{{ __('config.dtl') }}</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('config.detpos') }}</h3>
                <div class="pull-right card-tools">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="{{ __('config.prvious') }}"><i class="fa fa-reply"></i></a>
                </div>
            </div>
            <div class="card-body card-profile">
                <input type="hidden" name="title_id" value="{{ $title->id }}">
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item d-flex">
                        <b class="mr-auto">{{ __('config.dep') }}</b> <span class="pull-right">{{ $title->department->name }}</span>
                    </li>
                    <li class="list-group-item d-flex">
                        <b class="mr-auto">{{ __('config.name') }}</b> <span class="pull-right">{{ $title->name }}</span>
                    </li>
                    <li class="list-group-item d-flex">
                        <b class="mr-auto">{{ __('config.created') }}</b> <span class="pull-right">{{ $title->created_at }}</span>
                    </li>
                </ul>

            </div>
            <div class="overlay d-none">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="nav-tabs-custom tab-primary">
                <ul class="nav nav-tabs">
                    <li class="nav item"><a class="nav-link active" href="#subcategory" data-toggle="tab">{{ __('config.empdta') }}</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="subcategory">
                        <div class="overlay-wrapper">
                            <table class="table table-bordered table-striped" id="table-detail">
                                <thead>
                                    <tr>
                                        <th style="text-align:center" width="10">#</th>
                                        <th width="200">{{ __('config.name') }}</th>
                                        <th width="100">{{ __('config.nid') }}</th>
                                        <th width="100">{{ __('config.type') }}</th>
                                        <th width="100">{{ __('config.created') }}</th>
                                </thead>
                            </table>
                            <div class="overlay d-none">
                                <i class="fa fa-refresh fa-spin"></i>
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
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script>
    $(document).ready(function () {
        dataTable = $('#table-detail').DataTable({
            stateSave: true,
            processing: true,
            serverSide: true,
            filter: false,
            info: false,
            lengthChange: false,
            responsive: true,
            order: [
                [2, "asc"]
            ],
            ajax: {
                url: "{{url('admin/title/employee')}}",
                type: "GET",
                data:function(data){
                    data.title_id = {{$title->id}};
                }
            },
            columnDefs: [{
                    orderable: false,
                    targets: [0]
                },
                {
                    className: "text-right",
                    targets: [0]
                }
            ],
            columns: [
                {data: "no"},
                { data: "name" },
                { data: "nid" },
                { data: "type" },
                { data: "created_at" },
            ]
        });

    });

</script>
@endpush