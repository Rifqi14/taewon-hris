@extends('admin.layouts.app')

@section('title', 'Employee')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
    <li class="breadcrumb-item"><a href="{{route('employee.index')}}">Employee</a></li>
    <li class="breadcrumb-item active">Import</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-{{config('configs.app_theme')}} card-outline" id="employee-preview">
            <div class="card-header">
                <h3 class="card-title">Preview</h3>
                <!-- tools box -->
                <div class="pull-right card-tools">
                    <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}"
                        title="Save"><i class="fa fa-save"></i></button>
                    <a onclick="addImport()" class="btn btn-{{ config('configs.app_theme') }} text-white btn-sm"
                        data-toggle="tooltip" title="Import data" style="cursor: pointer;">
                        <i class="fa fa-file-import"></i>
                    </a>
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Back"><i
                            class="fa fa-reply"></i>
                    </a>
                </div>
                <!-- /. tools -->
            </div>
            <div class="card-body">
                <form id="form" action="{{route('employee.storemass')}}">
                </form>
                <table class="table table-striped table-bordered datatable" style="width:100%" id="table-item">
                    <thead>
                        <tr>
                            <th width="100">Name</th>
                            <th width="100">NIK</th>
                            <th width="100">Position</th>
                            <th width="100">Department</th>
                            <th width="100">Workgroup</th>
                            <th width="100">Grade</th>
                            <th width="100">PTKP</th>
                            <th width="100">Overtime</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="overlay d-none">
                <i class="fas fa-sync-alt fa-3x fa-spin"></i>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="select-file" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
      <div class="overlay-wrapper">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Choose File</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" id="form-import" action="#" method="post">
              {{ csrf_field() }}
              <div class="row">
                <div class="form-group col-12">
                  <label class="col-sm-12 control-label" for="file">File Excel</label>
                  <div class="col-sm-12">
                    <input type="file" class="form-control" id="file" name="file" required accept=".xlsx" />
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button form="form-import" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="Import"><i
                class="fa fa-file-import"></i></button>
          </div>
        </div>
        <div class="overlay d-none">
            <i class="fas fa-sync-alt fa-3x fa-spin"></i>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
<script type="text/javascript">
    function filter() {
        $('#add-filter').modal('show');
    }
    var items = {},count=0;
    function addImport(){
        $('#form-import')[0].reset();
        $('#form-import').find('.help-block').remove();
        $('#form-import .form-group').removeClass('has-error').removeClass('has-success');
        $('#select-file').modal('show');
    }

    function loadItem(table_item){
        count=0;
        $.each(items, function() {
            table_item.row.add([
                this.name,
                this.nik,
                this.position,
                this.department,
                this.workgroup,
                this.grade,
                this.ptkp,
                this.overtime,
            ]).draw(false);
            count++;
        });
    }

    $(function () {
        $("#file").fileinput({
            browseClass: "btn btn-{{ config('configs.app_theme') }}",
            showRemove: false,
            showUpload: false,
            allowedFileExtensions: ["xlsx"],
            dropZoneEnabled: false,
            theme:'explorer-fas'
        });
        $(document).on("change", "#file", function () {
            if (!$.isEmptyObject($('#form-import').validate().submitted)) {
                $('#form-import').validate().form();
            }
        });

        var table_item = $('#table-item').DataTable({
            responsive:true,
            filter:false,
            info:false,
            lengthChange:true,
            autoWidth:false,
            paging:true,
            order: [[ 0, "asc" ]],
            columnDefs: [
                {
                    orderable: false,targets:[0,1]
                },
            ],
        });

        $("#form-import").validate({
            errorElement: 'div',
            errorClass: 'invalid-feedback',
            focusInvalid: false,
            highlight: function (e) {
                $(e).closest('.form-group').removeClass('has-success').addClass('has-error was-validated');
            },

            success: function (e) {
                $(e).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(e).remove();
            },
            errorPlacement: function (error, element) {
                if(element.is(':file')) {
                    error.insertAfter(element.closest('.file-input'));
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
                    url:"{{route('employee.preview')}}",
                    method:'post',
                    data: new FormData($('#form-import')[0]),
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    beforeSend:function(){
                        $('#select-file .overlay').removeClass('d-none');
                    }
                }).done(function(response){
                    $('#select-file .overlay').addClass('d-none');
                    $("#select-file").modal('hide');
                    if(response.status){
                        items = {};
                        $.each(response.data,function(){
                            items[this.index] = this;
                        });
                        table_item.clear().draw();
                        loadItem(table_item);
                        console.log(items);
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
                }).fail(function (response) {
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

        $("#form").validate({
          errorElement: 'div',
          errorClass: 'invalid-feedback',
          focusInvalid: false,
          highlight: function (e) {
              $(e).closest('.form-group').removeClass('has-success').addClass('has-error was-validated');
          },
          success: function (e) {
              $(e).closest('.form-group').removeClass('has-error').addClass('has-success');
              $(e).remove();
          },
          errorPlacement: function (error, element) {
              if(element.is(':file')) {
                  error.insertAfter(element.parent());
              }else if(element.parent('.input-group').length) {
                  error.insertAfter(element.parent());
              }
              else{
                  error.insertAfter(element);
              }
          },
          submitHandler: function() {
              if (count == 0) {
                  $.gritter.add({
                      title: 'Warning',
                      text:  'No items have been added yet',
                      class_name: 'gritter-error',
                  });
                  return false;
              }
              var employee =[];
              $.each(items, function() {
                employee.push(this);
              });
              $.ajax({
                  url:$('#form').attr('action'),
                  dataType: 'json',
                  type:'POST',
                  data: {
                      _token: "{{ csrf_token() }}",
                      employee: JSON.stringify(employee)
                  },
                  beforeSend:function(){
                      $('#employee-preview .overlay').removeClass('d-none');
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
            });
          }
      });

    });
</script>
@endpush
