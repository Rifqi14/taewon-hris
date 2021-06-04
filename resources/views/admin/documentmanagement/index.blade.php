@extends('admin.layouts.app')

@section('title',__('document.docmanaj'))
    @section('stylesheets')
    <link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
    <link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
    <link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
    <link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
    <link href="{{asset('adminlte/component/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet">
    <style type="text/css">
        .asset-wrapper{
            display: flex;
        }
        .ui-state-active{
            background: #28a745 !important;
            border-color: #28a745 !important;
        }
        .ui-menu {
            overflow: auto;
            height:200px;
        }
    </style>
    @endsection
    @push('breadcrump')
        <li class="breadcrumb-item active">{{__('document.docmanaj')}}</li>
    @endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">{{__('document.docmanaj')}}</h3>
          <!-- tools card -->
          <div class="pull-right card-tools">
            <a href="#" class="btn btn-{{ config('configs.app_theme') }} btn-sm text-white add_document" data-toggle="tooltip" title="Tambah">
              <i class="fa fa-plus"></i>
            </a>

          </div>
          <!-- /. tools -->
        </div>
        <div class="card-body">
            <div class="row">
                        
                <div class="col-md-6">
                    <div class="form-group">
                    <label class="control-label" for="name">{{__('document.docname')}}</label>
                    <input type="text" name="name" id="name" class="form-control filter" placeholder="{{__('document.docname')}}">
                    </div>
                    <div id="document-container"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                    <label class="control-label" for="code">{{__('document.nodoc')}}</label>
                    <input type="text" name="code" id="code" class="form-control filter" placeholder="{{__('document.nodoc')}}">
                    </div>
                    <div id="nodoc-container"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="code">{{__('document.pic')}}</label>
                        {{-- <input type="text" name="pic" id="pic" class="form-control filter" placeholder="PIC"> --}}
                        <select name="pic" id="pic" class="form-control select2 filter" style="width: 100%" multiple aria-hidden="true" data-placeholder="{{__('document.pic')}}">
                            <option value=""></option>
                            @foreach ($pics as $v_pic)
                            <option value="{{ $v_pic->pic }}">{{ $v_pic->pic }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row col-md-6">
                    <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="date_from">{{__('document.from')}}</label>
                        <div class="controls">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" name="date_from" id="date_from" class="form-control datepicker filter" placeholder="Date">
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                        <label class="control-label" for="date_to">{{__('document.to')}}</label>
                            <div class="controls">
                                <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                    <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" name="date_to" id="date_to" class="form-control datepicker filter" placeholder="Date">
                                </div>
                            </div>
                        </div>
                </div>
                </div>
                
            </div>
            <table class="table table-striped table-bordered datatable" style="width:100%">
                <thead>
                    <tr>
                        <th width="10">#</th>
                        <th width="100">{{__('document.nodoc')}}</th>
                        <th width="100">{{__('general.name')}}</th>
                        <th width="100">{{__('document.exp_date')}}</th>
                        <th width="50">{{__('document.reminder')}}</th>
                        <th width="100">{{__('document.pic')}}</th>
                        <th width="50">{{__('document.file')}}</th>
                        <th width="50">Status</th>
                        <th width="10">#</th>
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

{{-- Modal Add Document --}}
<div class="modal fade" id="add_document" tabindex="-1" role="dialog" aria-hidden="true" role="dialog"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="overlay-wrapper">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('document.adddoc')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_document" class="form-horizontal" method="post" autocomplete="off">
                            
                        <div class="form-group col-sm-12">
                            <label for="code" class="control-label">{{__('document.nodoc')}} <b class="text-danger">*</b></label>
                            <input type="code" class="form-control" id="code" name="code" placeholder="Index Document">
                        </div>
                        <div class="d-flex">
                            <div class="form-group col-sm-6">
                                <label for="name" class="control-label">{{__('general.name')}}<b class="text-danger">*</b></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="{{__('general.name')}}"
                                    required>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="date" class="control-label">{{__('document.exp_date')}}<b class="text-danger">*</b></label>
                                <input type="text" class="form-control" id="expired_date" name="expired_date" placeholder="{{__('document.exp_date')}}"
                                    required>
                            </div>
                        </div>
                        <div class="d-flex">
                            
                            <div class="form-group col-sm-6">
                                <label for="nilai" class="control-label">{{__('document.reminder')}}({{__('general.day')}})<b class="text-danger">*</b></label>
                                <input type="number" class="form-control" id="nilai" name="nilai" placeholder="{{__('document.reminder')}}"
                                    required>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="pic" class="control-label">{{__('document.pic')}}</label><b class="text-danger">*</b></label>
                                <input type="text" class="form-control" id="pic" name="pic" placeholder="{{__('document.pic')}}"
                                    required>
                            </div>
                            
                        </div>
                        <div class="form-group col-sm-12">
                            <label for="file" class="control-label">{{__('document.file')}} <b class="text-danger">*</b></label>
                            <input type="file" class="form-control" name="file" id="file" accept="image/*, .pdf, .doc, .docx, .xls, .xlsx" />
                            <a id="document-preview" onclick="showDocument(this)" href="#" data-url="" class="mt-2"></a>
                        </div>
                        <div class="form-group col-sm-12">
                            <label for="description" class="control-label">{{__('general.desc')}}</label>
                            <textarea name="description" id="description" class="form-control"
                                placeholder="{{__('general.desc')}}"></textarea>
                        </div>
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" />
                    </form>
                </div>
                <div class="modal-footer">
                    <button form="form_document" type="submit"
                        class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="{{__('general.save')}}"><i
                            class="fa fa-save"></i></button>
                </div>
                <div class="overlay d-none">
                    <i class="fa fa-2x fa-sync-alt fa-spin"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Document --}}
