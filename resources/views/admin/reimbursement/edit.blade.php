@extends('admin.layouts.app')
@section('title', 'Edit Reimbursement')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{ route('reimbursement.index') }}">Master Reimbursement</a></li>
<li class="breadcrumb-item active">Edit</li>
@endpush

@section('content')
 <form id="form" action="{{ route('reimbursement.update', ['id'=>$reimbursement->id]) }}" class="form-horizontal" method="post" autocomplete="off">
  <div class="row">
    <div class="col-lg-12">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">Personal Data </h3>
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Simpan"><i
                class="fa fa-save"></i></button>
            <a href="javascript:void(0)" onclick="backurl()" class="btn btn-sm btn-default" title="Kembali"><i
                class="fa fa-reply"></i></a>
          </div>
        </div>
          <div class="card-body">
            {{ csrf_field() }}
            {{ method_field('put') }}
            <div class="form-row">
              <div class="form-group col-sm-6">
                <div class="row">
                  <label class="col-sm-3 label-controls" for="pick_id">Driver Name</label>
                  <div class="col-sm-8 controls">
                    <input type="text" class="form-control" id="pick_id" name="pick_id" data-placeholder="Select Driver">
                    <input type="hidden" name="dailyreportdriver_id" id="dailyreportdriver_id" value="{{ $reimbursement->daily_report_driver_id }}">
                    <input type="hidden" name="max_arrival" id="max_arrival" value="{{ $reimbursement->max_arrival }}">
                    <input type="hidden" name="get_day" id="get_day" value="{{ $reimbursement->get_day }}">
                    <input type="hidden" name="driver_id" id="driver_id" value="{{ $reimbursement->driver_id }}">
                  </div>
                </div>
              </div>
              <div class="form-group col-sm-6">
                <div class="row">
                  <label class="col-sm-3 label-controls" for="notes">Notes</label>
                  <div class="col-sm-8 controls">
                    <textarea type="text" class="form-control" name="notes" placeholder="Notes">{{ $reimbursement->notes }}</textarea>
                  </div>
                </div>
              </div>
              
            </div>
            <div class="form-row">
              <div class="form-group col-sm-6">
                <div class="row">
                  <label class="col-sm-3 label-controls" for="date">Date</label>
                  <div class="col-sm-8 controls">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="far fa-calendar-alt"></i>
                        </span>
                      </div>
                      <input type="text" name="date" class="form-control datepicker" placeholder="Date" required value="{{ date('d/m/Y', strtotime($reimbursement->date)) }}"/>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-12 ">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">List Calculation</h3>
          <div class="float-right">
            <button type="button"
							class="btn btn-{{ config('configs.app_theme') }} btn-sm text-white btnShowModal">
							<b><i class="fas fa-plus"></i></b>
						</button>
          </div>
        </div>
        <div class="card-body">
          <table class="table table-striped table-bordered datatable" id="table-product" style="width: 100%">
            <thead>
              <tr>
                {{-- <th class="text-center" width="10">No</th> --}}
                <th width="130">Description</th>
                <th class="text-right" width="100">Value</th>
                <th width="10">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($reimbursement->reimbursementcalculation as $key => $item)
                <tr data-id="{{$item->id}}" data-drd_calculation_id="{{$item->drd_calculation_id}}" data-drd_additional_id="{{$item->drd_additional_id}}" data-total="{{$item->value}}" data-reff_detail_additional="{{$item->reff_detail_additional}}">
                  <td class="text-left">{{$item->description}}
                    <input type="hidden" name="product_item[]" />
                    <input type="hidden" name="drd_calculation_id[]" value="{{$item->drd_calculation_id}}"/>
                    <input type="hidden" name="drd_additional_id[]" value="{{$item->drd_additional_id}}"/>
                    <input type="hidden" name="reff_detail_additional[]" value="{{$item->reff_detail_additional}}"/>
                    <input type="hidden" placeholder="Description" name="description[]" class="form-control form-control-sm" value="{{$item->description}}"/>
                  </td>
                  <td class="text-right">
                    {{number_format($item->value,0,',','.')}}
                    <input type="hidden" type="text" placeholder="Value" name="value[]" class="form-control form-control-sm currency text-right onCalculation" required  value="{{$item->value}}">
                  </td>
                  <td class="text-center">
                    <a href="javascript:;" class="link-red remove" title="Delete">
                      <i class="fa fa-trash"></i>
                    </a>
                  </td>
                </tr>
              @endforeach
            </tbody>
            <input type="hidden" name="subtotal" class="form-control form-control-sm currency text-right" value="{{ $reimbursement->subtotal }}">
            <input type="hidden" name="subtotalallowance" class="form-control form-control-sm currency text-right"  value="{{ $reimbursement->subtotalallowance }}">
            <input type="hidden" name="grandtotal" class="form-control form-control-sm currency text-right"  value="{{ $reimbursement->grandtotal }}">
            <tfoot>
              <tr>
                <th class="text-right" colspan="1">
                  Sub Total
                </th>
                <td class="text-right" data-subtotal>
                  {{number_format($reimbursement->subtotal,0,',','.')}}
                </td>
                <td></td>
              </tr>
              {{-- <tr>
                <td class="" colspan="4">
                </td>
                <td class="text-center">
                  <a onclick="addproduct()" style="color: white;" class="btn btn-sm btn-primary" title="Add"><i class="fas fa-plus-square"></i></a>
                </td>
              </tr> --}}
            </tfoot>
          </table>
        </div>
        <div class="overlay d-none">
          <i class="fa fa-2x fa-sync-alt fa-spin"></i>
        </div>
      </div>
    </div>
    <div class="col-lg-12 ">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">List Allowance</h3>
          <div class="float-right">
            <button type="button"
							class="btn btn-{{ config('configs.app_theme') }} btn-sm text-white btnShowModalAllowance">
							<b><i class="fas fa-plus"></i></b>
						</button>
          </div>
        </div>
        <div class="card-body">
          <table class="table table-striped table-bordered datatable" id="table-allowance" style="width: 100%">
            <thead>
              <tr>
                {{-- <th class="text-center" width="10">No</th> --}}
                <th width="200">Description</th>
                <th class="text-right" width="100">Value</th>
                <th class="text-center" width="5">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($reimbursement->reimbursementallowance as $key => $item)
                <tr data-id="{{$item->driver_list_id}}" data-total="{{$item->value}}">
                  <td class="text-left">{{$item->description}}
                    <input type="hidden" name="product_allowance[]" />
                    <input type="hidden" name="id[]" value="{{$item->driver_list_id}}"/>
                    <input type="hidden" name="driver_list_id[]" value="{{$item->driver_list_id}}"/>
                    <input type="hidden" placeholder="Description" name="description_allowance[]" class="form-control form-control-sm" value="{{$item->description}}"/>
                  </td>
                  <td class="text-right">
                    {{number_format($item->value,0,',','.')}}
                    <input type="hidden" placeholder="Value" name="value_allowance[]" class="form-control form-control-sm currency text-right onCalculation" required  value="{{$item->value}}">
                  </td>
                  <td class="text-center">
                    <a href="javascript:;" class="link-red remove" title="Delete">
                      <i class="fa fa-trash"></i>
                    </a>
                  </td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th class="text-right" colspan="1">
                  Sub Total
                </th>
                <td class="text-right" data-subtotalallowance="0">
                  {{number_format($reimbursement->subtotalallowance,0,',','.')}}
                </td>
                <td></td>
              </tr>
              <tr>
                <th class="text-right" colspan="1">
                  Grand Total
                </th>
                <td class="text-right" data-grandtotal="0">
                  {{number_format($reimbursement->grandtotal,0,',','.')}}
                </td>
                <td></td>
              </tr>
              {{-- <tr>
                <td class="" colspan="3">
                </td>
                <td class="text-center">
                  <a onclick="addAdditional()" style="color: white;" class="btn btn-sm btn-primary" title="Add"><i class="fas fa-plus-square"></i></a>
                </td>
              </tr> --}}
            </tfoot>
          </table>
        </div>
        <div class="overlay d-none">
          <i class="fa fa-2x fa-sync-alt fa-spin"></i>
        </div>
      </div>
    </div>
  </div>
