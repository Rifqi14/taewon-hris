@extends('admin.layouts.app')

@section('title', __('attendancelog.import'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<style type="text/css">
  .overlay-wrapper {
    position: relative;
  }
  .progress span {
      position: absolute;
      display: block;
      width: 100%;
      color: #000;
      font-weight:bold;

  }
</style>
@endsection
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('attendanceapproval.index')}}">{{ __('attendancelog.attenlog') }}</a></li>
<li class="breadcrumb-item active">{{ __('attendancelog.import') }}</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card card-{{ config('configs.app_theme') }} card-outline" id="attendance-preview">
      <div class="card-header">
        <h3 class="card-title">{{ __('attendancelog.preview') }}</h3>
        <!-- tools card -->
        <div class="pull-right card-tools">
          <a onclick="addImport()" class="btn btn-{{ config('configs.app_theme') }} text-white btn-sm" data-toggle="tooltip" title="{{ __('general.chs') }} {{ __('general.file') }}">
            <i class="fa fa-file-import"></i>
          </a>
          <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="{{ __('general.prvious') }}"><i class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="card-body">
        <form id="form" action="{{route('attendance.newstoremass')}}">
        </form>
        <div class="pull-right offset-8">
          <div class="row">
            <div class="col-2 col-form-label">
              <label>{{ __('attendancelog.period') }}</label>
            </div>
            <div class="col-5">
              <select class="form-control select2" name="month" id="month">
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">July</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
              </select>
            </div>
            <div class="col-5">
              <select name="year" class="form-control select2" id="year">
                @for ($i = date('Y'); $i >= 1990; $i--)
                <option value="{{ $i }}">{{ $i }}</option>
                @endfor
              </select>
            </div>
          </div>
        </div>
        <table class="table table-striped table-bordered" style="width:100%" id="table-item">
          <thead>
            <tr>
              <th width="100">{{ __('attendancelog.employid') }}</th>
              <th width="100">{{ __('attendancelog.firstnm') }}</th>
              <th width="100">{{ __('attendancelog.lastnm') }}</th>
              <th width="50">Area</th>
              <th width="100">{{ __('attendancelog.device') }}</th>
              <th width="100">{{ __('attendancelog.attentp') }}</th>
              <th width="100">{{ __('general.date') }}</th>
              <th width="50">Status</th>
            </tr>
          </thead>
        </table>
      </div>
      <div class="overlay d-none">
        <i class="fa fa-2x fa-sync-alt fa-spin"></i>
      </div>
    </div>
  </div>
