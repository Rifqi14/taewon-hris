@extends('admin.layouts.app')
@section('title', 'Menu')
@section('stylesheets')
<link href="{{asset('adminlte/component/nestable/nestable.min.css')}}" rel="stylesheet">
<style type="text/css">
.overlay-wrapper{
    position:relative;
}
.dd-handle {
    display: block;
    height: 40px;
    padding: 8px 10px;
    text-decoration: none;
    font-weight: 500;
    border: 1px solid 
    #ccc;
    border-radius: 3px;
    box-sizing: border-box;
    -moz-box-sizing: border-box;
}
.dd-handle span{
    font-weight: 500;
}
.dd-item > button {
    margin:10px 0;
}
.item_actions{
    position: absolute;
    top: 8px;
    right: 10px;
}
</style>
@endsection
@push('breadcrump')
    <li class="breadcrumb-item active">Menu</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-{{config('configs.app_theme')}} card-outline" id="box-menu">
            <div class="card-header">
              <h3 class="card-title">List Menu</h3>
              <div class="pull-right card-tools">
                <button class="btn btn-sm btn-{{config('configs.app_theme')}} create" title="Tambah"><i class="fa fa-plus"></i></button>
                <button class="btn btn-sm btn-default updateorder" title="Tambah"><i class="fa fa-save"></i></button>
              </div>
            </div>
            <div class="card-body">
                <div class="dd">
                    {!!buildDD($menus);!!}
                </div>
            </div>
            <div class="overlay d-none">
                <i class="fa fa-2x fa-sync-alt fa-spin"></i>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
            <div class="overlay-wrapper">
                <div class="modal-header">
                    <h4 class="modal-title">Add Menu</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form" method="post" autocomplete="off">
                        <div class="form-group row">
                            <label class="col-md-3 col-xs-12 control-label" for="menu_name">Name <b class="text-danger">*</b></label>
                            <div class="col-sm-9 controls">
                                    <input type="text" class="form-control" id="menu_name" name="menu_name" placeholder="Name" value="" required/>					
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-xs-12 control-label" for="menu_route">Route</label>
                            <div class="col-sm-9 controls">
                                    <input type="text" class="form-control" id="menu_route" name="menu_route" value="" placeholder="Route" />					
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-xs-12 control-label" for="menu_icon">Icon</label>
                            <div class="col-sm-9 controls">
                                    <input type="text" class="form-control" id="menu_icon" name="menu_icon" value="" placeholder="Icon"/>					
                            </div>
                        </div>
                        {{ csrf_field() }}
                        <input type="hidden" name="_method"/>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="form" class="btn btn-{{config('configs.app_theme')}}" ><i class="fa fa-save"></i></button>
                </div>
                <div class="overlay d-none">
                    <i class="fa fa-2x fa-sync-alt fa-spin"></i>
                </div>
            </div>
		</div>
	</div>