</form>
{{--  <!-- Modal -->  --}}
<div class="modal fade" id="addCalculationModal" tabindex="-1" role="dialog" aria-labelledby="addCalculationModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addCalculationModalLabel">Add Calculation</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-12">
						<div class="card card-primary card-tabs">
							<div class="card-header p-0 pt-1 bg-success ">
								<ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" id="table-current-sparepart-tab" data-toggle="pill"
											href="#table-current-sparepart" role="tab"
											aria-controls="table-current-sparepart" aria-selected="true">
											Calculation
										</a>
                  </li>
                  <li class="nav-item">
										<a class="nav-link" id="table-current-additional-tab" data-toggle="pill"
											href="#table-current-additional" role="tab"
											aria-controls="table-current-additional" aria-selected="true">
											Additional
										</a>
									</li>
								</ul>
							</div>
							<div class="card-body">
								<div class="tab-content" id="custom-tabs-one-tabContent">
									<div class="tab-pane fade show active" id="table-current-sparepart" role="tabpanel"
										aria-labelledby="table-current-sparepart-tab">
										<table class="table table-striped table-bordered w-100" id="example3">
											<thead>
												<tr>
													<th width="100">Parking</th>
													<th class="text-center" width="100">Toll Money</th>
													<th class="text-right" width="100">Oil</th>
													<th class="text-right" width="100">Etc</th>
													<th class="text-center" width="100">#</th>
												</tr>
											</thead>
										</table>
                  </div>
                  <div class="tab-pane fade" id="table-current-additional" role="tabpanel"
										aria-labelledby="table-current-additional-tab">
										<table class="table table-striped table-bordered w-100" id="example4">
											<thead>
												<tr>
													<th width="100">Additional Name</th>
													<th class="text-right" width="100">Value</th>
													<th class="text-center" width="100">#</th>
												</tr>
											</thead>
										</table>
									</div>
								</div>
							</div>
							<!-- /.card -->
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<!-- <button type="button" class="btn btn-primary bg-navy btn-sm color-palette btn-labeled legitRipple float-right">
					<b><i class="fas fa-plus"></i></b>
					Add
				</button> -->
				<button type="button" class="btn btn-secondary btn-sm color-palette btn-labeled legitRipple float-right"
					data-dismiss="modal">
					<b><i class="fas fa-times"></i></b>
					Cancel
				</button>
			</div>
		</div>
	</div>
