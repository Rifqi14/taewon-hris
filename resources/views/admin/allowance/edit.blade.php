@extends('admin.layouts.app')

@section('title', 'Edit Allowance')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<style type="text/css">
  .customcheckbox {
    width: 22px;
    height: 22px;
    background: url("/img/green.png") no-repeat;
    background-position-x: 0%;
    background-position-y: 0%;
    cursor: pointer;
    margin: 0 auto;
  }

  .customcheckbox.checked {
    background-position: -48px 0;
  }

  .customcheckbox:hover {
    background-position: -24px 0;
  }

  .customcheckbox.checked:hover {
    background-position: -48px 0;
  }

  .customcheckbox input {
    cursor: pointer;
    opacity: 0;
    scale: 1.6;
    width: 22px;
    height: 22px;
    margin: 0;
  }
</style>
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('allowance.index')}}">Allowance</a></li>
<li class="breadcrumb-item active">Edit</li>
@endpush

@section('content')
<form id="form" action="{{ route('allowance.update',['id'=>$allowance->id]) }}" class="form-horizontal"
          method="post" autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="put">
  <div class="row">
    <div class="col-lg-8">
      <div class="card card-{{ config('configs.app_theme')}} card-outline">
        <div class="card-header">
          <h3 class="card-title">Allowance List</h3>
        </div>
        <div class="card-body">
          
            <div class="box-body">
              <div class="row">
                <div class="col-sm-6">
                  <!-- text input -->
                  <div class="form-group">
                    <label>Allowance Name</label>
                    <input type="text" class="form-control" placeholder="Allowance" id="allowance" name="allowance"
                      value="{{ $allowance->allowance }}">
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>Category</label>
                    <select name="category" id="category" class="form-control select2" style="width: 100%"
                      aria-hidden="true">
                      @foreach (config('enums.allowance_category') as $key=>$value)
                      <option @if ($allowance->category == $key) selected @endif value="{{ $key }}">{{ $value }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
              <div class="row account-section">
                <div class="col-sm-6">
                  <!-- text input -->
                  <div class="form-group">
                    <label>Account</label>
                    <input type="text" class="form-control" placeholder="Account" id="account" name="account"
                      value="{{ $allowance->acc_name }}">
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>Recurrence</label>
                    <select name="recurrence" id="recurrence" class="form-control select2" style="width: 100%"
                      aria-hidden="true">
                      <option @if ($allowance->reccurance == 'hourly') selected @endif value="hourly">Hourly</option>
                      <option @if ($allowance->reccurance == 'daily') selected @endif value="daily">Daily</option>
                      <option @if ($allowance->reccurance == 'monthly') selected @endif value="monthly">Monthly</option>
                      <option @if ($allowance->reccurance == 'yearly') selected @endif value="yearly">Yearly</option>
											<option @if ($allowance->reccurance == 'breaktime') selected @endif value="breaktime">BreakTime</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <!-- text input -->
                  <div class="form-group">
                    <label>Group Allowance</label>
                    <input type="text" class="form-control" placeholder="Select Group Allowance" id="groupallowance" name="groupallowance"
                      value="">
                  </div>
                </div>
                <div class="col-sm-3">
									<div class="form-group">
										<label>Prorate</label>
										<select name="prorate" id="prorate" class="form-control select2" style="width: 100%" aria-hidden="true">
											<option value="yes" @if ($allowance->prorate == 'yes') selected @endif>Yes</option>
											<option value="no"  @if ($allowance->prorate == 'no') selected @endif>No</option>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>THR</label>
										<select name="thr" id="thr" class="form-control select2" style="width: 100%" aria-hidden="true">
											<option value="yes" @if ($allowance->thr == 'yes') selected @endif>Yes</option>
											<option value="no" @if ($allowance->thr == 'no') selected @endif>No</option>
										</select>
									</div>
								</div>
              </div>
              <div class="row">
                <div class="col-md-6 formula-bpjs-section d-none">
                    <div class="form-group">
                      <label for="formula-bpjs" class="control-label">Formula BPJS <b class="text-danger">*</b></label>
                      <select name="formula_bpjs" id="formula_bpjs" class="form-control select2" data-placeholder="Formula BPJS" required>
                      @foreach (config('enums.penalty_config_type') as $key => $item)
                      <option  @if ($allowance->formula_bpjs == $key) selected @endif value="{{ $key }}">{{ $item }}</option>
                      @endforeach
                      </select>
                    </div>
                </div>
                <div class="col-sm-6 working-time-section d-none">
                  <div class="form-group">
                    <label>Working Time</label>
                    <input type="text" class="form-control" placeholder="Working Time" id="working_time" name="working_time" value="{{ $allowance->workingtime_id }}">
                  </div>
                </div>
                <div class="row days-devisor-section d-none">
                  <div class="col-sm-6">
                    <!-- text input -->
                    <div class="form-group">
                      <label>Days Devisor</label>
                      <input type="text" class="form-control" id="days_devisor" name="days_devisor" placeholder="Days Devisor" value="{{ $allowance->days_devisor }}">
                    </div>
                  </div>
                </div>
             </div>
              
            </div>
        </div>
        <div class="overlay d-none">
          <i class="fa fa-2x fa-sync-alt fa-spin"></i>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card card-{{ config('configs.app_theme')}} card-outline">
        <div class="card-header">
          <h3 class="card-title">Other</h3>
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme')}}" title="Simpan"><i
                class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                class="fa fa-reply"></i></a>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-sm-12">
              <!-- text input -->
              <div class="form-group">
                <label>Notes</label>
                <textarea class="form-control" id="notes" name="notes"
                  placeholder="Notes"> {{ $allowance->notes }}</textarea>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label>Status</label>
                <select name="status" id="status" class="form-control select2" style="width: 100%" aria-hidden="true">
                  <option @if($allowance->status == 1) selected @endif value="1">Active</option>
                  <option @if($allowance->status == 0) selected @endif value="0">Non-Active</option>
                </select>
              </div>
            </div>
          </div>
          
        </div>
        <div class="overlay d-none">
          <i class="fa fa-2x fa-sync-alt fa-spin"></i>
        </div>
      </div>
    </div>
    <div class="col-lg-12 allowance-section d-none">
        <div class="card card-{{ config('configs.app_theme') }} card-outline">
          <div class="card-header">
          <h3 class="card-titl">Allowance</h3>
          </div>
          <div class="card-body">
          <table class="table table-striped table-bordered datatable" id="allowance-table" style="width: 100%">
            <thead>
            <tr>
              <th width="10">No</th>
              <th width="200">Allowance</th>
              <th width="200">Category</th>
              <th width="200">Group</th>
              <th width="10">
              <div class="customcheckbox">
                <input type="checkbox" name="checkall" class="checkall" id="checkall">
              </div>
              </th>
            </tr>
            </thead>
          </table>
          </div>
        </div>
      </div>
  </div>
</form>
<div class="row basic-salary-section-rules" style="display:none;">
  <div class="col-lg-12">
    <div class="card card-{{ config('configs.app_theme')}} card-outline">
      <div class="card-header">
        <h3 class="card-title">Rules</h3>
        <div class="pull-right card-tools">
          <a href="#" class="btn btn-{{ config('configs.app_theme')}} btn-sm text-white add_rules" data-toggle="tooltip"
            title="Tambah">
            <i class="fa fa-plus"></i>
          </a>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-striped table-bordered datatable" id="table-rules" style="width:100%">
          <thead>
            <tr>
              <th width="10">No</th>
              <th width="250">Qty Absent (Days)</th>
              <th width="250">Qty Allowance (Days)</th>
              <th width="10">#</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Modal Rules --}}
<div class="modal fade" id="add_rules" tabindex="-1" role="dialog" aria-hidden="true" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="overlay-wrapper">
        <div class="modal-header">
          <h4 class="modal-title">Add Rules</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="form_rules" class="form-horizontal" method="post" autocomplete="off">
            <div class="row">
              <input type="hidden" name="allowance_id">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="absent" class="control-label">Quantity Absent</label>
                  <input type="text" class="form-control" id="qty_absent" name="qty_absent" placeholder="Absent"
                    required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="qty_allowance" class="control-label">Quantity Allowance</label>
                  <input type="text" class="form-control" id="qty_allowance" name="qty_allowance"
                    placeholder="Allowance" required>
                </div>
              </div>
              {{ csrf_field() }}
              <input type="hidden" name="_method" />
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button form="form_rules" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme')}} text-white"
            title="Simpan"><i class="fa fa-save"></i></button>
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
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script>
  const BASIC = 'BASIC';
  var data = [];
  function checkAll(data) {
    $.ajax({
			url: `{{ route('allowance.updateall') }}`,
			method: 'post',
			data: {
				_token: "{{ csrf_token() }}",
				allowanceID: `{{ $allowance->id }}`,
				status: data.checked ? 1 : 0,
			},
			dataType: 'json',
			beforeSend: function() {
				$('.overlay').removeClass('d-none');
			}
		}).done(function(response) {
			$('.overlay').addClass('d-none');
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
		}).fail(function(response) {
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
  function updateAllowance(data) {
    var allowanceID,allowanceDetailID, status;
		if (data.checked) {
			allowanceID	= `{{ $allowance->id }}`;
			allowanceDetailID		  =	data.value;
			status					= 1;
		} else {
			allowanceID	= `{{ $allowance->id }}`;
			allowanceDetailID		  =	data.value;
			status					= 0;
		}
		$.ajax({
			url: `{{ route('allowance.updateallowance') }}`,
			method: 'post',
			data: {
				_token: "{{ csrf_token() }}",
				allowanceID: allowanceID,
				allowanceDetailID: allowanceID,
				status: status,
			},
			dataType: 'json',
			beforeSend: function() {
				$('.overlay').removeClass('d-none');
			}
		}).done(function(response) {
			$('.overlay').addClass('d-none');
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
		}).fail(function(response) {
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
  $(document).ready(function(){
    $('.select2').select2();
    $( "#groupallowance" ).select2({
      ajax: {
        url: "{{route('groupallowance.select')}}",
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
    @if($allowance->group_allowance_id)
    $("#groupallowance").select2('data',{id:{{$allowance->group_allowance_id}},text:'{{$allowance->groupallowance->name}}'}).trigger('change');
    @endif
    $(document).on("change", "#parent_id", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
    });
    $("#account").select2({
			ajax: {
				url: "{{route('account.select')}}",
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
							text: `${item.acc_name}`
						});
					});
					return {
						results: option, more: more,
					};
				},
			},
			allowClear: true,
		});
    @if ($allowance->account_id)
		$("#account").select2('data',{id:{{$allowance->account_id}},text:'{{$allowance->account->acc_name}}'}).trigger('change');
		@endif
		$(document).on("change", "#account", function () {
			if (!$.isEmptyObject($('#form').validate().submitted)) {
				$('#form').validate().form();
			}
		});
    $("#working_time").select2({
			ajax: {
				url: "{{route('workingtime.select')}}",
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
							text: `${item.description}`
						});
					});
					return {
						results: option, more: more,
					};
				},
			},
			allowClear: true,
      multiple: true
		});
    var data = [];
    @if ($allowance->allowanceworkingtime)
      @foreach ($allowance->allowanceworkingtime as $value)
        data.push({id: '{{ $value->workingtime_id }}', text: '{{ $value->workingtime->description }}'});
      @endforeach
    @endif
    $('#working_time').select2('data', data).trigger('change');
    $(document).on("change", "#category", function () {
			var value = $(this).val();
			var val = $('#working_type').val();
			switch (value) {
				case 'tunjanganLain':
				if (val == 'All') {
					$('#working_time').select2('disable');
					$('#working_time').select2('val', "");
				} else {
					$('#working_time').select2('enable');
				}
				$('.working-time-section').removeClass('d-none');
				$('.days-devisor-section').addClass('d-none');
				$('.basic-salary-section').addClass('d-none');
				$('.formula-bpjs-section').addClass('d-none');
				break;
				case 'tunjanganJkkJkm':
				if (val == 'All') {
					$('#working_time').select2('disable');
					$('#working_time').select2('val', "");
				} else {
					$('#working_time').select2('enable');
				}
				$('.working-time-section').removeClass('d-none');
				$('.days-devisor-section').addClass('d-none');
				$('.basic-salary-section').removeClass('d-none');
				$('.formula-bpjs-section').addClass('d-none');
				break;
				case 'tunjanganKehadiran':
				if (val == 'All') {
					$('#working_time').select2('disable');
					$('#working_time').select2('val', "");
				} else {
					$('#working_time').select2('enable');
				}
				$('.working-time-section').removeClass('d-none');
				$('.days-devisor-section').removeClass('d-none');
				$('.basic-salary-section').addClass('d-none');
				$('.formula-bpjs-section').addClass('d-none');
				break;
				case 'pensiunPekerja':
				if (val == 'All') {
					$('#formula_bpjs').select2('disable');
					$('#formula_bpjs').select2('val', "");
				} else {
					$('#formula_bpjs').select2('enable');
				}
				$('.formula-bpjs-section').removeClass('d-none');
				$('.days-devisor-section').addClass('d-none');
				$('.basic-salary-section').addClass('d-none');
				$('.working-time-section').addClass('d-none');
				break;
				case 'pensiunPemberi':
				if (val == 'All') {
					$('#formula_bpjs').select2('disable');
					$('#formula_bpjs').select2('val', "");
				} else {
					$('#formula_bpjs').select2('enable');
				}
				$('.formula-bpjs-section').removeClass('d-none');
				$('.days-devisor-section').addClass('d-none');
				$('.basic-salary-section').addClass('d-none');
				$('.working-time-section').addClass('d-none');
				break;
				case 'premiPekerja':
				if (val == 'All') {
					$('#formula_bpjs').select2('disable');
					$('#formula_bpjs').select2('val', "");
				} else {
					$('#formula_bpjs').select2('enable');
				}
				$('.formula-bpjs-section').removeClass('d-none');
				$('.days-devisor-section').addClass('d-none');
				$('.basic-salary-section').addClass('d-none');
				$('.working-time-section').addClass('d-none');
				break;
				case 'premiPemberi':
				if (val == 'All') {
					$('#formula_bpjs').select2('disable');
					$('#formula_bpjs').select2('val', "");
				} else {
					$('#formula_bpjs').select2('enable');
				}
				$('.formula-bpjs-section').removeClass('d-none');
				$('.days-devisor-section').addClass('d-none');
				$('.basic-salary-section').addClass('d-none');
				$('.working-time-section').addClass('d-none');
				break;
				default:
				$('.formula-bpjs-section').addClass('d-none');
				$('.working-time-section').addClass('d-none');
				$('.days-devisor-section').addClass('d-none');
				$('.basic-salary-section').addClass('d-none');
				break;
			}
		});
    $('#category').trigger('change');
    $(document).on('change', '#formula_bpjs', function() {
      // console.log('aaaa');
      if (this.value == BASIC) {
        $('.allowance-section').addClass('d-none');
      } else {
        $('.allowance-section').removeClass('d-none');
      }
		});
    $('#formula_bpjs').trigger('change');

			$('input[name=checkall]').prop('checked', true);
			$('input[name=checkall]').parent().addClass('checked');
			$(document).on('click', '.customcheckbox input', function() {
				if ($(this).is(':checked')) {
					$(this).parent().addClass('checked');
				} else {
					$(this).parent().removeClass('checked');
				}
			});
		$(document).on('change', '.checkall', function() {
			if (this.checked) {
				$('input[name^=allowanceID]').prop('checked', true);
				$('input[name^=allowanceID]').parent().addClass('checked');
			} else {
				$('input[name^=allowanceID]').prop('checked', false);
				$('input[name^=allowanceID]').parent().removeClass('checked');
			}
		});
		dataTableAllowance = $('.datatable').DataTable({
			stateSave: true,
			processing: true,
			serverSide: true,
			filter: false,
			info: false,
			lengthChange: false,
			responsive: true,
			paginate: false,
			order: [[ 1, "asc"]],
			ajax: {
				url: "{{ route('allowance.readallowance') }}",
				type: "GET",
				data: function(data) {
          data.allowanceId = `{{ $allowance->id }}`
				}
			},
			columnDefs: [
				{ orderable: false, targets: [0, 4] },
				{ className: 'text-right', targets: [0] },
				{ className: 'text-center', targets: [4] },
				{ render: function( data, type, row ) {
				return `<label class="customcheckbox checked"><input value="${row.id}" type="checkbox" name="allowanceID[]" checked><span class="checkmark"></span></label>`
				}, targets: [4] }
			],
			columns: [
				{ data: 'no' },
				{ data: 'allowance' },
				{ data: 'category' },
				{ data: 'groupallowance' },
				{ data: 'id' },
			]
		});
    $('#working_time').select2('data', data).trigger('change');
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
        });
      }
    });
    $('#category').change(function () {
      var value = $(this).val();
      switch (value) {
        case 'tunjanganLain':
          $('.working-time-section').removeClass('d-none');
          $('.days-devisor-section').addClass('d-none');
          $('.basic-salary-section').addClass('d-none');
          $('.basic-salary-section-rules').fadeOut();
          break;
        case 'tunjanganJkkJkm':
          $('.working-time-section').removeClass('d-none');
          $('.days-devisor-section').addClass('d-none');
          $('.basic-salary-section').removeClass('d-none');
          $('.basic-salary-section-rules').fadeIn();
          break;
        case 'tunjanganKehadiran':
          $('.working-time-section').removeClass('d-none');
          $('.days-devisor-section').removeClass('d-none');
          $('.basic-salary-section').addClass('d-none');
          $('.basic-salary-section-rules').fadeIn();
          break;
        default:
          $('.working-time-section').addClass('d-none');
          $('.days-devisor-section').addClass('d-none');
          $('.basic-salary-section').addClass('d-none');
          $('.basic-salary-section-rules').fadeOut();
          break;
      }
    });
    $('#category').val('{!! $allowance->category !!}').trigger('change');
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
        if (element.is(':file')) {
          error.insertAfter(element.parent().parent().parent());
        } else if(element.parent('.input-group').length) {
          error.insertAfter(element.parent());
        } else if (element.attr('type') == 'checkbox') {
          error.insertAfter(element.parent());
        } else {
          error.insertAfter(element);
        }
      },
      submitHandler: function() {
        $.ajax({
          url: $('#form').attr('action'),
          method: 'POST',
          data: new FormData($('#form')[0]),
          processData: false,
          contentType: false,
          dataType: 'JSON',
          beforeSend: function () {
            $('.overlay').removeClass('d-none');
          }
        }).done(function (response) {
          $('.overlay').addClass('d-none');
          if (response.status) {
            document.location = response.results;
          } else {
            $.gritter.add({
              title: 'Warning!',
              text: response.message,
              class_name: 'gritter-warning',
              time: 1000,
            });
          }
          return;
        }).fail(function(response) {
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
    
    $(document).on('click','.deleterule',function(){
        var id = $(this).data('id');
        bootbox.confirm({
          buttons: {
            confirm: {
              label: '<i class="fa fa-check"></i>',
              className: 'btn-{{ config('configs.app_theme')}}'
            },
            cancel: {
              label: '<i class="fa fa-undo"></i>',
              className: 'btn-default'
            },
          }
        });
    });
    dataTableRules = $('#table-rules').DataTable({
        stateSave:true,
        processing: true,
        serverSide: true,
        filter:false,
        info:false,
        lengthChange:true,
        responsive: true,
        order: [[ 3, "asc" ]],
        ajax: {
            url: "{{route('allowancerule.read')}}",
            type: "GET",
            data:function(data){
              var qty_absent = $('#form-search').find('input[name=qty_absent]').val();
              data.qty_absent = qty_absent;
              data.allowance_id = {{$allowance->id}};
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [0] },
            { className: "text-center", targets: [3] },
            { render: function ( data, type, row ) {
                return `<div class="dropdown">
                    <button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item editrule" href="#" data-id="${row.id}"><i class="fa fa-pencil-alt mr-2"></i> Edit</a></li>
                        <li><a class="dropdown-item deleterule" href="#" data-id="${row.id}"><i class="fa fa-trash mr-2"></i> Delete</a></li>
                    </ul>
                    </div>`
            },targets: [3]
            }
        ],
        columns: [
            { data: "no" },
            { data: "qty_absent" },
            { data: "qty_allowance" },
            { data: "id" },
        ]
    });
    $('.add_rules').on('click', function() {
      $('#form_rules')[0].reset();
      $('#form_rules').attr('action',"{{route('allowancerule.store')}}");
      $('#form_rules input[name=_method]').attr('value','POST');
      $('#form_rules input[name=allowance_id]').attr('value',{{ $allowance->id }});
      $('#form_rules input[name=qty_absent]').attr('value','');
      $('#form_rules input[name=qty_allowance]').attr('value','');
      $('#add_rules .modal-title').html('Add Rules');
      $('#add_rules').modal('show');
    });
    $(document).on('click','.editrule',function(){
      var id = $(this).data('id');
      $.ajax({
          url:`{{url('admin/allowancerule')}}/${id}/edit`,
          method:'GET',
          dataType:'json',
          beforeSend:function(){
              $('#box-menu .overlay').removeClass('d-none');
          },
      }).done(function(response){
          $('#box-menu .overlay').addClass('d-none');
          if(response.status){
              $('#add_rules .modal-title').html('Ubah Rule');
              $('#add_rules').modal('show');
              $('#form_rules')[0].reset();
              $('#form_rules .invalid-feedback').each(function () { $(this).remove(); });
              $('#form_rules .form-group').removeClass('has-error').removeClass('has-success');
              $('#form_rules input[name=_method]').attr('value','PUT');
              $('#form_rules input[name=allowance_id]').attr('value',{{$allowance->id}});
              $('#form_rules input[name=qty_absent]').attr('value',response.data.qty_absent);
              $('#form_rules input[name=qty_allowance]').attr('value',response.data.qty_allowance);
              $('#form_rules').attr('action',`{{url('admin/allowancerule/')}}/${response.data.id}`);
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
    });
    $(document).on('click','.deleterule',function(){
        var id = $(this).data('id');
        bootbox.confirm({
          buttons: {
            confirm: {
              label: '<i class="fa fa-check"></i>',
              className: 'btn-danger'
            },
            cancel: {
              label: '<i class="fa fa-undo"></i>',
              className: 'btn-default'
            },
          },
          title:'Menghapus Rule?',
          message:'Data yang telah dihapus tidak dapat dikembalikan',
          callback: function(result) {
              if(result) {
                var data = {
                  _token: "{{ csrf_token() }}",
                  id: id
                  };
                $.ajax({
                  url: `{{url('admin/allowancerule')}}/${id}`,
                  dataType: 'json',
                  data:data,
                  type:'DELETE',
                    beforeSend:function(){
                      $('.overlay').removeClass('hidden');
                      }
                }).done(function(response){
                    if(response.status){
                        $('.overlay').addClass('hidden');
                        $.gritter.add({
                            title: 'Success!',
                            text: response.message,
                            class_name: 'gritter-success',
                            time: 1000,
                        });
                        dataTableRules.ajax.reload( null, false );
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
                    $('.overlay').addClass('hidden');
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
  });
</script>
@endpush