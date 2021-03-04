@extends('admin.layouts.app')

@section('title', 'Tambah Kota')
@push('breadcrump')
    <li class="breadcrumb-item"><a href="{{route('region.index')}}">Kota</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endpush
@section('content')
<div class="card card-default">
  <div class="card-header">
    <h3 class="card-title">Tambah Kota</h3>
    <!-- tools card -->
    <div class="pull-right card-tools">
      <button form="form" type="submit" class="btn btn-sm btn-danger" title="Simpan"><i class="fa fa-save"></i></button>
      <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
    </div>
    <!-- /. tools -->
  </div>
  <div class="card-body">
      <form id="form" action="{{route('region.store')}}" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          <div class="box-body">
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Provinsi <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="province_id" name="province_id" data-placeholder="Pilih Provinsi" required>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Nama <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="name" name="name" placeholder="Nama" required>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Tipe <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                  <select class="form-control select2" id="type" required name="type">
                      <option value="Kabupaten">Kabupaten</option>
                      <option value="Kota">Kota</option>
                  </select>
              </div>
            </div>
          </div>
        </form>
  </div>
  <div class="overlay d-none">
    <i class="fa fa-refresh fa-spin"></i>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script>
  $(document).ready(function(){
      $('.select2').select2();
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
      $(document).on("change", "#province_id", function () {
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
</script>
@endpush