</div>
@endsection
@push('scripts')
<script src="{{asset('adminlte/component/nestable/jquery.nestable.js')}}"></script>
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script type="text/javascript">
$(function(){
    $('.dd').nestable({
        maxDepth:2
    }).nestable('collapseAll');
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
                    $('#create .overlay').removeClass('d-none');
                }
            }).done(function(response){
                $('#create .overlay').addClass('d-none');
                if(response.status){
                    if($('#form').attr('action') == "{{route('menu.store')}}"){
                        $('#create').modal('hide');
                        $('.dd-list').append(`
                        <li class="dd-item" data-id="${response.data.id}">
                            <div class="item_actions">
                                 <button class="btn btn-xs btn-default edit" data-id="${response.data.id}">
                                 <i class="fas fa-edit"></i>
                                </button> /
                                <button class="btn btn-xs btn-{{config('configs.app_theme')}} delete" data-id="${response.data.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="dd-handle">
                                <span>${response.data.menu_name}</span> 
                                <small class="url">${response.data.menu_route}</small>
                            </div> 
                        </li>`);
                    }
                    else{
                        $('#create').modal('hide');
                        $('li[data-id='+response.data.id+'] > .dd-handle > span').html(response.data.menu_name);
                        $('li[data-id='+response.data.id+'] > .dd-handle > small').html(response.data.menu_route);
                    }
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
                var response = response.responseJSON;
                $('#create .overlay').addClass('d-none');
                $.gritter.add({
                    title: 'Error!',
                    text: response.message,
                    class_name: 'gritter-error',
                    time: 1000,
                });
            })		
        }
    });
    $(document).on('click','.edit',function(){
        var id = $(this).data('id');
        $.ajax({
            url:`{{url('admin/menu')}}/${id}/edit`,
            method:'GET',
            dataType:'json',
            beforeSend:function(){
                $('#box-menu .overlay').removeClass('d-none');
            },
        }).done(function(response){
            $('#box-menu .overlay').addClass('d-none');
            if(response.status){
                $('#create .modal-title').html('Edit Menu');
                $('#create').modal('show');
                $('#form')[0].reset();
                $('#form .invalid-feedback').each(function () { $(this).remove(); });
                $('#form .form-group').removeClass('has-error').removeClass('has-success');
                $('#form input[name=_method]').attr('value','PUT');
                $('#form input[name=menu_name]').attr('value',response.data.menu_name);
                $('#form input[name=menu_route]').attr('value',response.data.menu_route);
                $('#form input[name=menu_icon]').attr('value',response.data.menu_icon);
                $('#form').attr('action',`{{url('admin/menu/')}}/${response.data.id}`);
            }          
        }).fail(function(response){
            var response = response.responseJSON;
            $('#box-menu .overlay').addClass('d-none');
            $.gritter.add({
                title: 'Error!',
                text: response.message,
                class_name: 'gritter-error',
                time: 1000,
            });
        })	
    })
    $(document).on('click','.delete',function(){
        var id = $(this).data('id');
        bootbox.confirm({
			buttons: {
				confirm: {
					label: '<i class="fa fa-check"></i>',
					className: 'btn-{{config('configs.app_theme')}}'
				},
				cancel: {
					label: '<i class="fa fa-undo"></i>',
					className: 'btn-default'
				},
			},
			title:'Hapus data?',
			message:'Data yang akan dihapus tidak dapat dikembalikan',
			callback: function(result) {
					if(result) {
						var data = {
                            _token: "{{ csrf_token() }}"
                        };
						$.ajax({
							url: `{{url('admin/menu')}}/${id}`,
							dataType: 'json', 
							data:data,
							type:'DELETE',
                            beforeSend:function(){
                                $('#box-menu .overlay').removeClass('d-none');
                            }
                        }).done(function(response){
                            if(response.status){
                                $('#box-menu .overlay').addClass('d-none');
                                $('.dd li[data-id='+id+']').remove();
                                $.gritter.add({
                                    title: 'Success!',
                                    text: response.message,
                                    class_name: 'gritter-success',
                                    time: 1000,
                                });
                            }
                            else{
                                $.gritter.add({
                                    title: 'Warning!',
                                    text: response.message,
                                    class_name: 'gritter-warning',
                                    time: 1000,
                                });
                            }
                        }).fail(function(response){
                            var response = response.responseJSON;
                            $('#box-menu .overlay').addClass('d-none');
                            $.gritter.add({
                                title: 'Error!',
                                text: response.message,
                                class_name: 'gritter-error',
                                time: 1000,
                            });
                        })		
					}
			}
		});
    })
    $('.create').on('click',function(){
        $('#form')[0].reset();
        $('#form').attr('action',"{{route('menu.store')}}");
        $('#form input[name=_method]').attr('value','POST');
        $('#form input[name=menu_name]').attr('value','');
        $('#form input[name=menu_route]').attr('value','');
        $('#form input[name=menu_icon]').attr('value','');
        $('#form .invalid-feedback').each(function () { $(this).remove(); });
        $('#form .form-group').removeClass('has-error').removeClass('has-success');
        $('#create .modal-title').html('Add Menu');
        $('#create').modal('show');
    })


    $('.updateorder').on('click',function(){
        bootbox.confirm({
			buttons: {
				confirm: {
					label: '<i class="fa fa-check"></i>',
					className: 'btn-{{config('configs.app_theme')}}'
				},
				cancel: {
					label: '<i class="fa fa-undo"></i>',
					className: 'btn-default'
				},
			},
			title:'Mengubah sort menu?',
			message:'Data sort akan berubah setelah melakukan persetujuan',
			callback: function(result) {
				if(result) {
                    var data = {
                        _token: "{{ csrf_token() }}",
                        order: JSON.stringify($('.dd').nestable('serialize'))
                    };
                    $.ajax({
                        url: "{{url('admin/menu')}}/order",
                        dataType: 'json', 
                        data:data,
                        type:'POST',
                        beforeSend:function(){
                            $('#box-menu .overlay').removeClass('d-none');
                        }
                    }).done(function(response){
                        if(response.status){
                            $('#box-menu .overlay').addClass('d-none');
                            $.gritter.add({
                                title: 'Success!',
                                text: response.message,
                                class_name: 'gritter-success',
                                time: 1000,
                            });
                        }
                        else{
                            $.gritter.add({
                                title: 'Warning!',
                                text: response.message,
                                class_name: 'gritter-warning',
                                time: 1000,
                            });
                        }
                    }).fail(function(response){
                        var response = response.responseJSON;
                        $('#box-menu .overlay').addClass('d-none');
                        $.gritter.add({
                            title: 'Error!',
                            text: response.message,
                            class_name: 'gritter-error',
                            time: 1000,
                        });
                    })
                }
            }
        })
    })
})
</script>
@endpush