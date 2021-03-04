@extends('admin.layouts.app')

@section('title', 'Edit Basic UMK Base')
@section('stylesheets')
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('basesallary.index')}}">Basic UMK Base</a></li>
<li class="breadcrumb-item active">Edit</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Edit Basic UMK Base</h3>
          <!-- tools box -->
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme')}}" title="Simpan"><i
              class="fa fa-save"></i></button>
              <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                class="fa fa-reply"></i></a>
              </div>
              <!-- /. tools -->
            </div>
            <div class="card-body">
              <form id="form" action="{{ route('basesallary.update',['id'=>$basesallary->id]) }}" method="post" autocomplete="off">
                {{ csrf_field() }}
                {{ method_field('put') }}
                <div class="row">
                  <div class="col-sm-6">
                    <!-- text input -->
                    <div class="form-group">
                      <label>Region</label>
                      <input class="form-control" id="region_id" data-placeholder="Pilih Region" name="region_id" value="{{ $basesallary->name }}">
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Sallary</label>
                      <input type="text" class="form-control" name="sallary" value="{{ $basesallary->sallary }}" placeholder="sallary">
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
    </div>
  </div>
  @endsection

  @push('scripts')
  <script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
  <script>
    $(document).ready(function(){
      $( "#region_id" ).select2({
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
      @if($basesallary->region_id)
      $("#region_id").select2('data',{id:{{$basesallary->region_id}},text:'{{$basesallary->region->name}}'}).trigger('change');
      @endif
      $(document).on("change", "#region_id", function () {
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