<div class="modal fade" id="show-document" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <embed id="url-document" src="" style="height:500px;width:500px;object-fit:contain;padding:20px;">
            <a href="" class="btn btn-{{ config('configs.app_theme') }} rounded-0 download-button" download>{{__('general.download')}}</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/jquery-ui/jquery-ui.min.js')}}"></script>
<script type="text/javascript">
function showDocument(e){
        $('#url-document').attr("src",$(e).data('url'));
        $('.download-button').attr("href",$(e).data('url'));
        $('#show-document').modal('show');
  }
  $(document).ready(function(){
    var documents = [
            @foreach($documents as $document_name)
                "{!!$document_name->name!!}",
            @endforeach
        ];
            $( "input[name=name]" ).autocomplete({
        source: documents,
        minLength:0,
        appendTo: '#document-container',
        select: function(event, response) {
            if(event.preventDefault(), 0 !== response.item.id){
                $(this).val(response.item.value);
                dataTable.draw();
            }
        }
        }).focus(function () {
            $(this).autocomplete("search");
        });
        $("input[name=name]").keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                $('input[name=name]').autocomplete('close');
                return false;
            }
        });
        var nodocs = [
            @foreach($nodocs as $no_document)
                "{!!$no_document->code!!}",
            @endforeach
        ];
            $( "input[name=code]" ).autocomplete({
        source: nodocs,
        minLength:0,
        appendTo: '#nodoc-container',
        select: function(event, response) {
            if(event.preventDefault(), 0 !== response.item.id){
                $(this).val(response.item.value);
                dataTable.draw();
            }
        }
        }).focus(function () {
            $(this).autocomplete("search");
        });
        $("input[name=code]").keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                $('input[name=code]').autocomplete('close');
                return false;
            }
        });
  });