</div>
<div class="modal" id="select-file" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="overlay-wrapper">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{ __('general.chs') }} {{ __('general.file') }}</h4>
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
          <button form="form-import" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="{{ __('general.imp') }}"><i class="fa fa-file-import"></i></button>
        </div>
      </div>
      <div class="overlay d-none">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="progress" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Please Wait</h4>
      </div>
      <div class="modal-body center-block">
        <span id="progress-message">Create Attendance Header</span>
        <div class="progress" style="height: 25px;">
          <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
            <span class="show"></span>
          </div>
        </div>
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
Object.size = function(obj) {
  var size = 0,
    key;
  for (key in obj) {
    if (obj.hasOwnProperty(key)) size++;
  }
  return size;
};
  var items = {},count=0;
  function addImport(){
      $('#form-import')[0].reset();
      $('#form-import').find('.help-block').remove();
      $('#form-import .form-group').removeClass('has-error').removeClass('has-success');
      $('#select-file').modal('show');
  }
  function storeHeader(order){
      $('#progress').find('#progress-message').text('Create Employee Attendace');
      $.ajax({
          url:"{{route('attendance.storeheader')}}",
          type:'POST',
          data: {
            _token: "{{ csrf_token() }}",
            order:order,
            limit: 100,
            month: $('#month').val(),
            year: $('#year').val()
          },
          dataType: 'json',
      }).done(function(response){
         $('#progress').find('.show').text(response.order+'/'+response.total+' Employee');
         $('#progress').find('.progress-bar').width(((response.order/response.total)*100)+'%');
         if(response.order == response.total){
           $('#progress').find('.show').text('');
           $('#progress').find('.progress-bar').width('0%');
           storeLog(1);
         }
         else{
           storeHeader(response.order);
         }
      })
  }

  function storeLog(order){
    var attendance =[];
    for(var i = order;i<= (order + 49);i++){
      attendance.push(items[order]);
    }
    $('#progress').find('#progress-message').text('Insert Log Attendace');
    $.ajax({
        url:"{{route('attendance.storelog')}}",
        type:'POST',
        data: {
          _token: "{{ csrf_token() }}",
          order:order,
          attendance: JSON.stringify(attendance),
          total:Object.size(items)
        },
        dataType: 'json',
    }).done(function(response){
      $('#progress').find('.show').text(response.order+'/'+response.total+' Log');
          $('#progress').find('.progress-bar').width(((response.order/response.total)*100)+'%');
        if(response.order == response.total){
          $('#progress').find('.show').text('');
          $('#progress').find('.progress-bar').width('0%');
          storeUpdateLog(1);
        }
        else{
          storeLog(response.order);
        }
    })
    
  }
  function storeUpdateLog(order){
    $('#progress').find('#progress-message').text('Update Log Attendace');
    $.ajax({
        url:"{{route('attendance.storeupdatelog')}}",
        type:'POST',
        data: {
          _token: "{{ csrf_token() }}",
          order:order,
          attendance: JSON.stringify(items[order]),
          total:Object.size(items)
        },
        dataType: 'json',
    }).done(function(response){
      $('#progress').find('.show').text(response.order+'/'+response.total+' Log');
          $('#progress').find('.progress-bar').width(((response.order/response.total)*100)+'%');
        if(response.order == response.total){
          document.location = "{{route('attendance.index')}}";
        }
        else{
          storeUpdateLog(response.order);
        }
    })
    
  }
  function loadItem(table_item){
      count=0;
      $.each(items, function() {
          table_item.row.add([
            this.personel_id,
            this.first_name,
            this.last_name,
            this.attendance_area,
            this.device_name,
            this.point_name,
            this.attendance_date,
            this.employee_id
          ]).draw(false);
          count++;
      });
  }
  function loadItem2(table_item){
      count=0;
      $.each(items, function() {
          table_item.row.add([
            this.personel_id,
            this.first_name,
            this.last_name,
            this.attendance_area,
            this.device_name,
            this.point_name,
            this.attendance_date,
            this.employee_id,
            this.workingtime_id,
            this.workingtime,
            this.overtime,
            this.in,
            this.out
          ]).draw(false);
          count++;
      });
  }
  $(function(){
    $('.select2').select2();
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
          language: {
            url: language_choosen == 'id' ? urlLocaleId : '',
          },
          columnDefs: [
              {
                  orderable: false,targets:[3]
              },
              { className: "text-center", targets: [7] },
              { render: function ( data, type, row ) {
                  if (row[7] !== null) {
                    return '<span class="badge badge-success">Valid</span>';
                  } else {
                    return '<span class="badge badge-danger">Not Valid</span>';
                  }
                },targets: [7]
              }
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
                  url:"{{route('attendance.preview')}}",
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
                  // loadItem2(table_item);
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
              var attendance =[];
              $.each(items, function() {
                attendance.push(this);
              });
              $('#progress').modal('show');
              storeHeader(0);
            //   $.ajax({
            //       url:$('#form').attr('action'),
            //       dataType: 'json',
            //       type:'POST',
            //       data: {
            //           _token: "{{ csrf_token() }}",
            //           attendance: JSON.stringify(attendance),
            //           month: $('#month').val(),
            //           year: $('#year').val()
            //       },
            //       beforeSend:function(){
            //           $('#attendance-preview .overlay').removeClass('d-none');
            //       }
            //   }).done(function(response){
            //       $('.overlay').addClass('d-none');
            //       if(response.status){
            //         document.location = response.results;
            //       }
            //       else{	
            //         $.gritter.add({
            //             title: 'Warning!',
            //             text: response.message,
            //             class_name: 'gritter-warning',
            //             time: 1000,
            //         });
            //       }
            //       return;
            // }).fail(function(response){
            //     $('.overlay').addClass('d-none');
            //     var response = response.responseJSON;
            //     $.gritter.add({
            //         title: 'Error!',
            //         text: response.message,
            //         class_name: 'gritter-error',
            //         time: 1000,
            //     });
            // });		
          }
      });
  });
</script>
@endpush