</div>
{{--  Modal Allowance  --}}
<div class="modal fade" id="addAllowanceModal" tabindex="-1" role="dialog" aria-labelledby="addAllowanceModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addAllowanceModalLabel">Add Allowance</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-12">
						<div class="card card-primary card-tabs">
							<div class="card-header p-0 pt-1 bg-success ">
								<ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" id="table-current-sparepart-tab" data-toggle="pill"
											href="#table-current-sparepart" role="tab"
											aria-controls="table-current-sparepart" aria-selected="true">
											Allowance
										</a>
									</li>
								</ul>
							</div>
							<div class="card-body">
								<div class="tab-content" id="custom-tabs-one-tabContent">
									<div class="tab-pane fade show active" id="table-current-sparepart" role="tabpanel"
										aria-labelledby="table-current-sparepart-tab">
										<table class="table table-striped table-bordered w-100" id="table-pickallowance">
											<thead>
												<tr>
													<th width="100">Allowance</th>
													<th class="text-right" width="100">Value</th>
													<th class="text-center" width="100">#</th>
												</tr>
											</thead>
										</table>
									</div>
								</div>
							</div>
							<!-- /.card -->
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<!-- <button type="button" class="btn btn-primary bg-navy btn-sm color-palette btn-labeled legitRipple float-right">
					<b><i class="fas fa-plus"></i></b>
					Add
				</button> -->
				<button type="button" class="btn btn-secondary btn-sm color-palette btn-labeled legitRipple float-right"
					data-dismiss="modal">
					<b><i class="fas fa-times"></i></b>
					Cancel
				</button>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/jquery-mask/jquery.mask.min.js')}}"></script>