function filter(){
    $('#add-filter').modal('show');
}
$(function(){
    $('#expired_date').daterangepicker({
      singleDatePicker: true,
      timePicker: false,
      timePickerIncrement: 1,
      locale: {
      format: 'YYYY/MM/DD'
      }
    });
    $('#date_to').daterangepicker({
      autoUpdateInput: false,
      singleDatePicker: true,
      timePicker: false,
      timePickerIncrement: 1,
      locale: {
      format: 'DD/MM/YYYY'
      }
    },function(chosen_date) {
      $('#date_to').val(chosen_date.format('DD/MM/YYYY'));
      dataTable.draw();
    });
    $('#date_from').daterangepicker({
      autoUpdateInput: false,
      singleDatePicker: true,
      timePicker: false,
      timePickerIncrement: 1,
      locale: {
      format: 'DD/MM/YYYY'
      }
    },function(chosen_date) {
      $('#date_from').val(chosen_date.format('DD/MM/YYYY'));
      dataTable.draw();
    });
    $('#date').on('change', function(){
        if (!$.isEmptyObject($(this).closest("form").validate())) {
            $(this).closest("form").validate().form();
        }
    });
    $("#form_document").validate({
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
        else{
            error.insertAfter(element);
        }
        },
        submitHandler: function() {
            var status = $('input[name=_method]').val();
            if (status == 'PUT') {
                $('.overlay').removeClass('d-none');
                bootbox.confirm({
                    buttons: {
                        confirm: {
                        label: '<i class="fa fa-check"></i>',
                        className: `btn-{{ config('configs.app_theme') }}`
                        },
                        cancel: {
                        label: '<i class="fa fa-undo"></i>',
                        className: 'btn-default'
                        },
                    },
                    title:'Save the update?',
                    message:'Are you sure to save the changes?',
                    callback: function(result) {
                        if(result) {            
                            $.ajax({
                                url:$('#form_document').attr('action'),
                                method:'post',
                                data: new FormData($('#form_document')[0]),
                                processData: false,
                                contentType: false,
                                dataType: 'json',
                                beforeSend:function(){
                                $('.overlay').removeClass('d-none');
                                }
                            }).done(function(response){
                                    $('.overlay').addClass('d-none');
                                    if(response.status){
                                    $('#add_document').modal('hide');
                                    dataTable.draw();
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
                        $('.overlay').addClass('d-none');
                    }
                });
            } else {
                $.ajax({
                    url:$('#form_document').attr('action'),
                    method:'post',
                    data: new FormData($('#form_document')[0]),
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    beforeSend:function(){
                    $('.overlay').removeClass('d-none');
                    }
                }).done(function(response){
                        $('.overlay').addClass('d-none');
                        if(response.status){
                        $('#add_document').modal('hide');
                        dataTable.draw();
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
        }
    });
    dataTable = $('.datatable').DataTable( {
        stateSave:true,
        processing: true,
        serverSide: true,
        filter:false,
        info:false,
        lengthChange:true,
        responsive: true,
        order: [[ 8, "asc" ]],
        language: {
            lengthMenu: `{{ __('general.showent') }}`,
            processing: `{{ __('general.process') }}`,
            paginate: {
                previous: `{{ __('general.prev') }}`,
                next: `{{ __('general.next') }}`,
            }
        },
        ajax: {
            url: "{{route('documentmanagement.read')}}",
            type: "GET",
            data:function(data){
                var name = $('input[name=name]').val();
                var code = $('input[name=code]').val();
                var pic = $('select[name=pic]').val();
                var date_from = $('input[name=date_from]').val();
                var date_to = $('input[name=date_to]').val();
                data.code = code;
                data.name = name;
                data.pic = pic;
                data.date_from = date_from;
                data.date_to = date_to
            }
        },
        columnDefs:[
					{
						orderable: false,targets:[0]
					},
					{ className: "text-right", targets: [0] },
                    { className: "text-center", targets: [4,5,7] },
                    {
						render: function (data, type, row) {
							return `${row.nilai} ${row.reminder_type}`	
						},
						targets: [4]
					},
					{
					render: function (data, type, row) {
						// return `<a href="${row.file}" target="_blank"><img class="img-fluid" src="${row.file}" height=\"100\" width=\"150\"/><a/>`
							return `<a onclick="showDocument(this)" data-url="${row.link}" href="#"><span class="badge badge-info">{{__('document.prv')}}</span><a/>`
					},
					targets: [6]
                    },
                    {
						render: function (data, type, row) {
							if (row.status == 'Active') {
								return `<span class="badge badge-success">{{__('general.actv')}}</span>`
							}else{
								return `<span class="badge badge-danger">{{__('general.expired')}}</span>`
							}
						},
						targets: [7]
                    },
                    
					{ render: function ( data, type, row ) {
						return `<div class="dropdown">
							<button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
							<i class="fa fa-bars"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right">
								<li><a class="dropdown-item editdocument" href="#" data-id="${row.id}"><i class="fa fa-pencil-alt mr-2"></i> {{__('general.edt')}}</a></li>
								<li><a class="dropdown-item deletedocument" href="#" data-id="${row.id}"><i class="fa fa-trash mr-2"></i> {{__('general.dlt')}}</a></li>
							</ul>
							</div>`
					},targets: [8]
					}
				],
				columns: [
                    { data: "no" },
                    { data: "code"},
                    { data: "name" },
                    { data: "expired_date"},
                    { data: "nilai" },
                    { data: "pic"},
                    { data: "file" },
                    { data: "status" },
					{ data: "id" }
				]
    });
     $(document).on('change keyup keydown keypress focus', '.filter', function() {
      dataTable.draw();
    });
    $('.add_document').on('click',function(){
				$('#form_document')[0].reset();
				$('#form_document').attr('action',"{{route('documentmanagement.store')}}");
                $('#form_document input[name=_method]').attr('value','POST');
                $('#form_document input[name=code]').attr('value','');
                $('#form_document input[name=name]').attr('value','');
                $('#form_document input[name=file]').attr('value','');
                $('#form_document input[name=pic]').attr('value','');
                $('#form_document input[name=expired_date]').attr('value','');
                $('#form_document input[name=nilai]').attr('value','');
				$('#form_document textarea[name=description]').html('');
				$('#form_document .invalid-feedback').each(function () { $(this).remove(); });
				$('#form_document .form-group').removeClass('has-error').removeClass('has-success');
				$('#add_document .modal-title').html('{{__('document.adddoc')}}');
				$('#document-preview').html('').attr('data-url','');
				$('#add_document').modal('show');
			});
			$(document).on('click','.editdocument',function(){
				var id = $(this).data('id');
                bootbox.confirm({
                    buttons: {
                        confirm: {
                            label: '<i class="fa fa-check"></i>',
                            className: 'btn-{{ config("configs.app_theme") }}'
                        },
                        cancel: {
                            label: '<i class="fa fa-undo"></i>',
                            className: 'btn-default'
                        },
                    },
                    title: 'Edit Document?',
                    message: 'You will be edit this document, are you sure?',
                    callback: function (result) {
                        if (result) {                        
                            $.ajax({
                                url:`{{url('admin/documentmanagement')}}/${id}/edit`,
                                method:'GET',
                                dataType:'json',
                                beforeSend:function(){
                                    $('#box-menu .overlay').removeClass('d-none');
                                },
                            }).done(function(response){
                                $('#box-menu .overlay').addClass('d-none');
                                if(response.status){
                                    $('#add_document .modal-title').html('{{__('document.edtdoc')}}');
                                    $('#add_document').modal('show');
                                    $('#form_document')[0].reset();
                                    $('#form_document .invalid-feedback').each(function () { $(this).remove(); });
                                    $('#form_document .form-group').removeClass('has-error').removeClass('has-success');
                                    $('#form_document input[name=_method]').attr('value','PUT');
                                    $('#form_document input[name=name]').attr('value',response.data.name);
                                    $('#form_document input[name=nilai]').attr('value',response.data.nilai);
                                    $('#form_document input[name=code]').attr('value',response.data.code);
                                    $('#form_document input[name=file]').attr('value',response.data.file);
                                    $('#form_document input[name=pic]').attr('value',response.data.pic);
                                    $('#form_document input[name=expired_date]').attr('value',response.data.expired_date);
                                    $('#form_document textarea[name=description]').html(response.data.description);
                                    $('#document-preview').html(response.data.file).attr('data-url',response.data.link);
                                    $('#form_document').attr('action',`{{url('admin/documentmanagement/')}}/${response.data.id}`);
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
                            });
                        }
                    }
                });
			});
			$(document).on('click','.deletedocument',function(){
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
				title:'Delete Data Document Management?',
				message:'Deleted Data Cannot Be Recovered',
				callback: function(result) {
					if(result) {
						var data = {
										_token: "{{ csrf_token() }}",
										id: id
									};
						$.ajax({
						url: `{{url('admin/documentmanagement')}}/${id}`,
						dataType: 'json',
						data:data,
						type:'DELETE',
							beforeSend:function(){
								$('.overlay').removeClass('d-none');
							}
						}).done(function(response){
							if(response.status){
								$('.overlay').addClass('d-none');
								$.gritter.add({
									title: 'Success!',
									text: response.message,
									class_name: 'gritter-success',
									time: 1000,
								});
								dataTable.draw();
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
							$('.overlay').addClass('d-none');
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
			});
    $("#file").fileinput({
        browseClass: "btn btn-{{config('configs.app_theme')}}",
        showRemove: false,
        showUpload: false,
        allowedFileExtensions: ["png", "jpg", "jpeg", "pdf", "doc", "docx", "xls", "xlsx"],
        dropZoneEnabled: false,
        initialPreviewAsData: false,
        initialPreviewFileType: 'image',
        theme:'explorer-fas'
    });
    $('.select2').select2();
    $('#form-search').submit(function(e){
        e.preventDefault();
        dataTable.draw();
        $('#add-filter').modal('hide');
    });
    // $('#name').select2({
    //         ajax: {
    //             url: "{{route('documentmanagement.select')}}",
    //             type:'GET',
    //             dataType: 'json',
    //             data: function (term,page) {
    //             return {
    //                 name:term,
    //                 page:page,
    //                 limit:30,
    //             };
    //             },
    //             results: function (data,page) {
    //             var more = (page * 30) < data.total;
    //             var option = [];
    //             $.each(data.rows,function(index,item){
    //                 option.push({
    //                 id:item.name,
    //                 text: item.name
    //                 });
    //             });
    //             return {
    //                 results: option, more: more,
    //             };
    //             },
    //         },
    //         allowClear: true,
    //     });
    
})
</script>
@endpush