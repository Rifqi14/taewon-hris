@extends('admin.layouts.app')

@section('title', 'Import SPL | Surat Pengajuan Lembur')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer/theme.min.css')}}" rel="stylesheet">
<style type="text/css">
  .overlay-wrapper {
    position: relative;
  }
</style>
@endsection
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('spl.index')}}">SPL</a></li>
<li class="breadcrumb-item active">Import SPL (Surat Pengajuan Lembur)</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card card-{{ config('configs.app_theme') }} card-outline" id="attendance-preview">
      <div class="card-header">
        <h3 class="card-title">Import Preview</h3>
        <!-- tools card -->
        <div class="pull-right card-tools">
          <a href="{{ asset('import/Spl.xlsx') }}" class="btn btn-{{ config('configs.app_theme') }} text-white btn-sm" data-toggle="tooltip" title="Download Template">
            <i class="fa fa-download"></i>
          </a>
          <a onclick="addImport()" class="btn btn-{{ config('configs.app_theme') }} text-white btn-sm"
            data-toggle="tooltip" title="Import data">
            <i class="fa fa-file-import"></i>
          </a>
          <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Simpan"><i
              class="fa fa-save"></i></button>
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
              class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="card-body">
        <form id="form" action="{{route('spl.storemass')}}">
        </form>
        <table class="table table-striped table-bordered" style="width:100%" id="table-item">
          <thead>
            <tr>
                <th width="100">Date</th>
                <th width="100">Employee Name</th>
                <th width="100">NIK Taewon</th>
                <th width="50">Start Date</th>
                <th width="50">Start Time</th>
                <th width="100">Finish Date</th>
                <th width="100">Finish Time</th>
                <th width="50">Error Message</th>
                <th width="50">#</th>
            </tr>
          </thead>
        </table>
      </div>
      <div class="overlay d-none">
        <i class="fa fa-refresh fa-spin"></i>
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
              <div class="col-sm-12">
                <div class="form-group">
                  <label class="control-label" for="file">File Excel</label>
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
        <i class="fa fa-refresh fa-spin"></i>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer/theme.min.js')}}"></script>
<script type="text/javascript">
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
            this.date,
            this.employee_name,
            this.nik,
            this.start_date,
            this.start_time,
            this.finish_date,
            this.finish_time,
            this.error_message,
            this.status,
          ]).draw(false);
          count++;
      });
  }
  $(function(){
      $("#file").fileinput({
          browseClass: "btn btn-{{ config('configs.app_theme') }}",
          showRemove: false,
          showUpload: false,
          allowedFileExtensions: ["xlsx"],
          dropZoneEnabled: false,
          theme:'explorer'
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
          lengthChange:false,
          autoWidth:false,
          paging:false,
          order: [[ 0, "asc" ]],
          columnDefs: [
              {
                  orderable: false,targets:[0,1,2,3,4,5,6,7]
              },
              { render: function ( data, type, row ) {
                  if (row[8] == 1) {
                    return '<span class="badge badge-success"><i class="fa fa-check"></i></span>';
                  } else {
                    return '<span class="badge badge-danger"><i class="fa fa-times"></i></span>';
                  }
                },targets: [8]
              },
              { className: "text-center", targets: [8] },
          ],
      });

      $("#form-import").validate({
          errorElement: 'span',
          errorClass: 'help-block',
          focusInvalid: false,
          highlight: function (e) {
              $(e).closest('.form-group').removeClass('has-success').addClass('has-error');
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
                  url:"{{route('spl.preview')}}",
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
                  items = {};
                  $.each(response.data,function(){
                      items[this.index] = this;
                  });
                  table_item.clear().draw();
                  loadItem(table_item);
                  console.log(items);
              })
          }
      });
      $("#form").validate({
          errorElement: 'span',
          errorClass: 'help-block',
          focusInvalid: false,
          highlight: function (e) {
              $(e).closest('.form-group').removeClass('has-success').addClass('has-error');
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
              var spls =[];
              $.each(items, function() {
                spls.push(this);
              });
              $.ajax({
                  url:$('#form').attr('action'),
                  dataType: 'json',
                  type:'POST',
                  data: {
                      _token: "{{ csrf_token() }}",
                      spls: JSON.stringify(spls)
                  },
                  beforeSend:function(){
                      $('#attendance-preview .overlay').removeClass('d-none');
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