<script src="{{asset('js/accounting/accounting.min.js')}}"></script>
<script type="text/javascript">
  var so_ids = [];
  var do_ids = [];
  function selectCalculation(that)
  {
    var id = $(that).data('id');
    var reff_detail = $(that).data('reff_detail');
    var total = $(that).data('total');
     if ($.inArray(id, so_ids) != -1) {
          $.gritter.add({
              title: 'Warning!',
              text: 'Data already selected',
              class_name: 'gritter-warning',
              time: 1000,
          });
          return;
    } else {
      so_ids.push(id);
    }
    length = $('#table-product tr').length-1;
    var html = `
    <tr data-id="${id}" data-total="${total}" data-reff_detail_additional="${reff_detail}">
        {{-- <td class="text-center">${length}</td> --}}
        <td class="text-left">Daily Driver
          <input type="hidden" name="product_item[]" />
          <input type="hidden" name="drd_calculation_id[]" value="${id}"/>
          <input type="hidden" name="drd_additional_id[]" value="0"/>
          <input type="hidden" name="reff_detail_additional[]" value="${reff_detail}"/>
          <input type="hidden" placeholder="Description" name="description[]" class="form-control form-control-sm" value="Daily Driver"/>
        </td>
        <td class="text-right">${accounting.formatMoney(total, '', ',', '.')}
          <input type="hidden" placeholder="Value" name="value[]" class="form-control form-control-sm currency text-right onCalculation" required  value="${total}">
        </td>
        <td class="text-center">
          <a href="javascript:;" class="link-red remove" title="Delete">
            <i class="fa fa-trash"></i>
          </a>
        </td>
      </tr>
    `;

    $('#table-product > tbody').prepend(html);
    resetCount();
    dataTable.draw();
   
  }
  function selectAllowance(that)
  {
    var id = $(that).data('id');
    var total = $(that).data('total');
    var description = $(that).data('description');
     if ($.inArray(id, do_ids) != -1) {
          $.gritter.add({
              title: 'Warning!',
              text: 'Data already selected',
              class_name: 'gritter-warning',
              time: 1000,
          });
          return;
    } else {
      do_ids.push(id);
    }
    length = $('#table-allowance tr').length-2;
    var html = `
    <tr data-id="${id}" data-total="${total}">
        {{-- <td class="text-center">${length}</td> --}}
        <td class="text-left">${description}
          <input type="hidden" name="product_allowance[]" />
          <input type="hidden" name="driver_list_id[]" value="${id}"/>
          <input type="hidden" placeholder="Description" name="description_allowance[]" class="form-control form-control-sm" value="${description}"/>
        </td>
        <td class="text-right">${accounting.formatMoney(total, '', ',', '.')}
          <input type="hidden" placeholder="Value" name="value_allowance[]" class="form-control form-control-sm currency text-right onCalculation" required  value="${total}">
        </td>
        <td class="text-center">
          <a href="javascript:;" class="link-red remove" title="Delete">
            <i class="fa fa-trash"></i>
          </a>
        </td>
      </tr>
    `;

    $('#table-allowance > tbody').prepend(html);
    resetCount();
    dataTableAllowance.draw();
   
  }
  function selectAdditional(that)
  {
    var id = $(that).data('id');
    var reff_additional = $(that).data('reff_additional');
    var total = $(that).data('total');
    var description = $(that).data('description');
     if ($.inArray(id, so_ids) != -1) {
          $.gritter.add({
              title: 'Warning!',
              text: 'Data already selected',
              class_name: 'gritter-warning',
              time: 1000,
          });
          return;
    } else {
      so_ids.push(id);
    }
    length = $('#table-product tr').length-1;
    var html = `
    <tr data-id="${id}" data-total="${total}" data-reff_detail_additional="${reff_additional}">
        {{-- <td class="text-center">${length}</td> --}}
        <td class="text-left">${description}
          <input type="hidden" name="product_item[]" />
          <input type="hidden" name="drd_calculation_id[]" value="0"/>
          <input type="hidden" name="drd_additional_id[]" value="${id}"/>
          <input type="hidden" name="reff_detail_additional[]" value="${reff_additional}"/>
          <input type="hidden" placeholder="Description" name="description[]" class="form-control form-control-sm" value="${description}"/>
        </td>
        <td class="text-right">${accounting.formatMoney(total, '', ',', '.')}
          <input type="hidden" placeholder="Value" name="value[]" class="form-control form-control-sm currency text-right onCalculation" required  value="${total}">
        </td>
        <td class="text-center">
          <a href="javascript:;" class="link-red remove" title="Delete">
            <i class="fa fa-trash"></i>
          </a>
        </td>
      </tr>
    `;

    $('#table-product > tbody').prepend(html);
    resetCount();
    dataTableAdditional.draw();
   
  }
  function changePick(){
        so_ids = [];
        $('#get_day').val("");
        $('#dailyreportdriver_id').val("");
        $('#max_arrival').val("");
        $('#driver_id').val("");
        $("input[name='subtotal']").val(0);
        $("input[name='subtotalallowance']").val(0);
        $("input[name='grandtotal']").val(0);
        $('#table-product > tfoot').find('td[data-subtotal]').html(accounting.formatMoney(0, '', ',', '.'));
        $('#table-allowance > tfoot').find('td[data-subtotalallowance]').html(accounting.formatMoney(0, '', ',', '.'));
        $('#table-allowance > tfoot').find('td[data-grandtotal]').html(accounting.formatMoney(0, '', ',', '.'));
        $("#table-product tbody").empty();
        $("#table-allowance tbody").empty();

        $('#get_day').val($('#pick_id').select2('data').date);
        $('#dailyreportdriver_id').val($('#pick_id').select2('data').dailyreportdriver_id);
        $('#driver_id').val($('#pick_id').select2('data').driver_id);
        getData();
  }
  $(function() {
    $('.select2').select2();
    $('.rupiah').mask('000.000.000.000.000.000', {reverse: true});
    $("#pick_id" ).select2({
      ajax: {
        url: "{{route('reimbursement.readdailydriver')}}",
        type:'GET',
        dataType: 'json',
        data: function (term,page) {
          return {
            path:'Driver',
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
              text: item.name + ' - ' +  item.code,
              dailyreportdriver_id:item.id,
              driver_id:item.driver_id,
              date:item.date
            });
          });
          $("#pick_id").on("change", function(e) {
          changePick();
          });
          return {
            results: option, more: more,
          };
        },
      },
      allowClear: true,
    });
    
    @if($reimbursement->driver_id)
      $("#pick_id").select2('data',{id:{{$reimbursement->driver_id}},text:'{{$reimbursement->driver->name}} - {{$reimbursement->dailyreportdriver->code}}'}).trigger('change');
    @endif
    $('#table-product').on('click', '.remove', function () {
      var id = $(this).parents('tr').data('id');
      var drd_calculation_id = $(this).parents('tr').data('drd_calculation_id');
      var drd_additional_id = $(this).parents('tr').data('drd_additional_id');
      var btn = $(this);

      var data = {
        _token: "{{ csrf_token() }}",
        id : id,
        drd_calculation_id:drd_calculation_id,
        drd_additional_id:drd_additional_id
      };

			$.ajax({
				url: "{{route('reimbursement.updatestatuscalculation')}}",
				type: "POST",
				data: data,
				dataType: 'json',
				success: function (response) {
					if (response.status) {
            console.log(response.status);
						$(btn).parents('tr').remove();

            resetCount();
            dataTable.draw();
            dataTableAdditional.draw();
					} else {
            $.gritter.add({
                title: 'Warning!',
                text: response.message,
                class_name: 'gritter-warning',
                time: 1000,
            });

						return;
					}
				}
			});
		});
    $('#table-allowance').on('click','.remove',function(){
      var id = $(this).parents('tr').data('id');
      var btn = $(this);
			for (let i = 0; i < do_ids.length; i++) {
				if (do_ids[i] === id) {
					do_ids.splice(i, 1);
				}
			}

      var data = {
        _token: "{{ csrf_token() }}",
        id : id
      };

			$.ajax({
				url: "{{route('reimbursement.updatestatusallowance')}}",
				type: "POST",
				data: data,
				dataType: 'json',
				success: function (response) {
					if (response.status) {
            console.log(response.status);
						$(btn).parents('tr').remove();

						resetCount();
					} else {
            $.gritter.add({
                title: 'Warning!',
                text: response.message,
                class_name: 'gritter-warning',
                time: 1000,
            });

						return;
					}
				}
			});
    });
    $('.datepicker').daterangepicker({
      singleDatePicker: true,
      timePicker: false,
      timePickerIncrement: 1,
      locale: {
      format: 'DD/MM/YYYY'
      }
    });
    $('.timepicker').daterangepicker({
      singleDatePicker: true,
      timePicker: true,
      timePicker24Hour: true,
      timePickerIncrement: 1,
      timePickerSeconds: false,
      locale: {
        format: 'HH:mm'
      }
    }).on('show.daterangepicker', function(ev, picker) {
      picker.container.find('.calendar-table').hide();
    });
    
    $('#form').validate({
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
          }
        });
      }
    });
    $('.btnShowModal').click(function(e) {
			e.preventDefault();

			if ($('#pick_id').val() == "") {
                $.gritter.add({
                    title: 'Warning!',
                    text: 'Please choose a Driver first',
                    class_name: 'gritter-warning',
                    time: 1000,
                });

                return;
			}

      dataTable.draw();
      dataTableAdditional.draw();

			$('#addCalculationModal').modal('show');
    });
    $('.btnShowModalAllowance').click(function(e) {
			e.preventDefault();

			if ($('#pick_id').val() == "") {
                $.gritter.add({
                    title: 'Warning!',
                    text: 'Please choose a Driver first',
                    class_name: 'gritter-warning',
                    time: 1000,
                });

                return;
			}

      dataTableAllowance.draw();

			$('#addAllowanceModal').modal('show');
    });
    dataTable = $('#example3').DataTable( {
			// scrollX:true,
			processing: true,
			serverSide: true,
			filter:false,
			info:false,
			lengthChange:false,
			responsive: true,
			order: [[ 0, "desc" ]],
			ajax: {
				url: "{{route('dailyreportdriver.readcalculation')}}",
				type: "GET",
				data: function (data) {
          var driver_id = $('#driver_id').val();
          var dailyreportdriver_id = $('#dailyreportdriver_id').val();
          data.dailyreportdriver_id = dailyreportdriver_id;
          data.driver_id = driver_id;
          data.exclude_ids = so_ids;

					return data;
				}
			},
			columnDefs: [
				{
					orderable: false,
					targets:[-1]
				},
				{
					render: function (data, type, row) {
						return accounting.formatMoney(parseInt(data), '', ',', '.');
					},
					className: 'text-right',
					targets: [0,1,2,3]
				},
				{
					render: function(data, type, row) {
						return `
							<button type="button" class="btn bg-navy btn-xs color-palette btn-labeled legitRipple select-requestorder" onclick="selectCalculation(this)" data-id="${row.id}" data-total="${row.total}" data-reff_detail="${row.reff_detail}">
								<b><i class="fas fa-check"></i></b> Select
							</button>
						`;
					},
					className: 'text-center',
					targets: [-1]
				}
			],
			columns: [
				{ data: "parking" },
				{ data: "toll_money" },
				{ data: "oil" },
				{ data: "etc" },
				{ data: "id" }
			]
    });
    dataTableAdditional = $('#example4').DataTable( {
			// scrollX:true,
			processing: true,
			serverSide: true,
			filter:false,
			info:false,
			lengthChange:false,
			responsive: true,
			order: [[ 0, "desc" ]],
			ajax: {
				url: "{{route('dailyreportdriver.readadditional')}}",
				type: "GET",
				data: function (data) {
          var driver_id = $('#driver_id').val();
          var dailyreportdriver_id = $('#dailyreportdriver_id').val();
          data.dailyreportdriver_id = dailyreportdriver_id;
          data.driver_id = driver_id;
          data.exclude_ids = so_ids;

					return data;
				}
			},
			columnDefs: [
				{
					orderable: false,
					targets:[-1]
				},
				{
					render: function (data, type, row) {
						return accounting.formatMoney(parseInt(data), '', ',', '.');
					},
					className: 'text-right',
					targets: [0,1]
				},
				{
					render: function(data, type, row) {
						return `
							<button type="button" class="btn bg-navy btn-xs color-palette btn-labeled legitRipple select-requestorder" onclick="selectAdditional(this)" data-id="${row.id}" data-description="${row.additional_name}" data-total="${row.additional_total}" data-reff_additional="${row.reff_additional}">
								<b><i class="fas fa-check"></i></b> Select
							</button>
						`;
					},
					className: 'text-center',
					targets: [-1]
				}
			],
			columns: [
				{ data: "additional_name" },
				{ data: "additional_total" },
				{ data: "id" }
			]
    });
    dataTableAllowance = $('#table-pickallowance').DataTable( {
			// scrollX:true,
			processing: true,
			serverSide: true,
			filter:false,
			info:false,
			lengthChange:false,
			responsive: true,
			order: [[ 0, "desc" ]],
			ajax: {
				url: "{{route('reimbursement.readallowance')}}",
				type: "GET",
				data: function (data) {
          var driver_id    = $('#driver_id').val();
          var max_arrival  = $('#max_arrival').val();
          var get_day      = $('#get_day').val();
          data.driver_id   = driver_id;
          data.max_arrival = max_arrival;
          data.get_day     = get_day;
          data.do_ids      = do_ids;

					return data;
				}
			},
			columnDefs: [
				{
					orderable: false,
					targets:[-1]
				},
				{
					render: function (data, type, row) {
						return accounting.formatMoney(parseInt(data), '', ',', '.');
					},
					className: 'text-right',
					targets: [1]
				},
				{
					render: function(data, type, row) {
						return `
							<button type="button" class="btn bg-navy btn-xs color-palette btn-labeled legitRipple select-requestorder" onclick="selectAllowance(this)" data-id="${row.id}" data-description="${row.allowance}" data-total="${row.value}">
								<b><i class="fas fa-check"></i></b> Select
							</button>
						`;
					},
					className: 'text-center',
					targets: [-1]
				}
			],
			columns: [
				{ data: "allowance" },
				{ data: "value" },
				{ data: "id" }
			]
		});

  });
    
    function resetCount()
    {

      var subtotal		= 0;
      var subtotalAllowance		= 0;
      $('#table-product > tbody  > tr').each(function(index, tr) { 
        subtotal   	+= $(tr).find('input[name="value[]"]').val() * 1;
      });
      $('#table-product > tfoot').find('td[data-subtotal]').html(accounting.formatMoney(subtotal, '', ',', '.'));
      $("input[name='subtotal']").val(subtotal);

      {{-- Allowance --}}
      $('#table-allowance > tbody  > tr').each(function(index, tr) { 
        subtotalAllowance   	+= $(tr).find('input[name="value_allowance[]"]').val() * 1;
      });
      $('#table-allowance > tfoot').find('td[data-subtotalallowance]').html(accounting.formatMoney(subtotalAllowance, '', ',', '.'));
      $("input[name='subtotalallowance']").val(subtotalAllowance);

      grandTotal = subtotal + subtotalAllowance;
      $('#table-allowance > tfoot').find('td[data-grandtotal]').html(accounting.formatMoney(grandTotal, '', ',', '.'));
      $("input[name='grandtotal']").val(grandTotal);
      
    }
    function getData(){
        $.ajax({
            url: "{{route('reimbursement.getdata')}}",
            type: "GET",
            dataType:'json',
            data: {
                dailyreportdriver_id: $("#dailyreportdriver_id").val(),
            },
            success: function(response) {
              $('#max_arrival').val(response);
            }
        });
    }
</script>
@endpush