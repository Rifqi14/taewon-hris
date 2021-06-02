@extends('admin.layouts.app')

@section('title',__('employee.employ'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
{{-- <link rel="stylesheet" href="https://bosung.biiscorp.com/"> --}}
{{-- <link href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet"> --}}
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
<style>
	.editbalance {
		cursor: pointer;
	}

	.editbalance:hover {
		cursor: pointer;
		color: blue;
	}
</style>
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('employees.index')}}">{{ __('employee.employ') }}</a></li>
<li class="breadcrumb-item active">{{ __('general.edt') }}</li>
@endpush


@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card card-{{config('configs.app_theme')}} card-outline">
			<div class="card-header p-0 border-bottom-0">
				<ul class="nav nav-tabs" id="tab-employee">
					<li class="nav-item"><a class="nav-link active" href="#personal" data-toggle="tab">{{ __('employee.person_data') }}</a></li>
					<li class="nav-item"><a class="nav-link" href="#contract" data-toggle="tab">{{ __('employee.contract') }}</a></li>
					<li class="nav-item"><a class="nav-link" href="#career" data-toggle="tab">{{ __('employee.career_je') }}</a></li>
					<li class="nav-item"><a class="nav-link" href="#education" data-toggle="tab">{{ __('employee.education') }}</a></li>
					<li class="nav-item"><a class="nav-link" href="#insurance" data-toggle="tab">{{ __('employee.insurance') }}</a></li>
					<li class="nav-item"><a class="nav-link" href="#document" data-toggle="tab">{{ __('employee.document') }}</a></li>
					<li class="nav-item"><a class="nav-link" href="#salary" data-toggle="tab">{{ __('employee.salary') }}</a></li>
					<li class="nav-item"><a class="nav-link" href="#leave" data-toggle="tab">{{ __('employee.leave') }}</a></li>
					<li class="nav-item"><a class="nav-link" href="#attendance" data-toggle="tab">{{ __('employee.attend') }}</a></li>
				</ul>
			</div>
			<div class="tab-content">
				{{-- Tab Personal --}}
				<div class="tab-pane active" id="personal">
					<form id="form" action="{{route('employees.update',['id'=>$employee->id])}}" class="form-horizontal" method="post" name="form_employee" autocomplete="off">
						{{ csrf_field() }}
						@method('PUT')
						<div class="card-header">
							<h3 class="card-title">{{ __('employee.empdata') }}</h3>
							<div class="pull-right card-tools">
								<button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
								<a href="#" onClick="backurl()" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
							</div>
						</div>
						<div class="row">
							<div class="col-12">
								<div class="card-body">
									<div class="row">
										<div class="col-lg-8">
											<div class="row">
												<div class="col-sm-6">
													<div class="form-group">
														<label>{{ __('general.name') }} <b class="text-danger">*</b></label>
														<input type="text" class="form-control" placeholder="{{ __('general.name') }}" name="name" value="{{ $employee->name}}" required>
													</div>
												</div>
												<div class="col-sm-6">
													<!-- text input -->
													<div class="form-group">
														<label>NIK Bosung</label>
														<input type="text" class="form-control" placeholder="NIK Bosung" name="nid" value="{{ $employee->nid}}" readonly>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-6">
													<div class="form-group">
														<label>{{ __('department.dep') }} <b class="text-danger">*</b></label>
														<input type="text" class="form-control" name="department_id" id="department_id" data-placeholder="Select {{ __('department.dep') }}" required required>
													</div>
												</div>
												<div class="col-sm-6">
													<!-- text input -->
													<div class="form-group">
														<label>{{ __('position.pos') }} <b class="text-danger">*</b></label>
														<input type="text" class="form-control" name="title_id" id="title_id" data-placeholder="Select {{ __('position.pos') }}" required>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-6">
													<!-- text input -->
													<div class="form-group">
														<label>{{ __('employee.workcomb') }} <b class="text-danger">*</b></label>
														<input type="text" class="form-control" name="workgroup_id" id="workgroup_id" data-placeholder="Select {{ __('employee.workcomb') }}" required>
													</div>
												</div>
												<div class="col-sm-6">
													<div class="form-group">
														<label>{{ __('employee.grade') }} <b class="text-danger">*</b></label>
														<input type="text" class="form-control" name="grade_id" id="grade_id" data-placeholder="Select {{ __('employee.grade') }}" required>
													</div>
												</div>
											</div>
										</div>
										<div class="col-lg-4">
											<div class="row">
												<div class="col-sm-12">
													<!-- text input -->
													<div class="form-group">
														<label for="app_logo" class="col-sm-5 control-label">{{ __('employee.img') }}</label>
														<div class="col-sm-12" style="border:1px solid #bdc3c7; border-radius:5px; height:203px; padding-top:10px;">
															<input type="file" class="form-control" name="photo" id="picture" accept="image/*" value="{{ $employee->photo }}" />
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-8">
									<div class="card-header">
										<h3 class="card-title">{{ __('employee.person_data') }}</h3>
									</div>
									<div class="card-body">
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<label>No.KTP <b class="text-danger">*</b></label>
													<input type="text" class="form-control" placeholder="No.Ktp" name="nik" value="{{ $employee->nik }}" required>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label>NPWP</label>
													<input type="text" class="form-control" placeholder="NPWP" name="npwp" value="{{ $employee->npwp }}">
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-6">
												<!-- text input -->
												<div class="form-group">
													<label>{{ __('employee.pob') }} <b class="text-danger">*</b></label>
													<input type="text" class="form-control" name="place_of_birth" id="place_of_birth" data-placeholder="Select {{ __('employee.pob') }}" required>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label>{{ __('employee.birthday') }} <b class="text-danger">*</b></label>
													<div class="input-group">
														<div class="input-group-prepend">
															<span class="input-group-text">
																<i class="far fa-calendar-alt"></i>
															</span>
														</div>
														<input type="text" required name="birth_date" class="form-control datepicker" id="birth_date" placeholder="{{ __('employee.birthday') }}" value="{{ date('d/m/Y',strtotime($employee->birth_date)) }}">
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<label>{{ __('employee.gender') }} <b class="text-danger">*</b></label>
													<select name="gender" id="gender" class="form-control select2" data-placeholder="Select {{ __('employee.gender') }}">
														<option value="male" @if($employee->gender == 'male') selected @endif>{{ __('general.m') }}</option>
														<option value="female" @if($employee->gender == 'female') selected @endif>{{ __('general.f') }}</option>
													</select>
												</div>
											</div>
											<div class="col-sm-6">
												<!-- text input -->
												<div class="form-group">
													<label> {{ __('employee.biomoth') }} <b class="text-danger">*</b></label>
													<input type="text" class="form-control" placeholder=" {{ __('employee.biomoth') }}" name="mother_name" value="{{ $employee->mother_name }}">
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-6">
												<!-- text input -->
												<div class="form-group">
													<label>{{ __('employee.bpjs') }}</label>
													<input type="text" class="form-control" placeholder="{{ __('employee.bpjs') }}" name="bpjs_tenaga_kerja" value="{{ $employee->bpjs_tenaga_kerja }}">
												</div>
											</div>
											<div class="col-sm-6">
												<!-- text input -->
												<div class="form-group">
													<label>PTKP <b class="text-danger">*</b></label>
													<select name="ptkp" id="ptkp" class="form-control select2" data-placeholder="Select PTKP">
														<option value=""></option>
														@foreach(config('enums.ptkp') as $key => $value)
														<option @if ($employee->ptkp == $key) selected @endif
															value="{{ $key }}">{{ $value }}</option>
														@endforeach
													</select>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-6">
												<!-- text input -->
												<div class="form-group">
													<label>{{ __('employee.nophone') }} <b class="text-danger">*</b></label>
													<input type="number" required class="form-control" placeholder="{{ __('employee.nophone') }}" name="phone" value="{{ $employee->phone }}">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label>Email <b class="text-danger">*</b></label>
													<div class="input-group">
														<div class="input-group-prepend">
															<span class="input-group-text">
																<i class="fas fa-envelope"></i>
															</span>
														</div>
														<input type="email" name="email" class="form-control" id="email" placeholder="Email" value="{{ $employee->email }}">
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-12">
												<!-- text input -->
												<div class="form-group">
													<label>{{ __('employee.address') }} <b class="text-danger">*</b></label>
													<input type="text" required class="form-control" placeholder="{{ __('employee.address') }}" name="address" value="{{ $employee->address }}">
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<label>{{ __('general.province') }} <b class="text-danger">*</b></label>
													<input type="text" required class="form-control" name="province_id" id="province_id" data-placeholder="Pilih {{ __('general.province') }}" required>
												</div>
											</div>
											<div class="col-sm-6">
												<!-- text input -->
												<div class="form-group">
													<label>{{ __('employee.city') }} <b class="text-danger">*</b></label>
													<input type="text" required class="form-control" name="region_id" id="region_id" data-placeholder="Select {{ __('employee.city') }}" required>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-6">
												<!-- text input -->
												<div class="form-group">
													<label>{{ __('employee.acc_no') }}</label>
													<div class="row">
														<div class="col-sm-4">
															<input type="text" class="form-control" name="account_bank" placeholder="Bank" value="{{ $employee->account_bank }}">
														</div>
														<div class="col-sm-8">
															<input type="number" class="form-control" name="account_no" placeholder="{{ __('employee.acc_no') }}" value="{{ $employee->account_no }}">
														</div>
													</div>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label>{{ __('employee.acc_name') }}</label>
													<input type="text" class="form-control" placeholder="{{ __('employee.acc_name') }}" name="account_name" value="{{ $employee->account_name }}">
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-6">
												<!-- text input -->
												<div class="form-group">
													<label>{{ __('employee.emr_no') }} <b class="text-danger">*</b></label>
													<input type="number" required class="form-control" placeholder="{{ __('employee.emr_no') }}" name="emergency_contact_no" value="{{ $employee->emergency_contact_no }}">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label>{{ __('employee.emr_name') }} <b class="text-danger">*</b></label>
													<input type="text" required class="form-control" placeholder="{{ __('employee.emr_name') }}" name="emergency_contact_name" value="{{ $employee->emergency_contact_name }}">
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-6">
												<!-- text input -->
												<div class="form-group">
													<label>{{ __('employee.worktype') }} <b class="text-danger">*</b></label>
													<select name="working_time_type" id="working_time_type" class="form-control select2" readonly data-placeholder="Select {{ __('employee.worktype') }}">
														@foreach(config('enums.workingtime_type') as $key => $value)
														<option @if ($employee->working_time_type == $key) selected @endif
															value="{{ $key }}">{{ $value }}</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label>{{ __('employee.workingtime') }}</label>
													<input type="text" class="form-control" name="working_time" id="working_time" data-placeholder="Pilih {{ __('employee.workingtime') }}">
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-6">
												<!-- text input -->
												<div class="form-group">
													<label>{{ __('calendar.calendar') }} <b class="text-danger">*</b></label>
													<input class="form-control" type="text" name="calendar_id" id="calendar_id" required data-placeholder="Select {{ __('calendar.calendar') }}">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label>{{ __('employee.tax_cal') }} <b class="text-danger">*</b></label>
													<select name="tax_calculation" id="calculation" class="form-control select2" data-placeholder="Select {{ __('employee.tax_cal') }}">
														@foreach(config('enums.calculation') as $key => $value)
														<option @if ($employee->tax_calculation == $key) selected @endif
															value="{{ $key }}">{{ $value }}</option>
														@endforeach
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-4">
									<div class="card-header">
										<h3 class="card-title">{{ __('general.other') }}</h3>
									</div>
									<div class="card-body">
										<div class="row">
											<div class="col-sm-12">
												<!-- text input -->
												<div class="form-group">
													<label>Status <b class="text-danger">*</b></label>
													<select name="status" id="status" class="form-control select2" data-placeholder="Select Status">
														<option @if($employee->status == 1) selected @endif value="1">{{ __('general.actv') }}</option>
														<option @if($employee->status == 0) selected @endif value="0">{{ __('general.noactv') }}</option>
													</select>
												</div>
											</div>
											<div class="col-sm-12">
												<div class="form-group">
													<label>{{ __('general.notes') }}</label>
													<textarea type="text" class="form-control" name="notes" placeholder="{{ __('general.notes') }}">{{ $employee->notes }}</textarea>
												</div>
											</div>
											<div class="col-sm-12">
												<div class="form-group">
													<label>{{ __('employee.join_labor') }} <b class="text-danger">*</b></label>
													<select id="join" class="form-control select2" name="join" data-placeholder="Select {{ __('employee.join_labor') }}">
														<option @if($employee->join == "yes") selected @endif
															value="yes">{{ __('general.yes') }}</option>
														<option @if($employee->join == "no") selected @endif value="no">No
														</option>
													</select>
												</div>
											</div>
											<div class="col-sm-12">
												<div class="form-group">
													<label>{{ __('employee.outsrc') }}</label>
													<input type="text" class="form-control" name="outsourcing_id" id="outsourcing_id" placeholder="{{ __('employee.outsrc') }}" readonly>
												</div>
											</div>
											<div class="col-sm-12">
												<div class="form-group">
													<label>{{ __('employee.ot') }} <b class="text-danger">*</b></label>
													<select id="join" class="form-control select2" name="overtime" data-placeholder="{{ __('employee.ot') }}">
														<option @if($employee->overtime == "yes") selected @endif
															value="yes">{{ __('general.yes') }}</option>
														<option @if($employee->overtime == "no") selected @endif
															value="no">{{ __('general.no') }}</option>
													</select>
												</div>
											</div>
											<div class="col-sm-12">
												<div class="form-group">
													<label>{{ __('general.to') }} <b class="text-danger">*</b></label>
													<select id="timeout" class="form-control select2" name="timeout" data-placeholder="{{ __('general.to') }}">
														<option value=""></option>
														<option @if($employee->timeout == "yes") selected @endif value="yes">{{ __('general.yes') }}</option>
														<option @if($employee->timeout == "no") selected @endif value="no">{{ __('general.no') }}</option>
													</select>
												</div>
											</div>
											<div class="col-sm-12">
												<div class="form-group">
													<label>SPL <b class="text-danger">*</b></label>
													<select id="spl" class="form-control select2" name="spl"
														data-placeholder="Select SPL" required>
														<option value=""></option>
														<option @if($employee->spl == "yes") selected @endif value="yes">{{ __('general.yes') }}</option>
														<option @if($employee->spl == "no") selected @endif value="no">{{ __('general.no') }}</option>
													</select>
												</div>
											</div>
											<div class="col-sm-12">
												<div class="form-group">
													<label>{{ __('general.jd') }} <b class="text-danger">*</b></label>
													<div class="input-group">
														<div class="input-group-prepend">
															<span class="input-group-text">
																<i class="far fa-calendar-alt"></i>
															</span>
														</div>
														<input type="text" name="join_date" class="form-control datepicker" id="join_date" placeholder="{{ __('general.jd') }}" value="{{ date('d/m/Y',strtotime($employee->join_date)) }}">
													</div>
												</div>
											</div>
											<div class="col-sm-12">
												<div class="form-group">
													<label>{{ __('general.rd') }}</label>
													<div class="input-group">
														<div class="input-group-prepend">
															<span class="input-group-text">
																<i class="far fa-calendar-alt"></i>
															</span>
														</div>
														<input type="text" name="resign_date" class="form-control datepicker" id="resign_date" placeholder="{{ __('general.rd') }}" value="{{ $employee->resign_date?date('d/m/Y',strtotime($employee->resign_date)):'' }}">
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="overlay d-none">
							<i class="fa fa-2x fa-sync-alt fa-spin"></i>
						</div>
					</form>
				</div>
				{{-- .Tab Personal --}}
				{{-- Tab Contarct Information --}}
				<div class="tab-pane" id="contract">
					<div class="card-header">
						<h3 class="card-title">{{ __('employee.contract') }}</h3>
						<div class="pull-right card-tools">
							<a href="#" class="btn btn-{{config('configs.app_theme')}} btn-sm text-white add_contract" data-toggle="tooltip" title="Tambah">
								<i class="fa fa-plus"></i>
							</a>
						</div>
					</div>
					<div class="card-body">
						<table class="table table-bordered table-striped" style="width:100%" id="table-contract">
							<thead>
								<tr>
									<th style="text-align:center" width="10">No</th>
									<th width="100">{{ __('employee.contract') }}</th>
									<th width="100">{{ __('employee.period') }}</th>
									<th width="200">{{ __('general.desc') }}</th>
									<th width="50">File</th>
									<th width="50">Status</th>
									<th width="50" style="text-align:center">{{ __('general.act') }}</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				{{-- .Tab Contract Information --}}
				{{-- Tab Carrer --}}
				<div class="tab-pane" id="career">
					<div class="card-header">
						<h3 class="card-title">{{ __('employee.career') }}</h3>
						<div class="pull-right card-tools">
							<a href="#" class="btn btn-{{config('configs.app_theme')}} btn-sm text-white add_career" data-toggle="tooltip" title="Tambah">
								<i class="fa fa-plus"></i>
							</a>
						</div>
					</div>
					<div class="card-body">
						<table class="table table-bordered table-striped" id="table-career" style="width:100%">
							<thead>
								<tr>
									<th style="text-align:center" width="10">No</th>
									<th width="150">{{ __('position.pos') }}</th>
									<th width="50">{{ __('employee.grade') }}</th>
									<th width="150">{{ __('department.dep') }}</th>
									<th width="150">{{ __('employee.period') }}</th>
									<th width="150">{{ __('employee.ref') }}</th>
									<th width="100">{{ __('general.act') }}</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
					<div class="card-header">
						<h3 class="card-title">{{ __('employee.je') }}</h3>
						<div class="pull-right card-tools">
							<a href="#" class="btn btn-{{config('configs.app_theme')}} btn-sm text-white add_experience" data-toggle="tooltip" title="Tambah">
								<i class="fa fa-plus"></i>
							</a>
						</div>
					</div>
					<div class="card-body">
						<table class="table table-bordered table-striped" id="table-experience" style="width:100%">
							<thead>
								<tr>
									<th style="text-align:center" width="10">No</th>
									<th width="150">{{ __('employee.last_pos') }}</th>
									<th width="150">{{ __('employee.company') }}</th>
									<th width="150">{{ __('employee.period') }}</th>
									<th width="150">{{ __('employee.duration') }}</th>
									<th width="100">{{ __('general.act') }}</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				{{-- .Tab Carrer --}}
				{{-- Tab Education --}}
				<div class="tab-pane" id="education">
					<div class="card-header">
						<h3 class="card-title">{{ __('employee.edu') }}</h3>
						<div class="pull-right card-tools">
							<a href="#" class="btn btn-{{config('configs.app_theme')}} btn-sm text-white add_education" data-toggle="tooltip" title="Tambah">
								<i class="fa fa-plus"></i>
							</a>
						</div>
					</div>
					<div class="card-body">
						<table class="table table-bordered table-striped" id="table-education" style="width: 100%">
							<thead>
								<tr>
									<th style="text-align:center" width="10">No</th>
									<th width="200">{{ __('employee.institution') }}</th>
									<th width="100">{{ __('employee.stage') }}</th>
									<th width="100">{{ __('employee.period') }}</th>
									<th width="100">{{ __('general.act') }}</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>

					<div class="card-header">
						<h3 class="card-title">{{ __('employee.training') }}</h3>
						<div class="pull-right card-tools">
							<a href="#" class="btn btn-{{config('configs.app_theme')}} btn-sm text-white add_training" data-toggle="tooltip" title="Tambah">
								<i class="fa fa-plus"></i>
							</a>
						</div>
					</div>
					<div class="card-body">
						<table class="table table-bordered table-striped" id="table-training" style="width:100%">
							<thead>
								<tr>
									<th style="text-align:center" width="10">No</th>
									<th width="150">{{ __('employee.doc_no') }}</th>
									<th width="150">{{ __('employee.issuedby') }}</th>
									<th width="200">{{ __('general.date') }} </th>
									<th width="250">{{ __('general.desc') }}</th>
									<th width="100">{{ __('general.act') }}</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				{{-- .Tab Education --}}
				{{-- Tab Insurance --}}
				<div class="tab-pane" id="insurance">
					<div class="card-header">
						<h3 class="card-title">{{ __('employee.co_bpjs') }}</h3>
						<div class="pull-right card-tools">
							<a href="#" class="btn btn-{{config('configs.app_theme')}} btn-sm text-white add_bpjs" data-toggle="tooltip" title="Tambah">
								<i class="fa fa-plus"></i>
							</a>
						</div>
					</div>
					<div class="card-body">
						<table class="table table-bordered table-striped" style="width: 100%" id="table-bpjs">
							<thead>
								<tr>
									<th style="text-align:center" width="10">No</th>
									<th width="200">{{ __('general.name') }}</th>
									<th width="150">NIK</th>
									<th width="100">{{ __('employee.relation') }}</th>
									<th width="150">{{ __('employee.address') }}</th>
									<th width="100">{{ __('general.act') }}</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
					<hr>
					<div class="card-header">
						<h3 class="card-title">{{ __('employee.add_member') }}</h3>
						<div class="pull-right card-tools">
							<a href="#" class="btn btn-{{config('configs.app_theme')}} btn-sm text-white add_member" data-toggle="tooltip" title="Tambah">
								<i class="fa fa-plus"></i>
							</a>
						</div>
					</div>
					<div class="card-body">
						<table class="table table-bordered table-striped" style="width: 100%" id="table-member">
							<thead>
								<tr>
									<th style="text-align:center" width="10">No</th>
									<th width="200">{{ __('general.name') }}</th>
									<th width="150">NIK</th>
									<th width="100">{{ __('employee.relation') }}</th>
									<th width="150">{{ __('employee.address') }}</th>
									<th width="100">{{ __('general.act') }}</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				{{-- .Tab Insurance --}}
				{{-- Tab Document --}}
				<div class="tab-pane" id="document">
					<div class="card-header">
						<h3 class="card-title">{{ __('employee.document') }}</h3>
						<div class="pull-right card-tools">
							<a href="#" class="btn btn-{{config('configs.app_theme')}} btn-sm text-white add_document" data-toggle="tooltip" title="Tambah">
								<i class="fa fa-plus"></i>
							</a>
						</div>
					</div>
					<div class="card-body">
						<table class="table table-bordered table-striped" style="width:100%" id="table-document">
							<thead>
								<tr>
									<th style="text-align:center" width="10">#</th>
									<th width="100">{{ __('general.category') }}</th>
									<th width="200">{{ __('employee.docname') }}</th>
									<th width="200">{{ __('general.desc') }}</th>
									<th width="100">File</th>
									<th width="10">#</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
				{{-- .Tab Document --}}
				{{-- Tab Salary --}}
				<div class="tab-pane" id="salary">
					<div class="card-body">
						<div class="row">
							<div class="col-3 col-sm-2">
								<div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist" aria-orientation="vertical">
									<a class="nav-link active" id="vert-tabs-home-tab" data-toggle="pill" href="#vert-tabs-home" role="tab" aria-controls="vert-tabs-home" aria-selected="true">{{ __('employee.bscslr') }}</a>
									<a class="nav-link" id="vert-tabs-gross-tab" data-toggle="pill" href="#vert-tabs-gross" role="tab" aria-controls="vert-tabs-gross" aria-selected="true">{{ __('employee.grosslr') }}</a>
									<a class="nav-link" id="vert-tabs-profile-tab" data-toggle="pill" href="#vert-tabs-profile" role="tab" aria-controls="vert-tabs-profile" aria-selected="false">{{ __('allowance.alw') }}</a>
									@if ($employee->department->driver == 'yes')
									<a class="nav-link" id="driver-allowance-tab" data-toggle="pill" href="#tabs-driver-allowance" role="tab" aria-controls="vert-tabs-messages" aria-selected="false">{{ __('employee.driveralw') }}</a>
									@endif
									<a class="nav-link" id="vert-tabs-messages-tab" data-toggle="pill" href="#vert-tabs-messages" role="tab" aria-controls="vert-tabs-messages" aria-selected="false">{{ __('employee.ot') }}</a>
									@if ($employee->workgroup->penalty == 'Basic')
									<a class="nav-link" id="vert-tabs-messages-tab" data-toggle="pill" href="#vert-tabs-penalti" role="tab" aria-controls="vert-tabs-messages" aria-selected="false">Potongan Absen</a>
									@endif
								</div>
							</div>
							<div class="col-9 col-sm-10">
								<div class="tab-content" id="vert-tabs-tabContent">
									<div class="tab-pane text-left fade show active" id="vert-tabs-home" role="tabpanel" aria-labelledby="vert-tabs-home-tab">
										<div class="card-header">
											<p class="card-title" style="padding-left:-25px;">{{ __('employee.salary') }}</p>
											<div class="pull-right card-tools">
												<a href="#" class="btn btn-{{config('configs.app_theme')}} btn-sm text-white add_salary" data-toggle="tooltip" title="Tambah"><i class="fa fa-plus"></i></a>
											</div>
										</div>
										<table class="table table-bordered table-striped" id="table-salary">
											<thead>
												<tr>
													<th style="text-align:center" width="10">#</th>
													<th width="200">{{ __('employee.amount') }}</th>
													<th width="150">{{ __('general.desc') }}</th>
													<th width="150">{{ __('employee.updateby') }}</th>
													<th width="150">{{ __('employee.updatetime') }}</th>
												</tr>
											</thead>
										</table>
									</div>
									<div class="tab-pane fade" id="vert-tabs-gross" role="tabpanel" aria-labelledby="vert-tabs-gross-tab">
										<div class="card-header">
											<p class="card-title">{{ __('employee.grosslr') }}</p>
										</div>
										<div class="card-body">
											<div class="form-group row">
												<label for="period" class="col-sm-2 col-md-1 form-label">{{ __('employee.period') }}</label>
												<div class="col-sm-5 col-md-2">
													<select class="form-control select2" name="month-gross" id="month-gross">
														<option value="01" @if (date('m', time())=="01" ) selected @endif>January</option>
														<option value="02" @if (date('m', time())=="02" ) selected @endif>February</option>
														<option value="03" @if (date('m', time())=="03" ) selected @endif>Maret</option>
														<option value="04" @if (date('m', time())=="04" ) selected @endif>April</option>
														<option value="05" @if (date('m', time())=="05" ) selected @endif>Mei</option>
														<option value="06" @if (date('m', time())=="06" ) selected @endif>Juni</option>
														<option value="07" @if (date('m', time())=="07" ) selected @endif>July</option>
														<option value="08" @if (date('m', time())=="08" ) selected @endif>Agustus</option>
														<option value="09" @if (date('m', time())=="09" ) selected @endif>September</option>
														<option value="10" @if (date('m', time())=="10" ) selected @endif>Oktober</option>
														<option value="11" @if (date('m', time())=="11" ) selected @endif>November</option>
														<option value="12" @if (date('m', time())=="12" ) selected @endif>Desember</option>
													</select>
												</div>
												<div class="col-sm-5 col-md-2">
													@php
													$thn_skr = date('Y');
													@endphp
													<select class="form-control select2" name="year-gross" id="year-gross">
														@for ($i = $thn_skr; $i >= 1991; $i--)
														<option value="{{ $i }}">{{ $i }}</option>
														@endfor
													</select>
												</div>
											</div>
												<table class="table table-striped table-bordered" id="gross-table" style="width: 100%">
													<thead>
														<tr>
														<th width="10">No</th>
														<th width="600">{{ __('general.desc') }}</th>
														<th width="200">Total</th>
														</tr>
													</thead>
													<tfoot>
														<tr>
														<th colspan="2" class="text-right">Total</th>
														<th id="gross" data-gross="1400"></th>
														</tr>
													</tfoot>
												</table>
										</div>
									</div>
									<div class="tab-pane fade" id="vert-tabs-profile" role="tabpanel" aria-labelledby="vert-tabs-profile-tab">
										<div class="card-header">
											<p class="card-title" style="padding-left:-25px;">{{ __('allowance.alw') }}</p>
											<div class="pull-right card-tools">
												<a href="#" class="btn btn-{{ config('configs.app_theme') }} btn-sm text-white add_allowance_data" data-toggle="tooltip" title="Tambah"><i class="fa fa-plus"></i></a>
												<a href="#" onclick="filter()" class="btn btn-default btn-sm" data-toggle="tooltip" title="Search"><i class="fa fa-search"></i></a>
											</div>
										</div>
										<div class="card-body">
											<div class="form-group row">
												<label for="inputEmail3" class="col-sm-1 col-form-label">{{ __('employee.period') }}</label>
												<div class="row col-sm-11">
													<input type="hidden" name="employee_id" value="{{ $employee->id }}">
													<div class="col-sm-4">
														<select class="form-control select2" name="montly" id="montly">
															<option value="01" @if (date('m', time())=="01" ) selected @endif>January</option>
															<option value="02" @if (date('m', time())=="02" ) selected @endif>February</option>
															<option value="03" @if (date('m', time())=="03" ) selected @endif>March</option>
															<option value="04" @if (date('m', time())=="04" ) selected @endif>April</option>
															<option value="05" @if (date('m', time())=="05" ) selected @endif>May</option>
															<option value="06" @if (date('m', time())=="06" ) selected @endif>June</option>
															<option value="07" @if (date('m', time())=="07" ) selected @endif>July</option>
															<option value="08" @if (date('m', time())=="08" ) selected @endif>August</option>
															<option value="09" @if (date('m', time())=="09" ) selected @endif>September</option>
															<option value="10" @if (date('m', time())=="10" ) selected @endif>October</option>
															<option value="11" @if (date('m', time())=="11" ) selected @endif>November</option>
															<option value="12" @if (date('m', time())=="12" ) selected @endif>December</option>
														</select>
													</div>
													<div class="col-sm-2" style="padding:0;">
														<div class="input-group">
															<select name="year" class="form-control select2" id="year">
																@php
																$thn_skr = date('Y');
																@endphp
																@for ($i = $thn_skr; $i >= 1991; $i--)
																<option value="{{ $i }}">{{ $i }}</option>
																@endfor
															</select>
														</div>
													</div>
													<div class="col-sm-2" style="padding-bottom:0;">
														<button type="button" onclick="generate()" class="btn btn-{{ config('configs.app_theme') }}">Generate</button>
													</div>
												</div>
											</div>
											<!-- <div class="form-group row">
													<label class="col-sm-1 form-label" for="period">Period</label>
													<div class="row">
														<input type="hidden" name="employee_id" value="{{ $employee->id }}">
														<div class="col-sm-6">
															<select class="form-control select2" name="montly" id="montly">
																<option value="01" @if (date('m', time()) == "01") selected @endif>Jan</option>
																<option value="02" @if (date('m', time()) == "02") selected @endif>Feb</option>
																<option value="03" @if (date('m', time()) == "03") selected @endif>Mar</option>
																<option value="04" @if (date('m', time()) == "04") selected @endif>Apr</option>
																<option value="05" @if (date('m', time()) == "05") selected @endif>May</option>
																<option value="06" @if (date('m', time()) == "06") selected @endif>Jun</option>
																<option value="07" @if (date('m', time()) == "07") selected @endif>July</option>
																<option value="08" @if (date('m', time()) == "08") selected @endif>Aug</option>
																<option value="09" @if (date('m', time()) == "09") selected @endif>Sep</option>
																<option value="10" @if (date('m', time()) == "10") selected @endif>Oct</option>
																<option value="11" @if (date('m', time()) == "11") selected @endif>Nov</option>
																<option value="12" @if (date('m', time()) == "12") selected @endif>Dec</option>
															</select>
														</div>
														<div class="col-sm-6" style="padding:0;">
															<div class="input-group">
																<select name="year" class="form-control" id="">
																	@php
																		$thn_skr = date('Y');
																	@endphp
																	@for ($i = $thn_skr; $i >= 1991; $i--)
																	<option value="{{ $i }}">{{ $i }}</option>
																	@endfor
																</select>
															</div>
														</div>
														<div class="col-sm-2" style="padding-bottom:0;">
															<button type="button" onclick="generate()" class="btn btn-{{ config('configs.app_theme') }}">Generate</button>
														</div>
													</div>
												</div> -->
											<table class="table table-bordered table-striped" id="table-allowance" width="100%">
												<thead>
													<tr>
														<th style="text-align:center" width="10">#</th>
														<th width="150">{{ __('employee.period') }}</th>
														<th width="200">{{ __('allowance.alw') }}</th>
														<th width="200">{{ __('general.category') }}</th>
														<th width="200">{{ __('allowance.recur') }}</th>
														<th width="150">{{ __('employee.factor') }}</th>
														<th width="150">{{ __('general.value') }}</th>
														<th width="150">Status</th>
														@if ($employee->workgroup->penalty == 'Gross')
														<th width="150">{{ __('employee.pnlty') }}</th>
														@endif
														<th width="100">{{ __('general.act') }}</th>
													</tr>
												</thead>
											</table>
										</div>
									</div>
									<div class="tab-pane fade" id="tabs-driver-allowance" role="tabpanel" aria-labelledby="driver-allowance-tab">
										<div class="card-header">
											<p class="card-title">{{ __('employee.driveralw') }}</p>
										</div>
										<div class="card-body">
											<div class="form-group row">
												<label for="period" class="col-sm-2 col-md-1 form-label">{{ __('employee.period') }}</label>
												<div class="col-sm-5 col-md-2">
													<select class="form-control select2" name="month-driver" id="month-driver">
														<option value="01" @if (date('m', time())=="01" ) selected @endif>January</option>
														<option value="02" @if (date('m', time())=="02" ) selected @endif>February</option>
														<option value="03" @if (date('m', time())=="03" ) selected @endif>Maret</option>
														<option value="04" @if (date('m', time())=="04" ) selected @endif>April</option>
														<option value="05" @if (date('m', time())=="05" ) selected @endif>Mei</option>
														<option value="06" @if (date('m', time())=="06" ) selected @endif>Juni</option>
														<option value="07" @if (date('m', time())=="07" ) selected @endif>July</option>
														<option value="08" @if (date('m', time())=="08" ) selected @endif>Agustus</option>
														<option value="09" @if (date('m', time())=="09" ) selected @endif>September</option>
														<option value="10" @if (date('m', time())=="10" ) selected @endif>Oktober</option>
														<option value="11" @if (date('m', time())=="11" ) selected @endif>November</option>
														<option value="12" @if (date('m', time())=="12" ) selected @endif>Desember</option>
													</select>
												</div>
												<div class="col-sm-5 col-md-2">
													@php
													$thn_skr = date('Y');
													@endphp
													<select class="form-control select2" name="year-driver" id="year-driver">
														@for ($i = $thn_skr; $i >= 1991; $i--)
														<option value="{{ $i }}">{{ $i }}</option>
														@endfor
													</select>
												</div>
											</div>
											<table class="table table-bordered table-striped w-100" id="table-driver-allowance">
												<thead>
													<tr>
														<th width="5" class="text-right">#</th>
														<th width="10">{{ __('general.date') }}</th>
														<th width="10">Kloter</th>
														<th width="10">{{ __('general.value') }}</th>
														<th width="10">RIT</th>
														<th width="10">Total</th>
														{{-- <th width="10">Action</th> --}}
													</tr>
												</thead>
												<tfoot>
													<tr>
														<td colspan="5" class="text-right"><b>Grand Total</b></td>
														<td>
															<span id="total" class="text-right" placeholder="Total"></span>
														</td>
													</tr>
												</tfoot>
											</table>
										</div>
									</div>
									<div class="tab-pane fade" id="vert-tabs-messages" role="tabpanel" aria-labelledby="vert-tabs-messages-tab">
										<div class="card-header">
											<p class="card-title" style="padding-left:-25px;">{{ __('employee.ot') }}</p>
										</div>
										<div class="card-body">
											<div class="form-group row">
												<label class="col-sm-1 form-label" for="period">{{ __('employee.period') }}</label>
												<div class="col-sm-6 row">
													<input type="hidden" name="employee_id_overtime" value="{{ $employee->id }}">
													<div class="col-sm-5">
														<select class="form-control select2" name="montly_overtime" id="montly_overtime">
															<option value="01" @if (date('m', time())=="01" ) selected @endif>January</option>
															<option value="02" @if (date('m', time())=="02" ) selected @endif>February</option>
															<option value="03" @if (date('m', time())=="03" ) selected @endif>Maret</option>
															<option value="04" @if (date('m', time())=="04" ) selected @endif>April</option>
															<option value="05" @if (date('m', time())=="05" ) selected @endif>Mei</option>
															<option value="06" @if (date('m', time())=="06" ) selected @endif>Juni</option>
															<option value="07" @if (date('m', time())=="07" ) selected @endif>July</option>
															<option value="08" @if (date('m', time())=="08" ) selected @endif>Agustus</option>
															<option value="09" @if (date('m', time())=="09" ) selected @endif>September</option>
															<option value="10" @if (date('m', time())=="10" ) selected @endif>Oktober</option>
															<option value="11" @if (date('m', time())=="11" ) selected @endif>November</option>
															<option value="12" @if (date('m', time())=="12" ) selected @endif>Desember</option>
														</select>
													</div>
													<div class="col-sm-4">
														<div class="input-group">
															<select name="year_overtime" class="form-control select2" id="year_overtime">
																@php
																$thn_skr = date('Y');
																@endphp
																@for ($i = $thn_skr; $i >= 1991; $i--)
																<option value="{{ $i }}">{{ $i }}</option>
																@endfor
															</select>
														</div>
													</div>
													<div class="col-sm-2" style="padding-bottom:0;">
													</div>
												</div>
											</div>
										</div>
										<table class="table table-bordered table-striped" id="table-overtime" width="100%">
											<thead>
												<tr>
													<th style="text-align:center" width="10">#</th>
													<th width="200">{{ __('general.day') }}</th>
													<th width="150">Rule</th>
													<th width="150">{{ __('general.hour') }}</th>
													<th width="150">{{ __('employee.amount') }}</th>
													<th width="150">{{ __('employee.bscslr') }} / {{ __('general.day') }}</th>
													<th width="150">Total</th>
													<th width="150">{{ __('general.act') }}</th>
												</tr>
											</thead>
											<tfoot>
												<tr>
													<td colspan="6" class="text-right"><b>Grand Total</b></td>
													<td class="text-left" colspan="2">
														<span id="total" class="text-right" placeholder="Total"></span>
													</td>
												</tr>
											</tfoot>
										</table>
									</div>
									<div class="tab-pane fade" id="vert-tabs-penalti" role="tabpanel" aria-labelledby="vert-tabs-messages-tab">
										<div class="card-header">
											<p class="card-title" style="padding-left:-25px;">{{ __('employee.pnlty') }}</p>
										</div>
										<div class="card-body">
											<div class="form-group row">
												<label class="col-sm-1 form-label" for="period">{{ __('employee.period') }}</label>
												<div class="col-sm-6 row">
													{{-- <input type="text" name="employee_id_penalty" value="{{ $employee->id }}"> --}}
													<div class="col-sm-5">
														<select class="form-control select2" name="montly_penalty" id="montly_penalty">
															<option value="01" @if (date('m', time())=="01" ) selected @endif>January</option>
															<option value="02" @if (date('m', time())=="02" ) selected @endif>February</option>
															<option value="03" @if (date('m', time())=="03" ) selected @endif>Maret</option>
															<option value="04" @if (date('m', time())=="04" ) selected @endif>April</option>
															<option value="05" @if (date('m', time())=="05" ) selected @endif>Mei</option>
															<option value="06" @if (date('m', time())=="06" ) selected @endif>Juni</option>
															<option value="07" @if (date('m', time())=="07" ) selected @endif>July</option>
															<option value="08" @if (date('m', time())=="08" ) selected @endif>Agustus</option>
															<option value="09" @if (date('m', time())=="09" ) selected @endif>September</option>
															<option value="10" @if (date('m', time())=="10" ) selected @endif>Oktober</option>
															<option value="11" @if (date('m', time())=="11" ) selected @endif>November</option>
															<option value="12" @if (date('m', time())=="12" ) selected @endif>Desember</option>
														</select>
													</div>
													<div class="col-sm-4">
														<div class="input-group">
															<select name="year_penalty" class="form-control select2" id="year_penalty">
																@php
																$thn_skr = date('Y');
																@endphp
																@for ($i = $thn_skr; $i >= 1991; $i--)
																<option value="{{ $i }}">{{ $i }}</option>
																@endfor
															</select>
														</div>
													</div>
													<div class="col-sm-2" style="padding-bottom:0;">
													</div>
												</div>
											</div>
										</div>
										<table class="table table-bordered table-striped" id="table-penalty" width="100%">
											<thead>
												<tr>
													<th style="text-align:center" width="10">#</th>
													<th width="200">{{ __('general.date') }}</th>
													<th width="150">{{ __('employee.leavetp') }}</th>
													<th width="150">{{ __('employee.bscslr') }}</th>
													<th width="150">{{ __('employee.bscslr') }}/{{ __('general.day') }}</th>
													<th width="150">{{ __('general.act') }}</th>
												</tr>
											</thead>
											<tfoot>
												<tr>
													<td colspan="4" class="text-right"><b>Grand Total</b></td>
													<td class="text-left" colspan="1">
														<span id="total" class="text-right" placeholder="Total"></span>
													</td>
													<td></td>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				{{-- .Tab Salary --}}
				{{-- Tab Leave --}}
				<div class="tab-pane" id="leave">
					<div class="card-header">
						<h3 class="card-title">{{ __('leavereport.leaverpt') }}</h3>
					</div>
					<div class="card-body">
						<div class="form-group row">
							<label class="control-label col-md-1" for="year_leave">{{ __('general.year') }}</label>
							<select name="year_leave" class="form-control col-md-2 select2" id="year_leave" multiple>
								@php
								$thn_skr = date('Y');
								@endphp
								@for ($i = $thn_skr; $i >= 1991; $i--)
								<option value="{{ $i }}">{{ $i }}</option>
								@endfor
							</select>
						</div>
						<hr>
						<table class="table table-bordered table-striped" style="width:100%" id="table-leave">
							<thead>
								<tr>
									<th style="text-align:center" width="10">No</th>
									<th width="150">{{ __('leavesetting.leavename') }}</th>
									<th width="150">{{ __('leavesetting.balance') }}</th>
									<th width="100">{{ __('employee.used_blc') }}</th>
									<th width="150">{{ __('employee.remain_blc') }}</th>
									<th width="150">{{ __('employee.ovr_blc') }}</th>
									<th width="150">{{ __('employee.prd_blc') }}</th>
									<th width="150">#</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
				{{-- .Tab Leave --}}
				{{-- Tab Attendance --}}
				<div class="tab-pane" id="attendance">
					<form id="form_approval" action="{{ route('attendanceapproval.approve') }}" class="form-horizontal" method="post" autocomplete="off">
						<div class="card-header">
							<h3 class="card-title">{{ __('employee.attend') }}</h3>
						</div>
						<div class="card-body">
							<div class="form-row col-md-3">
								<label class="control-label pull-right col-md-2" for="period">{{ __('employee.period') }}</label>
								<div class="col-sm-6">
									<select class="form-control select2" name="month_attendance" id="month_attendance">
										<option value="01" @if (date('m', time())=="01" ) selected @endif>January</option>
										<option value="02" @if (date('m', time())=="02" ) selected @endif>February</option>
										<option value="03" @if (date('m', time())=="03" ) selected @endif>March</option>
										<option value="04" @if (date('m', time())=="04" ) selected @endif>April</option>
										<option value="05" @if (date('m', time())=="05" ) selected @endif>May</option>
										<option value="06" @if (date('m', time())=="06" ) selected @endif>June</option>
										<option value="07" @if (date('m', time())=="07" ) selected @endif>July</option>
										<option value="08" @if (date('m', time())=="08" ) selected @endif>August</option>
										<option value="09" @if (date('m', time())=="09" ) selected @endif>September</option>
										<option value="10" @if (date('m', time())=="10" ) selected @endif>October</option>
										<option value="11" @if (date('m', time())=="11" ) selected @endif>November</option>
										<option value="12" @if (date('m', time())=="12" ) selected @endif>December</option>
									</select>
								</div>
								<div class="col-sm-4">
									<div class="input-group">
										<select name="year_attendance" class="form-control select2" id="year_attendance">
											@php
											$thn_skr = date('Y');
											@endphp
											@for ($i = $thn_skr; $i >= 1991; $i--)
											<option value="{{ $i }}">{{ $i }}</option>
											@endfor
										</select>
									</div>
								</div>
							</div>
							<table class="table table-striped table-bordered datatable" style="width: 100%">
								<thead>
									<tr>
										<th width="10">No</th>
										<th width="50">{{ __('general.date') }}</th>
										<th width="50">{{ __('department.dep') }}<br>{{ __('position.pos') }}</th>
										<th width="50">{{ __('workgroup.workgrp') }}</th>
										<th width="50">{{ __('employee.workshift') }}</th>
										<th width="10">{{ __('employee.check_in') }}</th>
										<th width="10">{{ __('employee.check_out') }}</th>
										<th width="10">{{ __('employee.summary') }}</th>
										<th width="50">Status</th>
									</tr>
								</thead>
							</table>
						</div>
						<div class="overlay">
							<i class="fa fa-refresh fa-spin"></i>
						</div>
					</form>
				</div>
				{{-- .Tab Attendance --}}
				
			</div>
		</div>
	</div>
</div>
<!-- Edit Overtime Hour -->
<div class="modal fade" id="editovertime" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">{{ __('general.edt') }} {{ __('employee.ot') }}</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<form id="form_overtime" class="form-horizontal" autocomplete="off">
					<div class="row">
						{{ csrf_field() }}
						<input type="hidden" name="_method" />
						<input type="hidden" name="employee_id">
						<div class="col-md-12">
							<div class="form-group">
								<label for="amount" class="control-label">{{ __('employee.hour') }} <b class="text-danger">*</b></label>
								<input type="text" class="form-control" id="hour" name="hour" placeholder="{{ __('employee.hour') }}" required>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button form="form_overtime" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
			</div>
			<div class="overlay d-none">
				<i class="fa fa-2x fa-sync-alt fa-spin"></i>
			</div>
		</div>
	</div>
</div>
<!-- End Edit Overtime -->

{{-- Modal Salary --}}
<div class="modal fade" id="add_salary" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">{{ __('general.add') }} Salary</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<form id="form_salary" class="form-horizontal" autocomplete="off">
					<div class="row">
						{{ csrf_field() }}
						<input type="hidden" name="userser" value="{{ Auth::guard('admin')->user()->name }}">
						<input type="hidden" name="_method" />
						<input type="hidden" name="employee_id">
						<div class="col-md-12">
							<div class="form-group">
								<label for="amount" class="control-label">{{ __('employee.amount') }} <b class="text-danger">*</b></label>
								<input type="text" class="form-control" id="amount" name="amount" placeholder="{{ __('employee.amount') }}" required>
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="description" class="control-label">{{ __('general.desc') }}</label>
								<input type="text" class="form-control" id="description" name="description" placeholder="{{ __('general.desc') }}">
							</div>
						</div>
						<div class="form-group col-md-12">
							<label for="user_id" class="control-label">{{ __('employee.updateby') }} <b class="text-danger">*</b></label>
							<input type="text" class="form-control" id="user_id" name="user_id" readonly placeholder="{{ __('employee.updateby') }}" required>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button form="form_salary" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
			</div>
			<div class="overlay d-none">
				<i class="fa fa-2x fa-sync-alt fa-spin"></i>
			</div>
		</div>
	</div>
</div>

{{-- Modal Allowance --}}
<div class="modal fade" id="add_allowance" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">{{ __('general.add') }} {{ __('allowance.alw') }}</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<form id="form_allowance" class="form-horizontal" autocomplete="off">
					@csrf
					@method('PUT')
					<div class="row">
						<input type="hidden" name="employee_id">
						<div class="col-md-6">
							<div class="form-group">
								<label for="type" class="control-label">{{ __('general.type') }}</label>
								<select class="form-control select2" data-placeholder="{{ __('general.type') }}" name="type" id="type">
									<option value=""></option>
									<option value="percentage">Percentage</option>
									<option value="nominal">Nominal</option>
									<option value="automatic">Automatic</option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="value" class="control-label">{{ __('general.value') }}</label>
								<input type="text" class="form-control" id="value" name="value" placeholder="{{ __('general.value') }}" required>
								<input type="hidden" class="form-control" id="get_allowance_id" name="get_allowance_id">
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="factor" class="control-label">{{ __('employee.factor') }}</label>
								<input type="text" class="form-control" id="factor" name="factor" placeholder="{{ __('employee.factor') }}">
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button form="form_allowance" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
			</div>
		</div>
		<div class="overlay d-none">
			<i class="fa fa-2x fa-sync-alt fa-spin"></i>
		</div>
	</div>
</div>

{{-- Modal View Allowance --}}
<div class="modal fade" id="view_allowance" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" style="width:75%;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">View {{ __('allowance.alw') }}</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<input type="hidden" name="employee_id" value="{{$employee->name}}">
					<input type="hidden" name="allowance" value="" id="allowance-id-history">
					<div class="card-body">
						<table class="table table-bordered table-striped" id="table-history" style="width:100%;">
							<thead>
								<tr>
									<th style="text-align:center" width="10">#</th>
									<th width="100">{{ __('general.date') }}</th>
									<th width="250">{{ __('general.category') }}</th>
									<th width="100">Nilai</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="overlay d-none">
			<i class="fa fa-2x fa-sync-alt fa-spin"></i>
		</div>
	</div>
</div>

{{-- Modal View Detail Driver Allowance --}}
<div class="modal fade" id="view-driver-allowance" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="view-driver-allowance-title">View {{ __('employee.driveralw') }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<table class="table table-bordered table-striped w-100" id="table-view-driver-allowance">
					<thead>
						<tr>
							<th width="5" class="text-right">#</th>
							<th width="10">{{ __('employee.dep_time') }}</th>
							<th width="10">{{ __('employee.arr_time') }}</th>
							<th width="10">{{ __('employee.pol_no') }}</th>
							<th width="10">{{ __('customer.cust') }}</th>
						</tr>
					</thead>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

{{-- Modal View Detail Leave --}}
<div class="modal fade" id="view-detail-leave" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="view-detail-leave-title">View Detail Leave</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<input type="hidden" name="leave_employee_id" value="{{$employee->id}}">
				<input type="hidden" name="leavesetting_id" value="" id="leavesetting_id">
				<table class="table table-bordered table-striped w-100" id="table-view-detail-leave">
					<thead>
						<tr>
							<th width="5" class="text-right">No</th>
							<th width="50">{{ __('general.date') }}</th>
							<th width="50">{{ __('general.type') }}</th>
							<th width="50">Status</th>
						</tr>
					</thead>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

{{-- Modal Edit Leave Balance --}}
<div class="modal fade" id="edit-leave-balance" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="edit-leave-balance-title">{{ __('general.edt') }} Balance Leave</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="form-edit-balance" action="{{ route('leave.quickupdate') }}" class="form-horizontal no-gutters" method="post" autocomplete="off">
					{{ csrf_field() }}
					<input type="hidden" name="balance_leave_employee_id" value="{{$employee->id}}">
					<input type="hidden" name="balance_leavesetting_id" value="" id="balance_leavesetting_id">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label" for="balance">{{ __('leavesetting.balance') }}</label>
								<input type="number" class="form-control" name="leave_balance" id="leave_balance" placeholder="{{ __('leavesetting.balance') }}" value="">
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button form="form-edit-balance" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="Apply"><i class="fa fa-save"></i></button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

{{-- Modal Allowance Add --}}
<div class="modal fade" id="add_allowance_data" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">{{ __('general.add') }} {{ __('allowance.alw') }}</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="form_allowance_data" class="form-horizontal" autocomplete="off">
					<div class="row">
						<input type="hidden" name="employee_id">
						<div class="col-md-12">
							<div class="form-group">
								<label for="allowance" class="control-label">{{ __('allowance.alw') }}</label>
								<input type="text" class="form-control" id="allowance_id" name="allowance_id" placeholder="{{ __('allowance.alw') }}" required>
							</div>
							<div class="form-group">
								<input type="hidden" class="form-control" name="month">
								<input type="hidden" class="form-control" name="year">
							</div>
						</div>
					</div>
					{{ csrf_field() }}
					<input type="hidden" name="_method" />
				</form>
			</div>
			<div class="modal-footer">
				<button form="form_allowance_data" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
			</div>
		</div>
		<div class="overlay d-none">
			<i class="fa fa-2x fa-sync-alt fa-spin"></i>
		</div>
	</div>
</div>

{{-- Modal Training --}}
<div class="modal fade" id="add_training" tabindex="-1" role="dialog" aria-hidden="true" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="overlay-wrapper">
				<div class="modal-header">
					<h4 class="modal-title">{{ __('general.add') }} {{ __('employee.train') }}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="form_training" class="form-horizontal" method="post" autocomplete="off">
						<input type="hidden" name="employee_id">
						<div class="form-group col-sm-12">
							<label for="code" class="control-label">No.{{ __('employee.document') }}<b class="text-danger">*</b></label>
							<input type="text" class="form-control" id="code" name="code" placeholder="No.{{ __('employee.document') }}" required>
						</div>
						<div class="form-group col-sm-12">
							<label for="issued" class="control-label">{{ __('employee.issuedby') }}<b class="text-danger">*</b></label>
							<input type="text" class="form-control" id="issued" name="issued" placeholder="{{ __('employee.issuedby') }}" required>
						</div>
						<div class="d-flex">
							<div class="form-group col-sm-6">
								<label for="start_date" class="control-label">{{ __('general.start_date') }}<b class="text-danger">*</b></label>
								<input type="text" class="form-control start_date" id="start_date" name="start_date" placeholder="{{ __('general.start_date') }}" required>
							</div>
							<div class="form-group col-sm-6">
								<label for="date" class="control-label">{{ __('general.finish_date') }}<b class="text-danger">*</b></label>
								<input type="text" class="form-control end_date" id="end_date" name="end_date" placeholder="{{ __('general.finish_date') }}" required>
							</div>
						</div>
						<div class="form-group col-sm-12">
							<label for="description" class="control-label">{{ __('general.desc') }}</label>
							<textarea name="description" id="description" class="form-control" placeholder="{{ __('general.desc') }}"></textarea>
						</div>
						{{ csrf_field() }}
						<input type="hidden" name="_method" />
					</form>
				</div>
				<div class="modal-footer">
					<button form="form_training" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
				</div>
				<div class="overlay d-none">
					<i class="fa fa-2x fa-sync-alt fa-spin"></i>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- Modal Add Document --}}
<div class="modal fade" id="add_document" tabindex="-1" role="dialog" aria-hidden="true" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="overlay-wrapper">
				<div class="modal-header">
					<h4 class="modal-title">{{ __('general.add') }} {{ __('employee.document') }}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="form_document" class="form-horizontal" method="post" autocomplete="off">
						<div class="d-flex">
							<input type="hidden" name="employee_id">
							<div class="form-group col-sm-6">
								<label for="category" class="control-label">{{ __('general.category') }} <b class="text-danger">*</b></label>
								<select id="category" name="category" class="form-control select2" data-placeholder="Choose {{ __('general.category') }}" required>
									<option value=""></option>
									@foreach(config('enums.document_category') as $key => $document_category)
									<option value="{{$key}}">{{$document_category}}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group col-sm-6">
								<label for="name" class="control-label">{{ __('general.name') }}<b class="text-danger">*</b></label>
								<input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
							</div>
						</div>

						<div class="form-group col-sm-12">
							<label for="file" class="control-label">File</label>
							<input type="file" value="file.jpg" class="form-control" name="file" id="file" accept="image/*" />
							<a id="document-preview" onclick="showDocument(this)" href="#" data-url="" class="mt-2"></a>
						</div>
						<div class="form-group col-sm-12">
							<label for="description" class="control-label">{{ __('general.desc') }}</label>
							<textarea name="description" id="description" class="form-control" placeholder="{{ __('general.desc') }}"></textarea>
						</div>
						{{ csrf_field() }}
						<input type="hidden" name="_method" />
					</form>
				</div>
				<div class="modal-footer">
					<button form="form_document" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
				</div>
				<div class="overlay d-none">
					<i class="fa fa-2x fa-sync-alt fa-spin"></i>
				</div>
			</div>
		</div>
	</div>
</div>
{{-- Modal Document Preview --}}
<div class="modal fade" id="show-document" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<embed id="url-document" src="" style="height:500px;width:500px">
		</div>
	</div>
</div>
{{-- Modal Career --}}
<div class="modal fade" id="add_career" tabindex="-1" role="dialog" aria-hidden="true" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="overlay-wrapper">
				<div class="modal-header">
					<h4 class="modal-title">{{ __('general.add') }} {{ __('employee.career') }}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="form_career" class="form-horizontal" method="post" autocomplete="off">
						<input type="hidden" name="employee_id">
						<div class="d-flex">
							<div class="form-group col-sm-6">
								<label for="position" class="control-label">{{ __('position.pos') }}<b class="text-danger">*</b></label>
								<input type="text" class="form-control" id="position" name="position" placeholder="{{ __('position.pos') }}" required>
							</div>
							<div class="form-group col-sm-6">
								<label for="grade" class="control-label">{{ __('employee.grade') }}<b class="text-danger">*</b></label>
								<input type="text" class="form-control" id="grade" name="grade" placeholder="{{ __('employee.grade') }}" value="1" required>
							</div>
						</div>
						<div class="d-flex">
							<div class="form-group col-sm-6">
								<label for="department" class="control-label">{{ __('department.dep') }}<b class="text-danger">*</b></label>
								<input type="text" class="form-control" id="department" name="department" placeholder="{{ __('department.dep') }}" required>
							</div>
							<div class="form-group col-sm-6">
								<label for="reference" class="control-label">{{ __('employee.ref') }}<b class="text-danger">*</b></label>
								<input type="text" class="form-control" id="reference" name="reference" placeholder="{{ __('employee.ref') }}" required>
							</div>
						</div>
						<div class="d-flex">
							<div class="form-group col-sm-6">
								<label for="start_date" class="control-label">{{ __('general.start_date') }}<b class="text-danger">*</b></label>
								<input type="text" class="form-control start_date" id="start_date" name="start_date" placeholder="{{ __('general.start_date') }}" required>
							</div>
							<div class="form-group col-sm-6">
								<label for="date" class="control-label">{{ __('general.finish_date') }}<b class="text-danger">*</b></label>
								<input type="text" class="form-control end_date" id="end_date" name="end_date" placeholder="{{ __('general.finish_date') }}" required>
							</div>
						</div>
						{{ csrf_field() }}
						<input type="hidden" name="_method" />
					</form>
				</div>
				<div class="modal-footer">
					<button form="form_career" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
				</div>
				<div class="overlay d-none">
					<i class="fa fa-2x fa-sync-alt fa-spin"></i>
				</div>
			</div>
		</div>
	</div>
</div>
{{-- Modal Experience --}}
<div class="modal fade" id="add_experience" tabindex="-1" role="dialog" aria-hidden="true" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="overlay-wrapper">
				<div class="modal-header">
					<h4 class="modal-title">{{ __('general.add') }} {{ __('employee.exper') }}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="form_experience" class="form-horizontal" method="post" autocomplete="off">
						<input type="hidden" name="employee_id">
						<div class="d-flex">
							<div class="form-group col-sm-6">
								<label for="last_position" class="control-label">{{ __('employee.last_pos') }}<b class="text-danger">*</b></label>
								<input type="text" class="form-control" id="last_position" name="last_position" placeholder="{{ __('employee.last_pos') }}" required>
							</div>
							<div class="form-group col-sm-6">
								<label for="company" class="control-label">{{ __('employee.company') }}<b class="text-danger">*</b></label>
								<input type="text" class="form-control" id="company" name="company" placeholder="{{ __('employee.company') }}" required>
							</div>
						</div>
						<div class="d-flex">
							<div class="form-group col-sm-6">
								<label for="start_date" class="control-label">{{ __('general.start_date') }}<b class="text-danger">*</b></label>
								<input type="text" class="form-control start_date" id="start_date" name="start_date" placeholder="{{ __('general.start_date') }}" required>
							</div>
							<div class="form-group col-sm-6">
								<label for="date" class="control-label">{{ __('general.finish_date') }}<b class="text-danger">*</b></label>
								<input type="text" class="form-control end_date" id="end_date" name="end_date" placeholder="{{ __('general.finish_date') }}" required>
							</div>
						</div>
						<div class="form-group col-sm-12">
							<label for="duration" class="control-label">{{ __('employee.duration') }}<b class="text-danger">*</b></label>
							<input type="text" class="form-control" id="duration" name="duration" placeholder="{{ __('employee.duration') }}" required>
						</div>

						{{ csrf_field() }}
						<input type="hidden" name="_method" />
					</form>
				</div>
				<div class="modal-footer">
					<button form="form_experience" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
				</div>
				<div class="overlay d-none">
					<i class="fa fa-2x fa-sync-alt fa-spin"></i>
				</div>
			</div>
		</div>
	</div>
</div>
{{-- Modal Education --}}
<div class="modal fade" id="add_education" tabindex="-1" role="dialog" aria-hidden="true" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="overlay-wrapper">
				<div class="modal-header">
					<h4 class="modal-title">{{ __('general.add') }} {{ __('employee.edu') }}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="form_education" class="form-horizontal" method="post" autocomplete="off">
						<input type="hidden" name="employee_id">
						<div class="form-group col-sm-12">
							<label for="institution" class="control-label">{{ __('employee.institution') }}<b class="text-danger">*</b></label>
							<input type="text" class="form-control" id="institution" name="institution" placeholder="{{ __('employee.institution') }}" required>
						</div>
						<div class="d-flex">
							<div class="form-group col-sm-6">
								<label for="stage" class="control-label">{{ __('employee.stage') }}<b class="text-danger">*</b></label>
								<input type="text" class="form-control stage" id="stage" name="stage" placeholder="{{ __('employee.stage') }}" required>
							</div>
							<div class="form-group col-sm-6">
								<label for="period" class="control-label">{{ __('employee.period') }}<b class="text-danger">*</b></label>
								<input type="text" class="form-control period" id="period" name="period" placeholder="{{ __('employee.period') }}" required>
							</div>
						</div>
						{{ csrf_field() }}
						<input type="hidden" name="_method" />
					</form>
				</div>
				<div class="modal-footer">
					<button form="form_education" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
				</div>
				<div class="overlay d-none">
					<i class="fa fa-2x fa-sync-alt fa-spin"></i>
				</div>
			</div>
		</div>
	</div>
</div>
{{-- Modal Contract --}}
<div class="modal fade" id="add_contract" tabindex="-1" role="dialog" aria-hidden="true" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="overlay-wrapper">
				<div class="modal-header">
					<h4 class="modal-title">{{ __('general.add') }} {{ __('employee.contract') }}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="form_contract" class="form-horizontal" method="post" autocomplete="off">
						<input type="hidden" name="employee_id">
						<div class="form-group col-sm-12">
							<label for="code" class="control-label">No.{{ __('employee.document') }}<b class="text-danger">*</b></label>
							<input type="text" class="form-control" id="code" name="code" placeholder="No.{{ __('employee.document') }}">
						</div>
						<div class="d-flex">
							<div class="form-group col-sm-6">
								<label for="start_date" class="control-label">{{ __('general.start_date') }}<b class="text-danger">*</b></label>
								<input type="text" class="form-control start_date" id="start_date" name="start_date" placeholder="{{ __('general.start_date') }}">
							</div>
							<div class="form-group col-sm-6">
								<label for="date" class="control-label">{{ __('general.finish_date') }}<b class="text-danger">*</b></label>
								<input type="text" class="form-control end_date" id="end_date" name="end_date" placeholder="{{ __('general.finish_date') }}">
							</div>
						</div>
						<div class="form-group col-sm-12">
							<label for="description" class="control-label">{{ __('general.desc') }}</label>
							<textarea name="description" id="description" class="form-control" placeholder="{{ __('general.desc') }}"></textarea>
						</div>
						<div class="form-group col-sm-12">
							<label for="file" class="control-label">File</label>
							<input type="file" value="file.jpg" class="form-control" name="file" id="filecontract" accept="image/*" />
							<a id="contract-preview" onclick="showContract(this)" href="#" data-url="" class="mt-2"></a>
						</div>
						<div class="form-group col-sm-12">
							<label for="status" class="control-label">Status <b class="text-danger">*</b></label>
							<select id="status" name="status" class="form-control select2" data-placeholder="Choose Status">
								<option value=""></option>
								@foreach(config('enums.status_contract') as $key => $status_contract)
								<option value="{{$key}}">{{$status_contract}}</option>
								@endforeach
							</select>
						</div>
						{{ csrf_field() }}
						<input type="hidden" name="_method" />
					</form>
				</div>
				<div class="modal-footer">
					<button form="form_contract" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
				</div>
				<div class="overlay d-none">
					<i class="fa fa-2x fa-sync-alt fa-spin"></i>
				</div>
			</div>
		</div>
	</div>
</div>
{{-- Modal Contract Preview --}}
<div class="modal fade" id="show-contract" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<embed id="url-contract" src="" style="height:500px;width:500px">
		</div>
	</div>
</div>

{{-- Modal Attendance --}}
<div class="modal fade" id="edit-shift" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Change {{ __('employee.workshift') }}</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
			<div class="modal-body">
				<form id="form-shift" action="{{ route('attendanceapproval.quickupdate') }}" class="form-horizontal no-gutters" method="post" autocomplete="off">
					{{ csrf_field() }}
					<input type="hidden" name="attendance_id" id="attendance_id">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label" for="working_shift">{{ __('employee.workshift') }}</label>
								<input type="text" class="form-control" name="working_shift" id="working_shift" placeholder="{{ __('employee.workshift') }}">
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button form="form-shift" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="Apply"><i class="fa fa-save"></i></button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="edit-in" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Change {{ __('employee.first_in') }}</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
			<div class="modal-body">
				<form id="form-in" action="{{ route('attendanceapproval.quickupdate') }}" class="form-horizontal no-gutters" method="post" autocomplete="off">
					{{ csrf_field() }}
					<input type="hidden" name="first_in_id" id="first_in_id">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label" for="first_in">{{ __('employee.first_in') }}</label>
								<input type="text" class="form-control timepicker" name="first_in" id="first_in" placeholder="{{ __('employee.first_in') }}">
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button form="form-in" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="Apply"><i class="fa fa-save"></i></button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="edit-out" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Change {{ __('employee.last_out') }}</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
			<div class="modal-body">
				<form id="form-out" action="{{ route('attendanceapproval.quickupdate') }}" class="form-horizontal no-gutters" method="post" autocomplete="off">
					{{ csrf_field() }}
					<input type="hidden" name="first_out_id" id="first_out_id">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label" for="last_out">{{ __('employee.last_out') }}</label>
								<input type="text" class="form-control timepicker" name="last_out" id="last_out" placeholder="{{ __('employee.last_out') }}">
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button form="form-out" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="Apply"><i class="fa fa-save"></i></button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="edit-worktime" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Adjust {{ __('employee.workingtime') }} & {{ __('employee.ot') }}</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
			<div class="modal-body">
				<form id="form-worktime" action="{{ route('attendanceapproval.quickupdate') }}" class="form-horizontal no-gutters" method="post" autocomplete="off">
					{{ csrf_field() }}
					<input type="hidden" name="workingtime_id" id="workingtime_id">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label" for="working_time">{{ __('employee.workingtime') }}</label>
								<input type="number" class="form-control" name="working_time" id="working_time" placeholder="{{ __('employee.workingtime') }}" value="0">
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label" for="over_time">{{ __('employee.ot') }}</label>
								<input type="number" class="form-control" name="over_time" id="over_time" placeholder="{{ __('employee.ot') }}" value="0">
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button form="form-worktime" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="Apply"><i class="fa fa-save"></i></button>
			</div>
		</div>
	</div>
</div>
{{-- End Modal Attendance --}}

{{-- Modal BPJS --}}
<div class="modal fade" id="add_bpjs" tabindex="-1" role="dialog" aria-hidden="true" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="overlay-wrapper">
				<div class="modal-header">
					<h4 class="modal-title">{{ __('general.add') }} BPJS</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="form_bpjs" class="form-horizontal" method="post" autocomplete="off">
						<input type="hidden" name="employee_id">
						<div class="form-group col-sm-12">
							<label for="name" class="control-label">{{ __('general.name') }}<b class="text-danger">*</b></label>
							<input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
						</div>
						<div class="d-flex">
							<div class="form-group col-sm-6">
								<label for="nik" class="control-label">NIK<b class="text-danger">*</b></label>
								<input type="text" class="form-control nik" id="nik" name="nik" placeholder="NIK" required>
							</div>
							<div class="form-group col-sm-6">
								<label for="date" class="control-label">{{ __('employee.relation') }}<b class="text-danger">*</b></label>
								<input type="text" class="form-control" id="relation" name="relation" placeholder="{{ __('employee.relation') }}" required>
							</div>
						</div>
						<div class="form-group col-sm-12">
							<label for="address" class="control-label">{{ __('employee.address') }}</label>
							<textarea name="address" id="address" class="form-control" placeholder="{{ __('employee.address') }}"></textarea>
						</div>

						{{ csrf_field() }}
						<input type="hidden" name="_method" />
					</form>
				</div>
				<div class="modal-footer">
					<button form="form_bpjs" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
				</div>
				<div class="overlay d-none">
					<i class="fa fa-2x fa-sync-alt fa-spin"></i>
				</div>
			</div>
		</div>
	</div>
</div>
{{-- Modal Member --}}
<div class="modal fade" id="add_member" tabindex="-1" role="dialog" aria-hidden="true" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="overlay-wrapper">
				<div class="modal-header">
					<h4 class="modal-title">{{ __('employee.add_member') }}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="form_member" class="form-horizontal" method="post" autocomplete="off">
						<input type="hidden" name="employee_id">
						<div class="form-group col-sm-12">
							<label for="name" class="control-label">{{ __('general.name') }}<b class="text-danger">*</b></label>
							<input type="text" class="form-control" id="name" name="name" placeholder="{{ __('general.name') }}" required>
						</div>
						<div class="d-flex">
							<div class="form-group col-sm-6">
								<label for="nik" class="control-label">NIK<b class="text-danger">*</b></label>
								<input type="text" class="form-control nik" id="nik" name="nik" placeholder="NIK" required>
							</div>
							<div class="form-group col-sm-6">
								<label for="date" class="control-label">{{ __('employee.relation') }}<b class="text-danger">*</b></label>
								<input type="text" class="form-control" id="relation" name="relation" placeholder="{{ __('employee.relation') }}" required>
							</div>
						</div>
						<div class="form-group col-sm-12">
							<label for="address" class="control-label">{{ __('employee.address') }}</label>
							<textarea name="address" id="address" class="form-control" placeholder="{{ __('employee.address') }}"></textarea>
						</div>

						{{ csrf_field() }}
						<input type="hidden" name="_method" />
					</form>
				</div>
				<div class="modal-footer">
					<button form="form_member" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
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
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script type="text/javascript">
	function showDocument(e){
	$('#url-document').attr("src",$(e).data('url'));
	$('#show-document').modal('show');
	$('#timeout').select2();
}
function showContract(e){
	$('#url-contract').attr("src",$(e).data('url'));
	$('#show-contract').modal('show');
	$('#timeout').select2();
}
// Custom function Section
function generate(){
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
		}
	});
	$.ajax({
		url: "{{ route('employeeallowance.generate')}}",
		type : "POST",
		data : {
			montly: $('select[name=montly]').val(),
			year : $('select[name=year]').val(),
			employee_id : $('input[name=employee_id]').val(),
		},
		success: function (response){
			alert(response.message);
			dataTableAllowance.draw()
		},
		error: function(response){
			var response = response.responsJSON;
			alert(response.message);
			return;
		}
	});
}
$(document).on('change', '#montly_overtime', function() {
	dataTableOvertime.draw();
});
$(document).on('change', '#year_overtime', function() {
	dataTableOvertime.draw();
});
$(document).on('change', '#montly_penalty', function() {
	dataTablePenalty.draw();
});
$(document).on('change', '#year_penalty', function() {
	dataTablePenalty.draw();
});
$(document).on('change', '#montly', function() {
	dataTableAllowance.draw();
});
$(document).on('change', '#year', function() {
	dataTableAllowance.draw();
});
$(document).on('change', '#month-driver', function() {
	dataTableDriver.draw();
});
$(document).on('change', '#year-driver', function() {
	dataTableDriver.draw();
});
$(document).on('change', '#month-gross', function() {
	dataTableGross.draw();
});
$(document).on('change', '#year-gross', function() {
	dataTableGross.draw();
});

$('#form-search').submit(function(e){
	e.preventDefault();
	dataTableOvertime.draw();
});

function generate_overtime() {
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
		}
	});
	$.ajax({
		url: "{{ route('overtime.read_overtime') }}",
		method : "GET",
		dataType: 'json',
		data : {
			montly : $('#montly_overtime').val(),
			year : $('#year_overtime').val(),
			employee_id : $('#employee_id_overtime').val(),
		},
		success: function (response){
			dataTableOvertime.draw()
		},
		error: function(response){
			var response = response.responsJSON;
			alert(response.message);
			return;
		}
	});
}
function formatTime(date) {
	var now = new Date(), year = now.getFullYear();
	var d = new Date(year + ' ' + date),
					minute = '' + d.getMinutes(),
					hour = '' + d.getHours();
	
	if (minute.length < 2)
		minute = '0' + minute;
	if (hour.length < 2)
		hour = '0' + hour;

	return [hour, minute].join(':');
}

function dayName(date) {
	var weekday = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
	var date = new Date(date);

	return weekday[date.getDay()];
}
// End of Custom Function
// View Data Attendance

dataTable = $('.datatable').DataTable({
	stateSave:true,
	processing: true,
	serverSide: true,
	filter:false,
	info:false,
	lengthChange:false,
	paging:false,
	responsive: true,
	order: [[ 1, "asc" ]],
	ajax: {
		url: "{{route('employees.readattendance')}}",
		type: "GET",
		data:function(data){
			data.employee_id = {{$employee->id}};
			data.month = $('select[name=month_attendance]').val();
			data.year = $('select[name=year_attendance]').val();
		}
	},
	columnDefs:[
		{
			orderable: false,targets:[0,2,3,4,5,6,7,8]
		},
		{ className: "text-center", targets: [0,1,2,3,4,5,6,7,8] },
		{ render: function ( data, type, row ) {
			var date = new Date(row.attendance_date);
			return `${row.attendance_date} <br> <span class="text-bold ${row.day == 'Off' ? 'text-red' : ''}">${dayName(row.attendance_date)}</span>`;
		},targets: [1]
		},
		{ render: function ( data, type, row ) {
			return `<span>${row.department_name} <br> ${row.title_name}</span>`;
		},targets: [2]
		},
		{ render: function ( data, type, row ) {
			return `<span class="text-blue">${row.description ? row.description : '-'}</span>`;
		},targets: [4]
		},
		{ render: function ( data, type, row ) {
			if (row.attendance_in) {
				if (row.attendance_in < row.start_time) {
					return `<span class="text-blue">${row.attendance_in}</span><br><span class="text-bold">${formatTime(row.start_time)}</span><br><span class="text-success text-bold">- ${row.diff_in}</span>`
				} else {
					return `<span class="text-blue">${row.attendance_in}</span><br><span class="text-bold">${formatTime(row.start_time)}</span><br><span class="text-danger text-bold">+ ${row.diff_in}</span>`
				}
			} else {
				return '<span class="text-red text-bold">?</span>';
			}
		},targets: [5]
		},
		{ render: function ( data, type, row ) {
			if (row.attendance_out) {
				if (row.attendance_out > row.finish_time) {
					return `<span class="text-blue">${row.attendance_out}</span><br><span class="text-bold">${formatTime(row.finish_time)}</span><br><span class="text-danger text-bold">+ ${row.diff_out}</span>`
				} else {
					return `<span class="text-blue">${row.attendance_out}</span><br><span class="text-bold">${formatTime(row.finish_time)}</span><br><span class="text-success text-bold">- ${row.diff_out}</span>`
				}
			} else {
				return '<span class="text-red text-bold">?</span>';
			}
		},targets: [6]
		},
		{ render: function ( data, type, row ) {
				return `WT: ${row.adj_working_time} Hours<br>OT: ${row.adj_over_time} Hours`
		},targets: [7]
		},
		{ render: function ( data, type, row ) {
			if (row.status == 0) {
				return '<span class="badge badge-warning">Waiting Approval</span>'
			} else {
				return '<span class="badge badge-success">Already Approval</span>'
			}
		},targets: [8]
		},
	],
	columns: [
		{ data: "no", className: "align-middle text-center" },
		{ data: "attendance_date", className: "align-middle text-center" },
		{ data: "department_name", className: "align-middle text-center" },
		{ data: "workgroup_name", className: "align-middle text-center" },
		{ data: "description", className: "shift align-middle text-center" },
		{ data: "attendance_in", className: "time_in align-middle text-center" },
		{ data: "attendance_out", className: "time_out align-middle text-center"},
		{ data: "adj_working_time", className: "worktime align-middle text-center" },
		{ data: "status", className: "align-middle text-center" },
	]
});
// End Delete Attendance Employee

$('#working_shift').select2({
	ajax: {
		url: "{{route('attendanceapproval.selectworkingtime')}}",
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
});
$(document).on('click', '.shift', function() {
	var data = dataTable.row(this).data();
	if (data) {
		$('#edit-shift').modal('show');
		$('#form-shift input[name=attendance_id]').attr('value', data.id);
		$("#working_shift").select2('data',{id:data.workingtime_id,text:data.description}).trigger('change');
		$(document).on("change", "#working_shift", function () {
			if (!$.isEmptyObject($('#form-shift').validate().submitted)) {
				$('#form-shift').validate().form();
			}
		});
	}
});
$(document.body).on('click', '.time_in', function() {
	var data = dataTable.row(this).data();
	if (data) {  
		if (!data.attendance_in) {
			$('#edit-in').modal('show');
			$('#form-in input[name=first_in_id]').attr('value', data.id);
			$('#form-in input[name=first_in]').daterangepicker({
				startDate: moment(data.attendance_date),
				singleDatePicker: true,
				timePicker: true,
				timePicker24Hour: true,
				timePickerSeconds: true,
				timePickerIncrement: 1,
				locale: {
					format: 'MM/DD/YYYY HH:mm:ss'
				}
			});
		} else {
			$('#edit-in').modal('show');
			$('#form-in input[name=first_in_id]').attr('value', data.id);
			$('#form-in input[name=first_in]').daterangepicker({
				startDate: moment(data.time_in),
				singleDatePicker: true,
				timePicker: true,
				timePicker24Hour: true,
				timePickerSeconds: true,
				timePickerIncrement: 1,
				locale: {
					format: 'MM/DD/YYYY HH:mm:ss'
				}
			});
		}
	}
});
$(document.body).on('click', '.time_out', function() {
	var data = dataTable.row(this).data();
	if (data) {  
		if (!data.attendance_out) {
			$('#edit-out').modal('show');
			$('#form-out input[name=first_out_id]').attr('value', data.id);
			$('#form-out input[name=last_out]').daterangepicker({
				startDate: moment(data.attendance_date),
				singleDatePicker: true,
				timePicker: true,
				timePicker24Hour: true,
				timePickerSeconds: true,
				timePickerIncrement: 1,
				locale: {
					format: 'MM/DD/YYYY HH:mm:ss'
				}
			});
		} else {
			$('#edit-out').modal('show');
			$('#form-out input[name=first_out_id]').attr('value', data.id);
			$('#form-out input[name=last_out]').daterangepicker({
				startDate: moment(data.time_out),
				singleDatePicker: true,
				timePicker: true,
				timePicker24Hour: true,
				timePickerSeconds: true,
				timePickerIncrement: 1,
				locale: {
					format: 'MM/DD/YYYY HH:mm:ss'
				}
			});
		}
	}
});
$(document).on('click', '.worktime', function() {
	var data = dataTable.row(this).data();
	if (data) {
		$('#edit-worktime').modal('show');
		$('#form-worktime input[name=workingtime_id]').attr('value', data.id);
		$('#form-worktime input[name=working_time]').attr('value', data.adj_working_time);
		$('#form-worktime input[name=over_time]').attr('value', data.adj_over_time);
	}
});
$('input[name=birth_date]').daterangepicker({
		singleDatePicker: true,
		autoUpdateInput: false,
		timePicker: false,
		locale: {
			format: 'DD/MM/YYYY'
		}
},
		function(chosen_date) {
			$('input[name=birth_date]').val(chosen_date.format('DD/MM/YYYY'));
		});
$('input[name=birth_date]').on('change', function(){
	if (!$.isEmptyObject($(this).closest("form").validate())) {
		$(this).closest("form").validate().form();
	}
});
$('input[name=join_date]').daterangepicker({
		singleDatePicker: true,
		autoUpdateInput: false,
		timePicker: false,
		locale: {
			format: 'DD/MM/YYYY'
		}
}, 
		function(chosen_date) {
			$('input[name=join_date]').val(chosen_date.format('DD/MM/YYYY'));
		});
$('input[name=join_date]').on('change', function(){
	if (!$.isEmptyObject($(this).closest("form").validate())) {
		$(this).closest("form").validate().form();
	}
});
$('input[name=resign_date]').daterangepicker({
		singleDatePicker: true,
		autoUpdateInput: false,
		timePicker: false,
		locale: {
			format: 'DD/MM/YYYY'
		}
},
		function(chosen_date) {
			$('input[name=resign_date]').val(chosen_date.format('DD/MM/YYYY'));
		});
$('input[name=resign_date]').on('change', function(){
	if (!$.isEmptyObject($(this).closest("form").validate())) {
		$(this).closest("form").validate().form();
	}
});
$("#form").validate({
	errorElement: 'div',
	errorClass: 'invalid-feedback',
	focusInvalid: false,
	highlight: function (e) {
		$(e).closest('.form-group').removeClass('has-success').addClass(
			'was-validated has-error');
	},

	success: function (e) {
		$(e).closest('.form-group').removeClass('has-error').addClass('has-success');
		$(e).remove();
	},
	errorPlacement: function (error, element) {
		if (element.is(':file')) {
			error.insertAfter(element.parent().parent().parent());
		} else
		if (element.parent('.input-group').length) {
			error.insertAfter(element.parent());
		} else
		if (element.attr('type') == 'checkbox') {
			error.insertAfter(element.parent());
		} else {
			error.insertAfter(element);
		}
	},
	submitHandler: function () {
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
				if (result) {
					$.ajax({
						url: $('#form').attr('action'),
						method: 'post',
						data: new FormData($('#form')[0]),
						processData: false,
						contentType: false,
						dataType: 'json',
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
			}
		});
	}
});

$("#form-shift").validate({
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
			url:$('#form-shift').attr('action'),
			method:'post',
			data: new FormData($('#form-shift')[0]),
			processData: false,
			contentType: false,
			dataType: 'json',
			beforeSend:function(){
					$('.overlay').removeClass('d-none');
			}
		}).done(function(response){
					$('.overlay').addClass('d-none');
					if(response.status){
						dataTable.draw();
						$('#edit-shift').modal('hide');
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
$("#form-in").validate({
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
			url:$('#form-in').attr('action'),
			method:'post',
			data: new FormData($('#form-in')[0]),
			processData: false,
			contentType: false,
			dataType: 'json',
			beforeSend:function(){
					$('.overlay').removeClass('d-none');
			}
		}).done(function(response){
					$('.overlay').addClass('d-none');
					if(response.status){
						dataTable.draw();
						$('#edit-in').modal('hide');
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
$("#form-out").validate({
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
			url:$('#form-out').attr('action'),
			method:'post',
			data: new FormData($('#form-out')[0]),
			processData: false,
			contentType: false,
			dataType: 'json',
			beforeSend:function(){
					$('.overlay').removeClass('d-none');
			}
		}).done(function(response){
					$('.overlay').addClass('d-none');
					if(response.status){
						dataTable.draw();
						$('#edit-out').modal('hide');
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
$("#form-worktime").validate({
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
			url:$('#form-worktime').attr('action'),
			method:'post',
			data: new FormData($('#form-worktime')[0]),
			processData: false,
			contentType: false,
			dataType: 'json',
			beforeSend:function(){
					$('.overlay').removeClass('d-none');
			}
		}).done(function(response){
					$('.overlay').addClass('d-none');
					if(response.status){
						dataTable.draw();
						$('#edit-worktime').modal('hide');
						$('#form-worktime input[name=workingtime_id]').attr('value', '');
						$('#form-worktime input[name=working_time]').attr('value', '0');
						$('#form-worktime input[name=over_time]').attr('value', '0');
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
$(document).on('change', '#month_attendance', function() {
	dataTable.draw();
});
$(document).on('change', '#year_attendance', function() {
	dataTable.draw();
});

//End View Data Attendance

$(document).ready(function(){
	$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
		var currentTab = $(e.target).text();
		switch (currentTab)   {
			case '{{ __('employee.person_data') }}' :
				$('#data-personal').css("width", '100%')
				$($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
				break ;
			case 'Kontak' :
			$('#table-pic').css("width", '100%')
				$($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
				break ;
			case '{{ __('employee.leave') }}' :
			$('#table-salary').css("width", '100%')
				$($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
				break ;
			case 'Leave' :
			$('#table-leave').css("width", '100%')
				$($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
				break ;
			case 'Legalitas' :
				$($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
				break ;
			default:
		};
	});
	
	$('input[name=status]').iCheck({
		checkboxClass: 'icheckbox_square-green',
		radioClass: 'iradio_square-green',
	});
	$("#picture").fileinput({
		browseClass: "btn btn-{{config('configs.app_theme')}}",
		showRemove: false,
		showUpload: false,
		allowedFileExtensions: ["png", "jpg", "jpeg"],
		dropZoneEnabled: false,
		initialPreview: '<img src="{{asset($employee->photo)}}" class="kv-preview-data file-preview-image">',
		initialPreviewAsData: false,
		initialPreviewFileType: 'image',
		initialPreviewConfig: [
			{caption: "{{$employee->photo}}", downloadUrl: "{{asset($employee->photo)}}", size:"{{ @File::size(public_path($employee->photo))}}",url: false}
		],
		theme:'explorer-fas'
	});
	$('.datepickermy').datepicker({
		autoclose: true,
		format: 'dd/mm/yyyy'
	})
	$('.datepickermy').on('change', function(){
		if (!$.isEmptyObject($(this).closest("form").validate())) {
			$(this).closest("form").validate().form();
		}
	});
	// $('.start_date').datepicker({
	// 	autoclose: true,
	// 	format: 'dd/mm/yyyy'
	// })
	// function(chosen_date) {
    // 	$('input[name=start_date]').val(chosen_date.format('DD/MM/YYYY'));
    // });
	// $('.start_date').on('change', function(){
	// 	if (!$.isEmptyObject($(this).closest("form").validate())) {
	// 		$(this).closest("form").validate().form();
	// 	}
	// });
	$('input[name=start_date]').daterangepicker({
		singleDatePicker: true,
		autoUpdateInput: false,
		timePicker: false,
		locale: {
			format: 'DD/MM/YYYY'
		}
	},
	function(chosen_date) {
		$('input[name=start_date]').val(chosen_date.format('DD/MM/YYYY'));
	});
	$('input[name=start_date]').on('change', function(){
		if (!$.isEmptyObject($(this).closest("form").validate())) {
			$(this).closest("form").validate().form();
		}
	});
	$('input[name=end_date]').daterangepicker({
		singleDatePicker: true,
		autoUpdateInput: false,
		timePicker: false,
		locale: {
			format: 'DD/MM/YYYY'
		}
	},
	function(chosen_date) {
		$('input[name=end_date]').val(chosen_date.format('DD/MM/YYYY'));
	});
	$('input[name=end_date]').on('change', function(){
		if (!$.isEmptyObject($(this).closest("form").validate())) {
			$(this).closest("form").validate().form();
		}
	});
	// $('.end_date').datepicker({
	// 	autoclose: true,
	// 	format: 'dd/mm/yyyy'
	// })
	// function(chosen_date) {
    // 	$('input[name=end_date]').val(chosen_date.format('DD/MM/YYYY'));
    // });
	// $('.end_date').on('change', function(){
	// 	if (!$.isEmptyObject($(this).closest("form").validate())) {
	// 		$(this).closest("form").validate().form();
	// 	}
	// });
	$('.select2').select2();
	//Outsourcing
	$("#outsourcing_id").select2({
		ajax: {
			url: "{{route('outsourcing.select')}}",
			type: 'GET',
			dataType: 'json',
			data: function (term, page) {
				return {
					name: term,
					page: page,
					limit: 30,
				};
			},
			results: function (data, page) {
				var more = (page * 30) < data.total;
				var option = [];
				$.each(data.rows, function (index, item) {
					option.push({
						id: item.id,
						text: `${item.name}`
					});
				});
				return {
					results: option,
					more: more,
				};
			},
		},
		allowClear: true,
	});
	
	//End Outsourcing
	//Calendar
	$("#calendar_id").select2({
		ajax: {
			url: "{{route('calendar.select')}}",
			type: 'GET',
			dataType: 'json',
			data: function (term, page) {
				return {
					name: term,
					page: page,
					limit: 30,
				};
			},
			results: function (data, page) {
				var more = (page * 30) < data.total;
				var option = [];
				$.each(data.rows, function (index, item) {
					option.push({
						id: item.id,
						text: `${item.name}`
					});
				});
				return {
					results: option,
					more: more,
				};
			},
		},
		allowClear: true,
	});
	@if($employee->calendar_id)
		$("#calendar_id").select2('data',{id:{{$employee->calendar_id}},text:'{{$employee->calendar->name}}'}).trigger('change');
	@endif
	$(document).on("change", "#calendar_id", function () {
			if (!$.isEmptyObject($('#form').validate().submitted)) {
					$('#form').validate().form();
			}
	});
	//End Calendar
	//Workgroup Combination
	$("#workgroup_id").select2({
			ajax: {
					url: "{{route('workgroup.select')}}",
					type: 'GET',
					dataType: 'json',
					data: function (term, page) {
							return {
									name: term,
									page: page,
									limit: 30,
							};
					},
					results: function (data, page) {
							var more = (page * 30) < data.total;
							var option = [];
							$.each(data.rows, function (index, item) {
									option.push({
											id: item.id,
											text: `${item.name}`,
											code: item.code
									});
							});
							return {
									results: option,
									more: more,
							};
					},
			},
			allowClear: true,
	});
	@if($employee->workgroup_id)
		$("#workgroup_id").select2('data',{id:{{$employee->workgroup_id}},text:'{{$employee->workgroup->name}}',code:'{{$employee->workgroup->workgroupmaster->code}}'}).trigger('change');
	@endif
	$(document).on("change", "#workgroup_id", function () {
		if (!$.isEmptyObject($('#form').validate().submitted)) {
			$('#form').validate().form();
		}
		var code = $("#workgroup_id").select2('data').code;
		$('#outsourcing_id').select2('val','');
		if (code == "outsource") {
			$('#outsourcing_id').select2('readonly',false);
			$('#outsourcing_id').prop('required',true);
		}else{
			$('#outsourcing_id').select2('readonly',true);
			$('#outsourcing_id').prop('required',false);
		}
	});
	$('#workgroup_id').trigger('change');
	@if($employee->outsourcing_id)
		$("#outsourcing_id").select2('data',{id:{{$employee->outsourcing_id}},text:'{{$employee->outsourcing->name}}'}).trigger('change');
	@endif
	$(document).on("change", "#outsourcing_id", function () {
		if (!$.isEmptyObject($('#form').validate().submitted)) {
				$('#form').validate().form();
		}
	});
	//End Workgroup Combination
	$("#title_id").select2({
		ajax: {
				url: "{{route('title.select')}}",
				type: 'GET',
				dataType: 'json',
				data: function (term, page) {
						return {
								name: term,
								page: page,
								limit: 30,
						};
				},
				results: function (data, page) {
						var more = (page * 30) < data.total;
						var option = [];
						$.each(data.rows, function (index, item) {
								option.push({
										id: item.id,
										text: `${item.name}`
								});
						});
						return {
								results: option,
								more: more,
						};
				},
		},
		allowClear: true,
	});
	@if($employee->title_id)
		$("#title_id").select2('data',{id:{{$employee->title_id}},text:'{{$employee->title->name}}'}).trigger('change');
	@endif
	$(document).on("change", "#title_id", function () {
			if (!$.isEmptyObject($('#form').validate().submitted)) {
					$('#form').validate().form();
			}
	});
	// Department
	$("#department_id").select2({
		ajax: {
				url: "{{route('department.select')}}",
				type: 'GET',
				dataType: 'json',
				data: function (term, page) {
						return {
								name: term,
								page: page,
								limit: 30,
						};
				},
				results: function (data, page) {
						var more = (page * 30) < data.total;
						var option = [];
						$.each(data.rows, function (index, item) {
								option.push({
										id: item.id,
										text: `${item.name}`
								});
						});
						return {
								results: option,
								more: more,
						};
				},
		},
		allowClear: true,
	});
	@if($employee->department_id)
		$("#department_id").select2('data',{id:{{$employee->department_id}},text:'{{$employee->department->name}}'}).trigger('change');
	@endif
	$(document).on("change", "#department_id", function () {
			if (!$.isEmptyObject($('#form').validate().submitted)) {
					$('#form').validate().form();
			}
	});
	//End Department
	// Grade
	$("#grade_id").select2({
		ajax: {
				url: "{{route('grade.select')}}",
				type: 'GET',
				dataType: 'json',
				data: function (term, page) {
						return {
								name: term,
								page: page,
								limit: 30,
						};
				},
				results: function (data, page) {
						var more = (page * 30) < data.total;
						var option = [];
						$.each(data.rows, function (index, item) {
								option.push({
										id: item.id,
										text: `${item.name}`
								});
						});
						return {
								results: option,
								more: more,
						};
				},
		},
		allowClear: true,
	});
	@if($employee->grade_id)
		$("#grade_id").select2('data',{id:{{$employee->grade_id}},text:'{{$employee->grade->name}}'}).trigger('change');
	@endif
	$(document).on("change", "#grade_id", function () {
			if (!$.isEmptyObject($('#form').validate().submitted)) {
					$('#form').validate().form();
			}
	});
	// End Grade
	// Province
	$("#province_id").select2({
		ajax: {
				url: "{{route('province.select')}}",
				type: 'GET',
				dataType: 'json',
				data: function (term, page) {
						return {
								name: term,
								page: page,
								limit: 30,
						};
				},
				results: function (data, page) {
						var more = (page * 30) < data.total;
						var option = [];
						$.each(data.rows, function (index, item) {
								option.push({
										id: item.id,
										text: `${item.name}`
								});
						});
						return {
								results: option,
								more: more,
						};
				},
		},
		allowClear: true,
	});

	@if($employee->province_id)
			$("#province_id").select2('data',{id:{{$employee->province_id}},text:'{{$employee->province->name}}'}).trigger('change');
	@endif
	$(document).on("change", "#province_id", function () {
			if (!$.isEmptyObject($('#form').validate().submitted)) {
					$('#form').validate().form();
			}
	});
	
	$("#region_id").select2({
			ajax: {
					url: "{{route('region.select')}}",
					type: 'GET',
					dataType: 'json',
					data: function (term, page) {
							return {
									province_id: $('#province_id').val(),
									name: term,
									page: page,
									limit: 30,
							};
					},
					results: function (data, page) {
							var more = (page * 30) < data.total;
							var option = [];
							$.each(data.rows, function (index, item) {
									option.push({
											id: item.id,
											text: `${item.type} ${item.name}`
									});
							});
							return {
									results: option,
									more: more,
							};
					},
			},
			allowClear: true,
	});

	@if($employee->region_id)
		$("#region_id").select2('data',{id:{{$employee->region_id}},text:'{{$employee->region->type.' '.$employee->region->name}}'}).trigger('change');
	@endif

	$(document).on("change", "#region_id", function () {
			if (!$.isEmptyObject($('#form').validate().submitted)) {
					$('#form').validate().form();
			}
	});
	// Endprovince

	// Place Birth Date
	$("#place_of_birth").select2({
		ajax: {
			url: "{{route('region.select')}}",
			type: 'GET',
			dataType: 'json',
			data: function (term, page) {
					return {
							name: term,
							page: page,
							limit: 30,
					};
			},
			results: function (data, page) {
					var more = (page * 30) < data.total;
					var option = [];
					$.each(data.rows, function (index, item) {
							option.push({
									id: item.id,
									text: `${item.type} ${item.name}`
							});
					});
					return {
							results: option,
							more: more,
					};
			},
		},
		allowClear: true,
	});

	@if($employee->place_of_birth)
		$("#place_of_birth").select2('data',{id:{{$employee->place_of_birth}},text:'{{$employee->place->type.' '.$employee->place->name}}'}).trigger('change');
	@endif

	$(document).on("change", "#place_of_birth", function () {
			if (!$.isEmptyObject($('#form').validate().submitted)) {
					$('#form').validate().form();
			}
	});
	// End Birth Date

	// Working Time
	$("#working_time").select2({
			ajax: {
				url: "{{route('workingtime.select')}}",
				type: 'GET',
				dataType: 'json',
				data: function (term, page) {
						return {
								working_time_type: $('#working_time_type').val(),
								description: term,
								page: page,
								limit: 30,
						};
				},
				results: function (data, page) {
						var more = (page * 30) < data.total;
						var option = [];
						$.each(data.rows, function (index, item) {
								option.push({
										id: item.id,
										text: `${item.description}`
								});
						});
						return {
								results: option,
								more: more,
						};
				},
			},
			allowClear: true,
	});
	$(document).on("change", "#working_time", function () {
		if (!$.isEmptyObject($('#form').validate().submitted)) {
				$('#form').validate().form();
		}
	});


	$(document).on("change", "#working_time_type", function () {
		if (!$.isEmptyObject($('#form').validate().submitted)) {
				$('#form').validate().form();
		}
		$('#working_time').select2('val','');
		if (this.value =='Non-Shift') {
			$('#working_time').select2('readonly',false);
			$('#working_time').prop('required',true);
		} else {
			$('#working_time').select2('readonly',true);
			$('#working_time').prop('required',false);
		}

	});
	$('#working_time_type').trigger('change');
	@if($employee->workingtime)
			$("#working_time").select2('data',{id:{{$employee->workingtime->id}},text:'{{$employee->workingtime->description}}'}).trigger('change');
	@endif
	//End Working Time
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
			else{
				error.insertAfter(element);
			}
		},
		submitHandler: function() {
			var form_data = new FormData($('#form_employee')[0]);
			form_data.append("outsourcing_id", ("#outsourcing_id").val());
			$.ajax({
				url:$('#form_employee').attr('action'),
				method:'post',
				data: new FormData($('#form')[0]),
				processData: false,
				contentType: false,
				dataType: 'json',
				beforeSend:function(){
				$('.overlay').removeClass('d-none');
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
			})
		}
	});
	$("#form_salary").validate({
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
			$.ajax({
				url:$('#form_salary').attr('action'),
				method:'post',
				data: new FormData($('#form_salary')[0]),
				processData: false,
				contentType: false,
				dataType: 'json',
				beforeSend:function(){
				$('.overlay').removeClass('d-none');
				}
			}).done(function(response){
					$('.overlay').addClass('d-none');
					if(response.status){
					$('#add_salary').modal('hide');
					dataTableSalary.draw();
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
			})
		}
	});
	// Contract
	$("#form_contract").validate({
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
			$.ajax({
				url:$('#form_contract').attr('action'),
				method:'post',
				data: new FormData($('#form_contract')[0]),
				processData: false,
				contentType: false,
				dataType: 'json',
				beforeSend:function(){
				$('.overlay').removeClass('d-none');
				}
			}).done(function(response){
					$('.overlay').addClass('d-none');
					if(response.status){
					$('#add_contract').modal('hide');
					dataTableContract.draw();
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
			})
		}
	});
	dataTableContract = $('#table-contract').DataTable( {
		stateSave:true,
		processing: true,
		serverSide: true,
		filter:false,
		info:false,
		lengthChange:true,
		responsive: true,
		order: [[ 6, "asc" ]],
		ajax: {
			url: "{{route('employeecontract.read')}}",
			type: "GET",
			data:function(data){
				var name = $('#form-search').find('input[name=name]').val();
				data.name = name;
				data.employee_id = {{$employee->id}};
			}
		},
		columnDefs:[
			{
				orderable: false,targets:[0]
			},
			{ className: "text-right", targets: [0] },
			{ className: "text-center", targets: [4,5] },
			{
			render: function (data, type, row) {
				return `${row.start_date} s/d ${row.end_date}`
			},
			targets: [2]
			},
			{
			render: function (data, type, row) {
				// return `<a href="${row.file}" target="_blank"><img class="img-fluid" src="${row.file}" height=\"100\" width=\"150\"/><a/>`
					return `<a onclick="showContract(this)" data-url="${row.link}" href="#"><span class="badge badge-info">Prview</span><a/>`
			},
			targets: [4]
			},
			{
				render: function (data, type, row) {
					if (row.status == 'Active') {
						return `<span class="badge badge-success">{{ __('general.actv') }}</span>`
					} else if(row.status == 'Non Active') 
					{
						return `<span class="badge badge-warning">{{ __('general.noactv') }}</span>`
					}else{
						return `<span class="badge badge-danger">Expired</span>`
					}
				},
				targets: [5]
			},
			{ render: function ( data, type, row ) {
				return `<div class="dropdown">
					<button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-bars"></i>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
						<li><a class="dropdown-item editcontract" href="#" data-id="${row.id}"><i class="fa fa-pencil-alt mr-2"></i> {{ __('general.edt') }}</a></li>
						<li><a class="dropdown-item deletecontract" href="#" data-id="${row.id}"><i class="fa fa-trash mr-2"></i> Delete</a></li>
					</ul>
					</div>`
			},targets: [6]
			}
		],
		columns: [
			{ data: "no" },
			{ data: "code" },
			{ data: "start_date" },
			{ data: "description"},
			{ data: "file"},
			{ data: "status" },
			{ data: "id" },
		]
	});
	$('.add_contract').on('click',function(){
		$('#form_contract')[0].reset();
		$('#form_contract').attr('action',"{{route('employeecontract.store')}}");
		$('#form_contract input[name=_method]').attr('value','POST');
		$('#form_contract input[name=employee_id]').attr('value',{{ $employee->id }});
		$('#form_contract input[name=code]').attr('value','');
		$('#form_contract input[name=start_date]').attr('value','');
		$('#form_contract input[name=end_date]').attr('value','');
		$('#form_contract textarea[name=description]').html('');
		$('#form_contract input[name=file]').attr('value','');
		$('#form_contract select[name=status]').select2("val", "");
		$('#form_contract .invalid-feedback').each(function () { $(this).remove(); });
		$('#form_contract .form-group').removeClass('has-error').removeClass('has-success');
		$('#add_contract .modal-title').html('Add Contract');
		$('#add_contract').modal('show');
	});
	$(document).on('click','.editcontract',function(){
		var id = $(this).data('id');
		$.ajax({
			url:`{{url('admin/employeecontract')}}/${id}/edit`,
			method:'GET',
			dataType:'json',
			beforeSend:function(){
				$('#box-menu .overlay').removeClass('d-none');
			},
		}).done(function(response){
			$('#box-menu .overlay').addClass('d-none');
			if(response.status){
				$('#add_contract .modal-title').html('Edit Contract');
				$('#add_contract').modal('show');
				$('#form_contract')[0].reset();
				$('#form_contract .invalid-feedback').each(function () { $(this).remove(); });
				$('#form_contract .form-group').removeClass('has-error').removeClass('has-success');
				$('#form_contract input[name=_method]').attr('value','PUT');
				$('#form_contract input[name=employee_id]').attr('value',{{$employee->id}});
				$('#form_contract input[name=code]').attr('value',response.data.code);
				$('#form_contract input[name=start_date]').attr('value',response.data.start_date);
				$('#form_contract input[name=end_date]').attr('value',response.data.end_date);
				$('#form_contract input[name=file]').attr('value',response.data.file);
				$('#form_contract textarea[name=description]').html(response.data.description);
				$('#form_contract select[name=status]').select2('val',response.data.status);
				$('#contract-preview').html(response.data.file).attr('data-url',response.data.link);
				$('#form_contract').attr('action',`{{url('admin/employeecontract/')}}/${response.data.id}`);
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
	$(document).on('click','.deletecontract',function(){
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
			title:'Delete Contract Data?',
			message:'Data that has been deleted cannot be recovered',
			callback: function(result) {
					if(result) {
						var data = {
										_token: "{{ csrf_token() }}",
										id: id
									};
						$.ajax({
						url: `{{url('admin/employeecontract')}}/${id}`,
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
								dataTableContract.draw();
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

	$("#filecontract").fileinput({
		browseClass: "btn btn-{{config('configs.app_theme')}}",
		showRemove: false,
		showUpload: false,
		allowedFileExtensions: ["png", "jpg", "jpeg", "pdf"],
		dropZoneEnabled: false,
		initialPreviewAsData: false,
		initialPreviewFileType: 'image',
		theme:'explorer-fas'
	});
	// End Contract
	// Career
	$("#form_career").validate({
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
		$.ajax({
			url:$('#form_career').attr('action'),
			method:'post',
			data: new FormData($('#form_career')[0]),
			processData: false,
			contentType: false,
			dataType: 'json',
			beforeSend:function(){
			$('.overlay').removeClass('d-none');
			}
		}).done(function(response){
				$('.overlay').addClass('d-none');
				if(response.status){
				$('#add_career').modal('hide');
				dataTableCareer.draw();
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
		})
		}
	});
	dataTableCareer = $('#table-career').DataTable( {
		stateSave:true,
		processing: true,
		serverSide: true,
		filter:false,
		info:false,
		lengthChange:true,
		responsive: true,
		order: [[ 6, "asc" ]],
		ajax: {
			url: "{{route('employeecareer.read')}}",
			type: "GET",
			data:function(data){
				var position = $('#form-search').find('input[name=position]').val();
				data.position = position;
				data.employee_id = {{$employee->id}};
			}
		},
		columnDefs:[
			{
				orderable: false,targets:[0]
			},
			{ className: "text-right", targets: [0] },
			{ className: "text-center", targets: [6] },
			{render: function (data, type, row) {
				return `${row.start_date} s/d ${row.end_date}`
			},targets: [4]},
			{ render: function ( data, type, row ) {
				return `<div class="dropdown">
					<button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-bars"></i>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
						<li><a class="dropdown-item editcareer" href="#" data-id="${row.id}"><i class="fa fa-pencil-alt mr-2"></i> {{ __('general.edt') }}</a></li>
						<li><a class="dropdown-item deletecareer" href="#" data-id="${row.id}"><i class="fa fa-trash mr-2"></i> Delete</a></li>
					</ul>
					</div>`
			},targets: [6]
			}
		],
		columns: [
			{ data: "no" },
			{ data: "position" },
			{ data: "grade" },
			{ data: "department"},
			{ data: "start_date" },
			{ data: "reference" },
			{ data: "id" },
		]
	});
	$('.add_career').on('click',function(){
		$('#form_career')[0].reset();
		$('#form_career').attr('action',"{{route('employeecareer.store')}}");
		$('#form_career input[name=_method]').attr('value','POST');
		$('#form_career input[name=employee_id]').attr('value',{{ $employee->id }});
		$('#form_career input[name=position]').attr('value','');
		$('#form_career input[name=grade]').attr('value','');
		$('#form_career input[name=start_date]').attr('value','');
		$('#form_career input[name=end_date]').attr('value','');
		$('#form_career input[name=department]').attr('value','');
		$('#form_career input[name=reference]').attr('value','');
		$('#form_career .invalid-feedback').each(function () { $(this).remove(); });
		$('#form_career .form-group').removeClass('has-error').removeClass('has-success');
		$('#add_career .modal-title').html('Add Career');
		$('#add_career').modal('show');
	});
	$(document).on('click','.editcareer',function(){
		var id = $(this).data('id');
		$.ajax({
			url:`{{url('admin/employeecareer')}}/${id}/edit`,
			method:'GET',
			dataType:'json',
			beforeSend:function(){
				$('#box-menu .overlay').removeClass('d-none');
			},
		}).done(function(response){
			$('#box-menu .overlay').addClass('d-none');
			if(response.status){
				$('#add_career .modal-title').html('Edit Career');
				$('#add_career').modal('show');
				$('#form_career')[0].reset();
				$('#form_career .invalid-feedback').each(function () { $(this).remove(); });
				$('#form_career .form-group').removeClass('has-error').removeClass('has-success');
				$('#form_career input[name=_method]').attr('value','PUT');
				$('#form_career input[name=employee_id]').attr('value',{{$employee->id}});
				$('#form_career input[name=position]').attr('value',response.data.position);
				$('#form_career input[name=start_date]').attr('value',response.data.start_date);
				$('#form_career input[name=end_date]').attr('value',response.data.end_date);
				$('#form_career input[name=department]').attr('value',response.data.department);
				$('#form_career input[name=reference]').attr('value',response.data.reference);					
				$('#form_career').attr('action',`{{url('admin/employeecareer/')}}/${response.data.id}`);
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
	$(document).on('click','.deletecareer',function(){
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
			title:'Delete Data Career?',
			message:'Data that has been deleted cannot be recovered',
			callback: function(result) {
				if(result) {
					var data = {
									_token: "{{ csrf_token() }}",
									id: id
								};
					$.ajax({
					url: `{{url('admin/employeecareer')}}/${id}`,
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
							dataTableCareer.draw();
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
	// End Contract

	// Experience
	$("#form_experience").validate({
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
		$.ajax({
			url:$('#form_experience').attr('action'),
			method:'post',
			data: new FormData($('#form_experience')[0]),
			processData: false,
			contentType: false,
			dataType: 'json',
			beforeSend:function(){
			$('.overlay').removeClass('d-none');
			}
		}).done(function(response){
				$('.overlay').addClass('d-none');
				if(response.status){
				$('#add_experience').modal('hide');
				dataTableExperience.draw();
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
		})
		}
	});
	dataTableExperience = $('#table-experience').DataTable( {
		stateSave:true,
		processing: true,
		serverSide: true,
		filter:false,
		info:false,
		lengthChange:true,
		responsive: true,
		order: [[ 5, "asc" ]],
		ajax: {
			url: "{{route('employeeexperience.read')}}",
			type: "GET",
			data:function(data){
				var last_position = $('#form-search').find('input[name=last_position]').val();
				data.last_position = last_position;
				data.employee_id = {{$employee->id}};
			}
		},
		columnDefs:[
			{
				orderable: false,targets:[0]
			},
			{ className: "text-right", targets: [0] },
			{ className: "text-center", targets: [5] },
			{render: function (data, type, row) {
				return `${row.start_date} s/d ${row.end_date}`
			},targets: [3]},
			{ render: function ( data, type, row ) {
				return `<div class="dropdown">
					<button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-bars"></i>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
						<li><a class="dropdown-item editexperience" href="#" data-id="${row.id}"><i class="fa fa-pencil-alt mr-2"></i> {{ __('general.edt') }}</a></li>
						<li><a class="dropdown-item deleteexperience" href="#" data-id="${row.id}"><i class="fa fa-trash mr-2"></i> Delete</a></li>
					</ul>
					</div>`
			},targets: [5]
			}
		],
		columns: [
			{ data: "no" },
			{ data: "last_position" },
			{ data: "company" },
			{ data: "start_date"},
			{ data: "duration" },
			{ data: "id" },
		]
	});
	$('.add_experience').on('click',function(){
		$('#form_experience')[0].reset();
		$('#form_experience').attr('action',"{{route('employeeexperience.store')}}");
		$('#form_experience input[name=_method]').attr('value','POST');
		$('#form_experience input[name=employee_id]').attr('value',{{ $employee->id }});
		$('#form_experience input[name=last_position]').attr('value','');
		$('#form_experience input[name=company]').attr('value','');
		$('#form_experience input[name=start_date]').attr('value','');
		$('#form_experience input[name=end_date]').attr('value','');
		$('#form_experience input[name=duration]').attr('value','');
		$('#form_experience .invalid-feedback').each(function () { $(this).remove(); });
		$('#form_experience .form-group').removeClass('has-error').removeClass('has-success');
		$('#add_experience .modal-title').html('Add Experience');
		$('#add_experience').modal('show');
	});
	$(document).on('click','.editexperience',function(){
		var id = $(this).data('id');
		$.ajax({
			url:`{{url('admin/employeeexperience')}}/${id}/edit`,
			method:'GET',
			dataType:'json',
			beforeSend:function(){
				$('#box-menu .overlay').removeClass('d-none');
			},
		}).done(function(response){
			$('#box-menu .overlay').addClass('d-none');
			if(response.status){
				$('#add_experience .modal-title').html('Edit Experience');
				$('#add_experience').modal('show');
				$('#form_experience')[0].reset();
				$('#form_experience .invalid-feedback').each(function () { $(this).remove(); });
				$('#form_experience .form-group').removeClass('has-error').removeClass('has-success');
				$('#form_experience input[name=_method]').attr('value','PUT');
				$('#form_experience input[name=employee_id]').attr('value',{{$employee->id}});
				$('#form_experience input[name=last_position]').attr('value',response.data.last_position);
				$('#form_experience input[name=start_date]').attr('value',response.data.start_date);
				$('#form_experience input[name=end_date]').attr('value',response.data.end_date);
				$('#form_experience input[name=company]').attr('value',response.data.company);
				$('#form_experience input[name=duration]').attr('value',response.data.duration);					
				$('#form_experience').attr('action',`{{url('admin/employeeexperience/')}}/${response.data.id}`);
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
	$(document).on('click','.deleteexperience',function(){
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
		title:'Delete Data Experience?',
		message:'Data that has been deleted cannot be recovered',
		callback: function(result) {
			if(result) {
				var data = {
								_token: "{{ csrf_token() }}",
								id: id
							};
				$.ajax({
				url: `{{url('admin/employeeexperience')}}/${id}`,
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
						dataTableExperience.draw();
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
	// End Experience

	// Education
	$("#form_education").validate({
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
		$.ajax({
			url:$('#form_education').attr('action'),
			method:'post',
			data: new FormData($('#form_education')[0]),
			processData: false,
			contentType: false,
			dataType: 'json',
			beforeSend:function(){
			$('.overlay').removeClass('d-none');
			}
		}).done(function(response){
				$('.overlay').addClass('d-none');
				if(response.status){
				$('#add_education').modal('hide');
				dataTableEducation.draw();
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
		})
		}
	});
	dataTableEducation = $('#table-education').DataTable( {
		stateSave:true,
		processing: true,
		serverSide: true,
		filter:false,
		info:false,
		lengthChange:true,
		responsive: true,
		order: [[ 4, "asc" ]],
		ajax: {
			url: "{{route('employeeeducation.read')}}",
			type: "GET",
			data:function(data){
				
				data.employee_id = {{$employee->id}};
			}
		},
		columnDefs:[
			{
				orderable: false,targets:[0]
			},
			{ className: "text-right", targets: [0] },
			{ className: "text-center", targets: [4] },
			
			{ render: function ( data, type, row ) {
				return `<div class="dropdown">
					<button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-bars"></i>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
						<li><a class="dropdown-item editeducation" href="#" data-id="${row.id}"><i class="fa fa-pencil-alt mr-2"></i> {{ __('general.edt') }}</a></li>
						<li><a class="dropdown-item deleteeducation" href="#" data-id="${row.id}"><i class="fa fa-trash mr-2"></i> Delete</a></li>
					</ul>
					</div>`
			},targets: [4]
			}
		],
		columns: [
			{ data: "no" },
			{ data: "institution" },
			{ data: "stage" },
			{ data: "period"},
			{ data: "id" },
		]
	});
	$('.add_education').on('click',function(){
		$('#form_education')[0].reset();
		$('#form_education').attr('action',"{{route('employeeeducation.store')}}");
		$('#form_education input[name=_method]').attr('value','POST');
		$('#form_education input[name=employee_id]').attr('value',{{ $employee->id }});
		$('#form_education input[name=institution]').attr('value','');
		$('#form_education input[name=stage]').attr('value','');
		$('#form_education input[name=period]').attr('value','');
		$('#form_education .invalid-feedback').each(function () { $(this).remove(); });
		$('#form_education .form-group').removeClass('has-error').removeClass('has-success');
		$('#add_education .modal-title').html('Add Education');
		$('#add_education').modal('show');
	});
	$(document).on('click','.editeducation',function(){
		var id = $(this).data('id');
		$.ajax({
			url:`{{url('admin/employeeeducation')}}/${id}/edit`,
			method:'GET',
			dataType:'json',
			beforeSend:function(){
				$('#box-menu .overlay').removeClass('d-none');
			},
		}).done(function(response){
			$('#box-menu .overlay').addClass('d-none');
			if(response.status){
				$('#add_education .modal-title').html('{{ __('general.edt') }} Education');
				$('#add_education').modal('show');
				$('#form_education')[0].reset();
				$('#form_education .invalid-feedback').each(function () { $(this).remove(); });
				$('#form_education .form-group').removeClass('has-error').removeClass('has-success');
				$('#form_education input[name=_method]').attr('value','PUT');
				$('#form_education input[name=employee_id]').attr('value',{{$employee->id}});
				$('#form_education input[name=institution]').attr('value',response.data.institution);
				$('#form_education input[name=stage]').attr('value',response.data.stage);
				$('#form_education input[name=period]').attr('value',response.data.period);
				$('#form_education').attr('action',`{{url('admin/employeeeducation/')}}/${response.data.id}`);
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
	$(document).on('click','.deleteeducation',function(){
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
		title:'Delete Data Education?',
		message:'Data that has been deleted cannot be recovered',
		callback: function(result) {
			if(result) {
				var data = {
								_token: "{{ csrf_token() }}",
								id: id
							};
				$.ajax({
				url: `{{url('admin/employeeeducation')}}/${id}`,
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
						dataTableEducation.draw();
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
	// End education

	// Training
	$("#form_training").validate({
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
		$.ajax({
			url:$('#form_training').attr('action'),
			method:'post',
			data: new FormData($('#form_training')[0]),
			processData: false,
			contentType: false,
			dataType: 'json',
			beforeSend:function(){
			$('.overlay').removeClass('d-none');
			}
		}).done(function(response){
				$('.overlay').addClass('d-none');
				if(response.status){
				$('#add_training').modal('hide');
				dataTableTraining.draw();
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
		})
		}
	});
	dataTableTraining = $('#table-training').DataTable( {
		stateSave:true,
		processing: true,
		serverSide: true,
		filter:false,
		info:false,
		lengthChange:true,
		responsive: true,
		order: [[ 5, "asc" ]],
		ajax: {
			url: "{{route('employeetraining.read')}}",
			type: "GET",
			data:function(data){
				data.employee_id = {{$employee->id}};
			}
		},
		columnDefs:[
			{
				orderable: false,targets:[0]
			},
			{ className: "text-right", targets: [0] },
			{ className: "text-center", targets: [5] },
			{
				render: function (data, type, row) {
					return `${row.start_date} s/d ${row.end_date}`
				}, targets: [3]
			},
			{ render: function ( data, type, row ) {
				return `<div class="dropdown">
					<button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-bars"></i>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
						<li><a class="dropdown-item edittraining" href="#" data-id="${row.id}"><i class="fa fa-pencil-alt mr-2"></i> {{ __('general.edt') }}</a></li>
						<li><a class="dropdown-item deletetraining" href="#" data-id="${row.id}"><i class="fa fa-trash mr-2"></i> Delete</a></li>
					</ul>
					</div>`
			},targets: [5]
			}
		],
		columns: [
			{ data: "no" },
			{ data: "code" },
			{ data: "issued" },
			{ data: "start_date"},
			{ data: "description"},
			{ data: "id" }
		]
	});
	$('.add_training').on('click',function(){
		$('#form_training')[0].reset();
		$('#form_training').attr('action',"{{route('employeetraining.store')}}");
		$('#form_training input[name=_method]').attr('value','POST');
		$('#form_training input[name=employee_id]').attr('value',{{ $employee->id }});
		$('#form_training input[name=code]').attr('value','');
		$('#form_training input[name=issued]').attr('value','');
		$('#form_training input[name=start_date]').attr('value','');
		$('#form_training input[name=end_date]').attr('value','');
		$('#form_training textarea[name=description]').html('');
		$('#form_training .invalid-feedback').each(function () { $(this).remove(); });
		$('#form_training .form-group').removeClass('has-error').removeClass('has-success');
		$('#add_training .modal-title').html('Add Training');
		$('#add_training').modal('show');
	});
	$(document).on('click','.edittraining',function(){
		var id = $(this).data('id');
		$.ajax({
			url:`{{url('admin/employeetraining')}}/${id}/edit`,
			method:'GET',
			dataType:'json',
			beforeSend:function(){
				$('#box-menu .overlay').removeClass('d-none');
			},
		}).done(function(response){
			$('#box-menu .overlay').addClass('d-none');
			if(response.status){
				$('#add_training .modal-title').html('{{ __('general.edt') }} Training');
				$('#add_training').modal('show');
				$('#form_training')[0].reset();
				$('#form_training .invalid-feedback').each(function () { $(this).remove(); });
				$('#form_training .form-group').removeClass('has-error').removeClass('has-success');
				$('#form_training input[name=_method]').attr('value','PUT');
				$('#form_training input[name=employee_id]').attr('value',{{$employee->id}});
				$('#form_training input[name=code]').attr('value',response.data.code);
				$('#form_training input[name=issued]').attr('value',response.data.issued);
				$('#form_training input[name=start_date]').attr('value',response.data.start_date);
				$('#form_training input[name=end_date]').attr('value',response.data.end_date);
				$('#form_training textarea[name=description]').html(response.data.description);
				$('#form_training').attr('action',`{{url('admin/employeetraining/')}}/${response.data.id}`);
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
	$(document).on('click','.deletetraining',function(){
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
		title:'Delete Data Training and Certification?',
		message:'Data that has been deleted cannot be recovered',
		callback: function(result) {
			if(result) {
				var data = {
								_token: "{{ csrf_token() }}",
								id: id
							};
				$.ajax({
				url: `{{url('admin/employeetraining')}}/${id}`,
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
						dataTableTraining.draw();
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
	// End Training

	// BPJS
	$("#form_bpjs").validate({
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
		$.ajax({
			url:$('#form_bpjs').attr('action'),
			method:'post',
			data: new FormData($('#form_bpjs')[0]),
			processData: false,
			contentType: false,
			dataType: 'json',
			beforeSend:function(){
			$('.overlay').removeClass('d-none');
			}
		}).done(function(response){
				$('.overlay').addClass('d-none');
				if(response.status){
				$('#add_bpjs').modal('hide');
				dataTableBpjs.draw();
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
		})
		}
	});
	dataTableBpjs = $('#table-bpjs').DataTable( {
		stateSave:true,
		processing: true,
		serverSide: true,
		filter:false,
		info:false,
		lengthChange:true,
		responsive: true,
		order: [[ 5, "asc" ]],
		ajax: {
			url: "{{route('employeebpjs.read')}}",
			type: "GET",
			data:function(data){
				
				data.employee_id = {{$employee->id}};
			}
		},
		columnDefs:[
			{
				orderable: false,targets:[0]
			},
			{ className: "text-right", targets: [0] },
			{ className: "text-center", targets: [5] },

			{ render: function ( data, type, row ) {
				return `<div class="dropdown">
					<button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-bars"></i>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
						<li><a class="dropdown-item editbpjs" href="#" data-id="${row.id}"><i class="fa fa-pencil-alt mr-2"></i> {{ __('general.edt') }}</a></li>
						<li><a class="dropdown-item deletebpjs" href="#" data-id="${row.id}"><i class="fa fa-trash mr-2"></i> Delete</a></li>
					</ul>
					</div>`
			},targets: [5]
			}
		],
		columns: [
			{ data: "no" },
			{ data: "name" },
			{ data: "nik" },
			{ data: "relation"},
			{ data: "address"},
			{ data: "id" }
		]
	});
	$('.add_bpjs').on('click',function(){
		$('#form_bpjs')[0].reset();
		$('#form_bpjs').attr('action',"{{route('employeebpjs.store')}}");
		$('#form_bpjs input[name=_method]').attr('value','POST');
		$('#form_bpjs input[name=employee_id]').attr('value',{{ $employee->id }});
		$('#form_bpjs input[name=name]').attr('value','');
		$('#form_bpjs input[name=nik]').attr('value','');
		$('#form_bpjs input[name=relation]').val();
		$('#form_bpjs textarea[name=address]').html('');
		$('#form_bpjs .invalid-feedback').each(function () { $(this).remove(); });
		$('#form_bpjs .form-group').removeClass('has-error').removeClass('has-success');
		$('#add_bpjs .modal-title').html('Add BPJS');
		$('#add_bpjs').modal('show');
	});
	$(document).on('click','.editbpjs',function(){
		var id = $(this).data('id');
		$.ajax({
			url:`{{url('admin/employeebpjs')}}/${id}/edit`,
			method:'GET',
			dataType:'json',
			beforeSend:function(){
				$('#box-menu .overlay').removeClass('d-none');
			},
		}).done(function(response){
			$('#box-menu .overlay').addClass('d-none');
			if(response.status){
				$('#add_bpjs .modal-title').html('{{ __('general.edt') }} BPJS');
				$('#add_bpjs').modal('show');
				$('#form_bpjs')[0].reset();
				$('#form_bpjs .invalid-feedback').each(function () { $(this).remove(); });
				$('#form_bpjs .form-group').removeClass('has-error').removeClass('has-success');
				$('#form_bpjs input[name=_method]').attr('value','PUT');
				$('#form_bpjs input[name=employee_id]').attr('value',{{$employee->id}});
				$('#form_bpjs input[name=name]').attr('value',response.data.name);
				$('#form_bpjs input[name=nik]').attr('value',response.data.nik);
				$('#form_bpjs input[name=relation]').attr('value',response.data.relation);
				$('#form_bpjs textarea[name=address]').html(response.data.address);
				$('#form_bpjs').attr('action',`{{url('admin/employeebpjs/')}}/${response.data.id}`);
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
	$(document).on('click','.deletebpjs',function(){
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
		title:'Deelete Data BPJS?',
		message:'Data that has been deleted cannot be recovered',
		callback: function(result) {
			if(result) {
				var data = {
								_token: "{{ csrf_token() }}",
								id: id
							};
				$.ajax({
				url: `{{url('admin/employeebpjs')}}/${id}`,
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
						dataTableBpjs.draw();
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
	// End bpjs


	// Member
	$("#form_member").validate({
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
		$.ajax({
			url:$('#form_member').attr('action'),
			method:'post',
			data: new FormData($('#form_member')[0]),
			processData: false,
			contentType: false,
			dataType: 'json',
			beforeSend:function(){
			$('.overlay').removeClass('d-none');
			}
		}).done(function(response){
				$('.overlay').addClass('d-none');
				if(response.status){
				$('#add_member').modal('hide');
				dataTablemember.draw();
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
		})
		}
	});
	dataTablemember = $('#table-member').DataTable( {
		stateSave:true,
		processing: true,
		serverSide: true,
		filter:false,
		info:false,
		lengthChange:true,
		responsive: true,
		order: [[ 5, "asc" ]],
		ajax: {
			url: "{{route('employeemember.read')}}",
			type: "GET",
			data:function(data){
				
				data.employee_id = {{$employee->id}};
			}
		},
		columnDefs:[
			{
				orderable: false,targets:[0]
			},
			{ className: "text-right", targets: [0] },
			{ className: "text-center", targets: [5] },

			{ render: function ( data, type, row ) {
				return `<div class="dropdown">
					<button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-bars"></i>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
						<li><a class="dropdown-item editmember" href="#" data-id="${row.id}"><i class="fa fa-pencil-alt mr-2"></i> {{ __('general.edt') }}</a></li>
						<li><a class="dropdown-item deletemember" href="#" data-id="${row.id}"><i class="fa fa-trash mr-2"></i> Delete</a></li>
					</ul>
					</div>`
			},targets: [5]
			}
		],
		columns: [
			{ data: "no" },
			{ data: "name" },
			{ data: "nik" },
			{ data: "relation"},
			{ data: "address"},
			{ data: "id" }
		]
	});
	$('.add_member').on('click',function(){
		$('#form_member')[0].reset();
		$('#form_member').attr('action',"{{route('employeemember.store')}}");
		$('#form_member input[name=_method]').attr('value','POST');
		$('#form_member input[name=employee_id]').attr('value',{{ $employee->id }});
		$('#form_member input[name=name]').attr('value','');
		$('#form_member input[name=nik]').attr('value','');
		$('#form_member input[name=relation]').attr('value','');
		$('#form_member textarea[name=address]').html('');
		$('#form_member .invalid-feedback').each(function () { $(this).remove(); });
		$('#form_member .form-group').removeClass('has-error').removeClass('has-success');
		$('#add_member .modal-title').html('Add Member');
		$('#add_member').modal('show');
	});
	$(document).on('click','.editmember',function(){
		var id = $(this).data('id');
		$.ajax({
			url:`{{url('admin/employeemember')}}/${id}/edit`,
			method:'GET',
			dataType:'json',
			beforeSend:function(){
				$('#box-menu .overlay').removeClass('d-none');
			},
		}).done(function(response){
			$('#box-menu .overlay').addClass('d-none');
			if(response.status){
				$('#add_member .modal-title').html('{{ __('general.edt') }} Member');
				$('#add_member').modal('show');
				$('#form_member')[0].reset();
				$('#form_member .invalid-feedback').each(function () { $(this).remove(); });
				$('#form_member .form-group').removeClass('has-error').removeClass('has-success');
				$('#form_member input[name=_method]').attr('value','PUT');
				$('#form_member input[name=employee_id]').attr('value',{{$employee->id}});
				$('#form_member input[name=name]').attr('value',response.data.name);
				$('#form_member input[name=nik]').attr('value',response.data.nik);
				$('#form_member input[name=relation]').attr('value',response.data.relation);
				$('#form_member textarea[name=address]').html(response.data.address);
				$('#form_member').attr('action',`{{url('admin/employeemember/')}}/${response.data.id}`);
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
	$(document).on('click','.deletemember',function(){
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
		title:'Delete	 Data Member?',
		message:'Data that has been deleted cannot be recovered',
		callback: function(result) {
			if(result) {
				var data = {
								_token: "{{ csrf_token() }}",
								id: id
							};
				$.ajax({
				url: `{{url('admin/employeemember')}}/${id}`,
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
						dataTablemember.draw();
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
	// End bpjs

	// Document
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
				dataTableDocument.draw();
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
		})
		}
	});
	dataTableDocument = $('#table-document').DataTable( {
		stateSave:true,
		processing: true,
		serverSide: true,
		filter:false,
		info:false,
		lengthChange:true,
		responsive: true,
		order: [[ 5, "asc" ]],
		ajax: {
			url: "{{route('employeedocument.read')}}",
			type: "GET",
			data:function(data){
				var name = $('#form-search').find('input[name=name]').val();
				data.name = name;
				data.employee_id = {{$employee->id}};
			}
		},
		columnDefs:[
			{
				orderable: false,targets:[0]
			},
			{ className: "text-right", targets: [0] },
			{ className: "text-center", targets: [4,5] },
			{
			render: function (data, type, row) {
				// return `<a href="${row.file}" target="_blank"><img class="img-fluid" src="${row.file}" height=\"100\" width=\"150\"/><a/>`
					return `<a onclick="showDocument(this)" data-url="${row.link}" href="#"><span class="badge badge-info">Preview</span><a/>`
			},
			targets: [4]
			},
			{ render: function ( data, type, row ) {
				return `<div class="dropdown">
					<button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-bars"></i>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
						<li><a class="dropdown-item editdocument" href="#" data-id="${row.id}"><i class="fa fa-pencil-alt mr-2"></i> {{ __('general.edt') }}</a></li>
						<li><a class="dropdown-item deletedocument" href="#" data-id="${row.id}"><i class="fa fa-trash mr-2"></i> Delete</a></li>
					</ul>
					</div>`
			},targets: [5]
			}
		],
		columns: [
			{ data: "no" },
			{ data: "category" },
			{ data: "name" },
			{ data: "description"},
			{ data: "file" },
			{ data: "id" },
		]
	});
	$('.add_document').on('click',function(){
		$('#form_document')[0].reset();
		$('#form_document').attr('action',"{{route('employeedocument.store')}}");
		$('#form_document input[name=_method]').attr('value','POST');
		$('#form_document input[name=employee_id]').attr('value',{{ $employee->id }});
		$('#form_document select[name=category]').select2('val','');
		$('#form_document input[name=name]').attr('value','');
		$('#form_document input[name=file]').attr('value','');
		$('#form_document textarea[name=description]').html('');
		$('#form_document .invalid-feedback').each(function () { $(this).remove(); });
		$('#form_document .form-group').removeClass('has-error').removeClass('has-success');
		$('#add_document .modal-title').html('Add Document');
		$('#document-preview').html('').attr('data-url','');
		$('#add_document').modal('show');
	});
	$(document).on('click','.editdocument',function(){
		var id = $(this).data('id');
		$.ajax({
			url:`{{url('admin/employeedocument')}}/${id}/edit`,
			method:'GET',
			dataType:'json',
			beforeSend:function(){
				$('#box-menu .overlay').removeClass('d-none');
			},
		}).done(function(response){
			$('#box-menu .overlay').addClass('d-none');
			if(response.status){
				$('#add_document .modal-title').html('{{ __('general.edt') }} Document');
				$('#add_document').modal('show');
				$('#form_document')[0].reset();
				$('#form_document .invalid-feedback').each(function () { $(this).remove(); });
				$('#form_document .form-group').removeClass('has-error').removeClass('has-success');
				$('#form_document input[name=_method]').attr('value','PUT');
				$('#form_document input[name=employee_id]').attr('value',{{$employee->id}});
				$('#form_document input[name=name]').attr('value',response.data.name);
				$('#form_document input[name=file]').attr('value',response.data.file);
				$('#form_document select[name=category]').select2('val',response.data.category);
				$('#form_document textarea[name=description]').html(response.data.description);
				$('#document-preview').html(response.data.file).attr('data-url',response.data.link);
				$('#form_document').attr('action',`{{url('admin/employeedocument/')}}/${response.data.id}`);
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
		title:'Menghapus Data Dokumen?',
		message:'Data yang telah dihapus tidak dapat dikembalikan',
		callback: function(result) {
			if(result) {
				var data = {
								_token: "{{ csrf_token() }}",
								id: id
							};
				$.ajax({
				url: `{{url('admin/employeedocument')}}/${id}`,
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
						dataTableDocument.ajax.reload( null, false );
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
		allowedFileExtensions: ["png", "jpg", "jpeg", "pdf"],
		dropZoneEnabled: false,
		initialPreviewAsData: false,
		initialPreviewFileType: 'image',
		theme:'explorer-fas'
	});
	// End Document

	// Salary
	dataTableSalary = $('#table-salary').DataTable( {
		stateSave:false,
		processing: true,
		serverSide: true,
		filter:false,
		info:false,
		lengthChange:false,
		responsive: true,
		order: [[ 4, "desc" ]],
		ajax: {
			url: "{{ route('salaryemployee.read') }}",
			type: "GET",
			data:function(data){
				var name = $('#form-search').find('input[name=name]').val();
				data.name = name;
				data.employee_id = {{$employee->id}};
			}
		},
		columnDefs:[
			{
				orderable: false,targets:[0,1,2,3]
			},
			{ className: "text-right", targets: [0] },
			{ className: "text-center", targets: [0] }
		],
		columns: [
			{ data: "no" },
			{ data: "amount", render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' )},
			{ data: "description" },
			{ data: "user_name" },
			{ data: "updated_at" }
		]
	});
	$('.add_salary').on('click',function(){
		var a = $('#form_salary input[name=userser]').val();
		$('#form_salary')[0].reset();
		$('#form_salary').attr('action',"{{ route('salaryemployee.store') }}");
		$('#form_salary input[name=_method]').attr('value','POST');
		$('#form_salary input[name=employee_id]').attr('value',{{ $employee->id }});
		$('#form_salary input[name=amount]').attr('value','');
		$('#form_salary input[name=description]').attr('value','');
		$('#form_salary input[name=user_id]').attr('value', a);
		$('#form_salary .invalid-feedback').each(function () { $(this).remove(); });
		$('#form_salary .form-group').removeClass('has-error').removeClass('has-success');
		$('#add_salary .modal-title').html('Add Salary');
		$('#add_salary').modal('show');
	});
	$(document).on('click','.editsalary',function(){
		var id = $(this).data('id');
		$.ajax({
			url:`{{url('admin/salaryemployee')}}/${id}/edit`,
			method:'GET',
			dataType:'json',
			beforeSend:function(){
				$('#box-menu .overlay').removeClass('d-none');
			},
		}).done(function(response){
			$('#box-menu .overlay').addClass('d-none');
			if(response.status){
				$('#add_salary .modal-title').html('{{ __('general.edt') }} Salary');
				$('#add_salary').modal('show');
				$('#form_salary')[0].reset();
				$('#form_salary .invalid-feedback').each(function () { $(this).remove(); });
				$('#form_salary .form-group').removeClass('has-error').removeClass('has-success');
				$('#form_salary input[name=_method]').attr('value','PUT');
				$('#form_salary input[name=employee_id]').attr('value',{{$employee->id}});
				$('#form_salary input[name=amount]').attr('value',response.data.amount);
				$('#form_salary input[name=description]').attr('value',response.data.description);
				$('#form_salary').attr('action',`{{url('admin/salaryemployee/')}}/${response.data.id}`);
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
	$(document).on('click','.deletesalary',function(){
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
		title:'Menghapus Data Salary?',
		message:'Data yang telah dihapus tidak dapat dikembalikan',
		callback: function(result) {
			if(result) {
			var data = {
							_token: "{{ csrf_token() }}",
							id: id
						};
			$.ajax({
				url: `{{url('admin/salaryemployee')}}/${id}`,
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
					dataTableDocument.ajax.reload( null, false );
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
	// End Salary
  dataTableGross = $('#gross-table').DataTable({
    stateSave:true,
    processing:true,
    serverSide:true,
    filter:false,
    info:false,
    lengthChange:false,
    paging: false,
    responsive:true,
    ordering: false,
    ajax: {
      url: "{{route('salaryreportdetail.employeegross')}}",
      type: "GET",
      data:function(data){
       data.employee_id = {{$employee->id}};
	   data.month = $('select[name=month-gross]').val();
	   data.year = $('select[name=year-gross]').val();
      }
    },
    columnDefs:[
      { orderable: false,targets:[0,1,2] },
      { className: "text-right", targets: [2] },
      { render: function(data, type, row) {
        var total = $.fn.dataTable.render.number( '.', ',', 0, ' Rp. ' ).display(data);
        if (row.description == 'Potongan absen') {
          return `<span class="text-danger">( ${total} )</span>`;
        }
        return `${total}`;
      }, targets:[2]}
    ],
    columns: [
      { data: "no" },
      { data: "description" },
      { data: "total"}
    ],
    footerCallback: function(row, data, start, end, display) {
      var api = this.api(), data;

      var intVal = function ( i ) {
          return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
      };

      total = api.column( 2 ).data().reduce( function (a, b) { return intVal(a) + intVal(b);}, 0 );

      pageTotal = api.column( 2, { page: 'current'} ).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      var numFormat = $.fn.dataTable.render.number('.', ',', 0, 'Rp. ' ).display;

      $( api.column( 2 ).footer() ).html(numFormat(total));
      $('#gross').attr('data-gross', total);
    }
});
	// Allowance
	$("#allowance_id").select2({
		ajax: {
				url: "{{route('allowance.select')}}",
				type: 'GET',
				dataType: 'json',
				data: function (term, page) {
						return {
								name: term,
								page: page,
								limit: 30,
						};
				},
				results: function (data, page) {
						var more = (page * 30) < data.total;
						var option = [];
						$.each(data.rows, function (index, item) {
								option.push({
										id: item.id,
										text: `${item.allowance}`
								});
						});
						return {
								results: option,
								more: more,
						};
				},
		},
		allowClear: true,
	});
	$(document).on("change", "#allowance_id", function () {
			if (!$.isEmptyObject($('#form').validate().submitted)) {
					$('#form').validate().form();
			}
	});

	$("#form_allowance").validate({
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
		$.ajax({
			url:$('#form_allowance').attr('action'),
			method:'post',
			data: new FormData($('#form_allowance')[0]),
			processData: false,
			contentType: false,
			dataType: 'json',
			beforeSend:function(){
			$('.overlay').removeClass('d-none');
			}
		}).done(function(response){
				$('.overlay').addClass('d-none');
				if(response.status){
				$('#add_allowance').modal('hide');
				dataTableAllowance.draw();
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
		})
		}
	});

	$("#form_allowance_data").validate({
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
		$.ajax({
			url:$('#form_allowance_data').attr('action'),
			method:'post',
			data: new FormData($('#form_allowance_data')[0]),
			processData: false,
			contentType: false,
			dataType: 'json',
			beforeSend:function(){
			$('.overlay').removeClass('d-none');
			}
		}).done(function(response){
				$('.overlay').addClass('d-none');
				if(response.status){
				$('#add_allowance_data').modal('hide');
				dataTableAllowance.draw();
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
		})
		}
	});

	$("#form_overtime").validate({
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
		$.ajax({
			url:$('#form_overtime').attr('action'),
			method:'post',
			data: new FormData($('#form_overtime')[0]),
			processData: false,
			contentType: false,
			dataType: 'json',
			beforeSend:function(){
			$('.overlay').removeClass('d-none');
			}
		}).done(function(response){
				$('.overlay').addClass('d-none');
				if(response.status){
				$('#editovertime').modal('hide');
				dataTableOvertime.draw();
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
		})
		}
	});

	$('.add_allowance_data').on('click',function(){
		$('#form_allowance_data')[0].reset();
		$('#form_allowance_data').attr('action',"{{ route('employeeallowance.store') }}");
		$('#form_allowance_data input[name=_method]').attr('value','POST');
		$('#form_allowance_data input[name=employee_id]').attr('value',{{$employee->id}});
		$('#form_allowance_data input[name=allowance_id]').select2('val','');
		$('#form_allowance_data input[name=month]').attr('value',$('#vert-tabs-profile select[name=montly]').val());
		$('#form_allowance_data input[name=year]').attr('value',$('#vert-tabs-profile select[name=year]').val());
		$('#form_allowance_data .invalid-feedback').each(function () { $(this).remove(); });
		$('#form_allowance_data .form-group').removeClass('has-error').removeClass('has-success');
		$('#add_allowance_data .modal-title').html('Add Allowance');
		$('#add_allowance_data').modal('show');
	});

	dataTableAllowance = $('#table-allowance').DataTable( {
		stateSave:false,
		processing: true,
		serverSide: true,
		filter:false,
		info:false,
		lengthChange:false,
		"paging" : false,
		responsive: true,
		order: [[ 3, "asc" ]],
		ajax: {
			url: "{{ route('employeeallowance.read') }}",
			type: "GET",
			data:function(data){
				var name = $('#form-search').find('input[name=name]').val();
				data.name = name;
				data.employee_id = {{$employee->id}};
				data.montly = $('#montly').val();
				data.year = $('select[name=year]').val();
			}
		},
		columnDefs:[
			{
				orderable: false,targets:[0,1,2,3,4,5,6,7,8]
			},
			{ className: "text-right", targets: [0] },
			{ className: "text-center", targets: [6,7,8] },
			{ render: function ( data, type, row ) {
				if((row.year && row.month) != null){
				const d = new Date(row.year, parseInt(row.month) -1, 1);
				const ye = new Intl.DateTimeFormat('en', { year: 'numeric' }).format(d);
				const mo = new Intl.DateTimeFormat('en', { month: 'long' }).format(d);
				return `${mo}-${ye}`
				}else{
					return "-";
				}
			},
			targets: [1] },
			{ render: function ( data, type, row ) {
				if(row.type == 'nominal'){
					return 'Rp. ' + row.value
				}else if(row.type == 'percentage'){
					return row.value + '%'
				}else{
					return row.value
				}
			},
			targets: [6] },
			{ render: function ( data, type, row ) {
				var html = '<input type="checkbox"';
				if (data == 1) {
				html += ` checked class="updateallowance" name="default" value="${row.id}">`;
				} else {
				html += ` class="updateallowance" name="default" value="${row.id}">`;
				}
				return html
			},
			targets: [7] },
			@if($employee->workgroup->penalty == 'Gross')
			{ render: function ( data, type, row ) {
				var html = '<input type="checkbox"';
				if (data == 1) {
				html += ` checked class="updatepenalty" name="penalty" value="${row.id}">`;
				} else {
				html += ` class="updatepenalty" name="penalty" value="${row.id}">`;
				}
				return html
			},
			targets: [8] },
			@endif
			{ render: function ( data, type, row ) {
				return `<div class="dropdown">
				<button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
				<i class="fa fa-bars"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right">
				<li><a class="dropdown-item editallowance" href="#" data-id="${row.id}"><i class="fas fa-pencil-alt mr-2"></i> {{ __('general.edt') }}</a></li>
				<li><a class="dropdown-item viewallowance" href="#" data-id="${row.id}" data-allowance="${row.allowance_id}"><i class="fas fa-info mr-3"></i> Detail</a></li>
				</ul></div>`
				},
			targets: [@if($employee->workgroup->penalty == 'Gross') 9 @endif @if($employee->workgroup->penalty == 'Basic') 8 @endif] }
		],
		columns: [
			{ data: "no" },
			{ data: "year" },
			{ data: "allowance" },
			{ data: "category" },
			{ data: "reccurance" },
			{ data: "factor" },
			{ data: "value" },
			{ data: "status" },
			@if($employee->workgroup->penalty == 'Gross')
			{ data: "is_penalty" },
			@endif
			{ data: "id" },
		]
	});

	$(document).on('click','.viewallowance',function(){
		$('#allowance-id-history').attr('value', $(this).data('allowance'));
		dataTableHistory.draw();
		$('#view_allowance').modal('show');
	})

	dataTableHistory = $('#table-history').DataTable( {
		stateSave:true,
		processing: true,
		serverSide: true,
		filter:false,
		info:false,
		lengthChange:false,
		responsive: true,
		order: [[ 1, "asc" ]],
		ajax: {
			url: "{{ route('employeedetailallowance.readdetail') }}",
			type: "GET",
			data:function(data){
				var name = $('#form-search').find('input[name=name]').val();
				var allowance = $('#allowance-id-history').val();
				data.name = name;
				data.employee_id = {{$employee->id}};
				data.allowance = allowance;
				data.montly = $('#montly').val();
				data.year = $('select[name=year]').val();
			}
		},
		columnDefs:[
			{
				orderable: false,targets:[0,2,3]
			},
			{ className: "text-right", targets: [0] },
			{ className: "text-center", targets: [0] }
		],
		columns: [
			{ data: "no" },
			{ data: "tanggal_masuk" },
			{ data: "allowance"},
			{ data: "value" }
		]
	});

	$(document).on('click','.editallowance',function(){
		var id = $(this).data('id');
		$.ajax({
			url:`{{url('admin/employeeallowance')}}/${id}/edit`,
			method:'GET',
			dataType:'json',
			beforeSend:function(){
				$('#box-menu .overlay').removeClass('d-none');
			},
		}).done(function(response){
			$('#box-menu .overlay').addClass('d-none');
			if(response.status){
				$('#add_allowance .modal-title').html('{{ __('general.edt') }} Allowance');
				$('#add_allowance').modal('show');
				$('#form_allowance')[0].reset();
				$('#form_allowance .invalid-feedback').each(function () { $(this).remove(); });
				$('#form_allowance .form-group').removeClass('has-error').removeClass('has-success');
				$('#form_allowance input[name=_method]').attr('value','PUT');
				$('#form_allowance input[name=employee_id]').attr('value',{{$employee->id}});
				$('#form_allowance #type').select2('data',{id:response.data.type, text:response.data.type});
				$('#form_allowance input[name=value]').attr('value',response.data.value);
				$('#form_allowance input[name=factor]').attr('value',response.data.factor);
				$('#form_allowance input[name=get_allowance_id]').attr('value',response.data.allowance_id);
				$('#form_allowance').attr('action',`{{url('admin/employeeallowance/')}}/${response.data.id}`);
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

	$(document).on('change', '#type', function(){
		var value = $(this).val();
		$('#value').val(0);
		switch(value){
			case 'automatic':
			$('#value').val("Automatic");
			$('#value').attr('readonly', true);
			break;
			default:
			$('#value').attr('readonly', false);
			$('#value').removeAttr('max');
			$('.invalid-feedback').addClass('d-none');
			break;
			case 'percentage':
			$('#value').attr('readonly', false);
			$('#value').attr('max', 100);
			break;
		}
	});

	$(document).on('change','.updateallowance', function () {
		$.ajax({
			url: "{{url('admin/employees/update_allowances')}}",
			data: {
				_token: "{{ csrf_token() }}",
				id: this.value,
				status: this.checked ? 1 : 0,
				_method: 'PUT'
			},
			type: 'POST',
			dataType: 'json',
			beforeSend: function () {
				$('#workgroup_allowance .overlay').removeClass('d-none');
			}
		}).done(function (response) {
			$('#workgroup_allowance .overlay').addClass('d-none');
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

		}).fail(function (response) {
			var response = response.responseJSON;
			$('#workgroup_allowance  .overlay').addClass('d-none');
			$.gritter.add({
				title: 'Error!',
				text: response.message,
				class_name: 'gritter-error',
				time: 1000,
			});
		})
	});

	$(document).on('change', '.updatepenalty', function () {
		$.ajax({
			url: "{{ url('admin/employees/update_penalty') }}",
			data: {
				_token: "{{ csrf_token() }}",
				id: this.value,
				penalty: this.checked ? 1 : 0,
				_method: 'PUT',
			},
			type: 'POST',
			dataType: 'json',
			beforeSend: function () {
				$("#workgroup_allowance .overlay").removeClass("d-none");
			}
		}).done(function (response) {
			$("#workgroup_allowance .overlay").addClass("d-none");
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
		}).fail(function (response) {
			var response = response.responsJSON;
			$("#workgroup_allowance .overlay").addClass('d-none');
			$.gritter.add({
				title: 'Error!',
				text: response.message,
				class_name: 'gritter-error',
				time: 1000,
			});
		});
	});
	// End Allowance

	// Driver Allowance
	dataTableDriver = $('#table-driver-allowance').DataTable( {
		stateSave:true,
		processing: true,
		serverSide: true,
		filter:false,
		info:false,
		lengthChange:true,
		responsive: true,
		order: [[ 1, "asc" ]],
		ajax: {
			url: "{{ route('driverallowancelist.read') }}",
			type: "GET",
			data:function(data){
				data.employee_id = {{$employee->id}};
				data.month = $('#month-driver').val();
				data.year = $('#year-driver').val();
			}
		},
		columnDefs:[
			{
				orderable: false,targets:[0,1,2,3,4,5]
			},
			{ className: "text-right", targets: [0,5] },
			{ className: "text-center", targets: [2,3,4] },
			// { render: function(data, type, row) {
			// 	if (row.truck == 'fuso') {
			// 		return 'Fuso'
			// 	} else {
			// 		return 'Colt Diesel'
			// 	}
			// }, targets:[2]},
			{ render: function ( data, type, row ) {
				return row.rit + '%'
				
			},
			targets: [4] },
			{ render: function(data, type, row) {
				return `${row.date}<br><small> Type Kendaraan ${row.truck}</small>`;
			}, targets:[1]},
			// { render: function ( data, type, row ) {
			// 	return `<div class="dropdown">
			// 		<button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
			// 		<i class="fa fa-bars"></i>
			// 		</button>
			// 		<ul class="dropdown-menu dropdown-menu-right">
			// 			<li><a class="dropdown-item detaildriver" href="#" data-date="${row.date}" data-driver="${row.driver_id}" data-truck="${row.truck}" data-group="${row.rule}"><i class="fas fa-search mr-2"></i> Detail</a></li>
			// 		</ul>
			// 		</div>`
			// },targets: [6]
			// }
		],
		columns: [
			{ data: "no" },
			{ data: "date"},
			{ data: "group" },
			{ data: "value", render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' ) },
			{ data: "group"},
			{ data: "total_value", render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' )}
			// { data: "id" },

		],
		footerCallback: function(row, data, start, end, display, grand_total) {
			var api = this.api(), data;
			console.log(grand_total);

			var intVal = function ( i ) {
					return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
			};

			total = api.column( 5 ).data().reduce( function (a, b) { return intVal(a) + intVal(b);}, 0 );

			pageTotal = api.column( 5, { page: 'current'} ).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
			var numFormat = $.fn.dataTable.render.number('.', ',', 0, 'Rp. ' ).display;

			$( api.column( 5 ).footer() ).html(numFormat(total));
		}
	});
	$(document).on('click','.detaildriver',function(){
		// $('#allowance-id-history').attr('value', $(this).data('allowance'));
		// dataTableHistory.draw();
		$('#view-driver-allowance').modal('show');
		var group = $(this).data('group');
		var date = $(this).data("date");
		var truck = $(this).data("truck");
		var driver = $(this).data('driver');
		// console.log(group);
		dataTableDriverDetail = $('#table-view-driver-allowance').DataTable({
			stateSave:true,
			processing: true,
			serverSide: true,
			filter:false,
			info:false,
			destroy: true,
			lengthChange:true,
			responsive: true,
			order: [[ 1, "asc" ]],
			ajax: {
				url: "{{ route('driverallowancelist.read_detail') }}",
				type: "GET",
				data:function(data){
					// data.date = date;
					data.driver = driver;
					// data.truck = truck;
					// data.group = group;
					data.month = $('#month-driver').val();
					data.year = $('#year-driver').val();
				}
			},
			columnDefs:[
				{
					orderable: false,targets:[0]
				},
				{ className: "text-right", targets: [0] },
				{ className: "text-center", targets: [3] },
			],
			columns: [
				{ data: "no" },
				{ data: "departure_date" },
				{ data: "arrived_date" },
				{ data: "police_no" }
				// { data: "customer" }

			],
			footerCallback: function(row, data, start, end, display, grand_total) {
				var api = this.api(), data;
				console.log(grand_total);

				var intVal = function ( i ) {
						return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
				};

				total = api.column( 4 ).data().reduce( function (a, b) { return intVal(a) + intVal(b);}, 0 );

				pageTotal = api.column( 4, { page: 'current'} ).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
				var numFormat = $.fn.dataTable.render.number('.', ',', 0, 'Rp. ' ).display;

				$( api.column( 4 ).footer() ).html(numFormat(total));
			}
		});
	});
	// End Driver Allowance

	//Overtime
	dataTableOvertime = $('#table-overtime').DataTable( {
		stateSave:false,
		processing: true,
		serverSide: true,
		filter:false,
		info:false,
		paginate: false,
		lengthChange:false,
		responsive: true,
		order: [[ 1, "asc" ]],
		ajax: {
			url: "{{ route('overtime.read_overtime') }}",
			type: "GET",
			data:function(data){
				data.employee_id = {{$employee->id}};
				data.montly = $('#montly_overtime').val();
				data.year = $('#year_overtime').val();
			}
		},
		columnDefs:[
			{
				orderable: false,targets:[0,1,2,3,4,5,6,7]
			},
			{ className: "text-right", targets: [0,5,6] },
			{ className: "text-center", targets: [6,7] },
			{ render: function(data, type, row) {
				return `${row.date} - ${row.day}`;
			}, targets:[1]},
			{ render: function ( data, type, row ) {
				return `<div class="dropdown">
					<button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-bars"></i>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
						<li><a class="dropdown-item editovertime" href="#" data-id="${row.id}"><i class="fas fa-pencil-alt mr-2"></i> {{ __('general.edt') }}</a></li>
					</ul>
					</div>`
			},targets: [7]
			}
		],
		columns: [
			{ data: "no" },
			{ data: "date" },
			{ data: "scheme_rule" },
			{ data: "hour" },
			{ data: "amount" },
			{ data: "basic_salary", render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' )},
			{ data: "final_salary", render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' )},
			{ data: "id"}

		],
		footerCallback: function(row, data, start, end, display, grand_total) {
			var api = this.api(), data;
			console.log(grand_total);

			var intVal = function ( i ) {
					return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
			};

			total = api.column( 6 ).data().reduce( function (a, b) { return intVal(a) + intVal(b);}, 0 );

			pageTotal = api.column( 6, { page: 'current'} ).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
			var numFormat = $.fn.dataTable.render.number('.', ',', 0, 'Rp. ' ).display;

			$( api.column( 6 ).footer() ).html(numFormat(total));
		}
	});
	$(document).on('click','.editovertime',function(){
		var id = $(this).data('id');
		// $('#editovertime').modal('show');
		$.ajax({
			url:`{{url('admin/overtime')}}/${id}/edit`,
			method:'GET',
			dataType:'json',
			beforeSend:function(){
				$('#box-menu .overlay').removeClass('d-none');
			},
		}).done(function(response){
			$('#box-menu .overlay').addClass('d-none');
			// if(response.status){
				$('#editovertime .modal-title').html('{{ __('general.edt') }} Overtime');
				$('#editovertime').modal('show');
				$('#form_overtime')[0].reset();
				$('#form_overtime .invalid-feedback').each(function () { $(this).remove(); });
				$('#form_overtime .form-group').removeClass('has-error').removeClass('has-success');
				$('#form_overtime input[name=_method]').attr('value','PUT');
				$('#form_overtime input[name=employee_id]').attr('value',{{$employee->id}});
				$('#form_overtime input[name=hour]').attr('value',response.data.hour);
				$('#form_overtime').attr('action',`{{url('admin/overtime/')}}/${response.data.id}`);
			// }          
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
	//End Overtime

	//Penalty
	dataTablePenalty = $('#table-penalty').DataTable( {
		stateSave:false,
		processing: true,
		serverSide: true,
		filter:false,
		info:false,
		paginate: false,
		lengthChange:false,
		responsive: true,
		order: [[ 1, "asc" ]],
		ajax: {
			url: "{{ route('penalty.read') }}",
			type: "GET",
			data:function(data){
				data.employee_id = {{$employee->id}};
				data.montly = $('#montly_penalty').val();
				data.year = $('#year_penalty').val();
			}
		},
		columnDefs:[
			{
				orderable: false,targets:[0]
			},
			{ className: "text-right", targets: [3,4	] },
			{ className: "text-center", targets: [5] },
			{ render: function ( data, type, row ) {
				return `<div class="dropdown">
					<button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-bars"></i>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
						<li><a class="dropdown-item" href="../../leavereport/${row.leave_id}/detail"><i class="fas fa-pencil-alt mr-2"></i> Detail</a></li>
					</ul>
					</div>`
			},targets: [5]
			}
		],
		columns: [
			{ data: "no" },
			{ data: "date" },
			{ data: "leave_type" },
			{ data: "salary", render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' )},
			{ data: "penalty", render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' )},
			{ data: "leave_id"}

		],
		footerCallback: function(row, data, start, end, display, grand_total) {
			var api = this.api(), data;
			console.log(grand_total);

			var intVal = function ( i ) {
					return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
			};

			total = api.column( 4 ).data().reduce( function (a, b) { return intVal(a) + intVal(b);}, 0 );

			pageTotal = api.column( 4, { page: 'current'} ).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
			var numFormat = $.fn.dataTable.render.number('.', ',', 0, 'Rp. ' ).display;

			$( api.column( 4 ).footer() ).html(numFormat(total));
		}
	});
	// $(document).on('click','.editovertime',function(){
	// 	var id = $(this).data('id');
	// 	// $('#editovertime').modal('show');
	// 	$.ajax({
	// 		url:`{{url('admin/overtime')}}/${id}/edit`,
	// 		method:'GET',
	// 		dataType:'json',
	// 		beforeSend:function(){
	// 			$('#box-menu .overlay').removeClass('d-none');
	// 		},
	// 	}).done(function(response){
	// 		$('#box-menu .overlay').addClass('d-none');
	// 		// if(response.status){
	// 			$('#editovertime .modal-title').html('Edit Overtime');
	// 			$('#editovertime').modal('show');
	// 			$('#form_overtime')[0].reset();
	// 			$('#form_overtime .invalid-feedback').each(function () { $(this).remove(); });
	// 			$('#form_overtime .form-group').removeClass('has-error').removeClass('has-success');
	// 			$('#form_overtime input[name=_method]').attr('value','PUT');
	// 			$('#form_overtime input[name=employee_id]').attr('value',{{$employee->id}});
	// 			$('#form_overtime input[name=hour]').attr('value',response.data.hour);
	// 			$('#form_overtime').attr('action',`{{url('admin/overtime/')}}/${response.data.id}`);
	// 		// }          
	// 	}).fail(function(response){
	// 		var response = response.responseJSON;
	// 		$('#box-menu .overlay').addClass('d-none');
	// 		$.gritter.add({
	// 			title: 'Error!',
	// 			text: response.message,
	// 			class_name: 'gritter-error',
	// 			time: 1000,
	// 		});
	// 	})	
	// });
	//End Penalty

	// Leave Name
	$("#leave_name").select2({
		ajax: {
				url: "{{route('selectleave.select')}}",
				type: 'GET',
				dataType: 'json',
				data: function (term, page) {
						return {
								name: term,
								page: page,
								limit: 30,
						};
				},
				results: function (data, page) {
						var more = (page * 30) < data.total;
						var option = [];
						$.each(data.rows, function (index, item) {
								option.push({
										id: item.id,
										text: `${item.leave_name}`,
										used:item.used_balance,
										quota:item.balance,
										balance:item.remaining_balance
								});
						});
						return {
								results: option,
								more: more,
						};
				},
		},
		allowClear: true,
	});
	$(document).on("change", "#leave_name", function () {
		var used = $('#leave_name').select2('data').used;
		var quota = $('#leave_name').select2('data').quota;
		var balance = $('#leave_name').select2('data').balance;
		
		$('#leave_used').val(used);
		$('#leave_quota').val(quota);
		$('#leave_balance').val(balance);
	});
	// End Leave Name
	$(document).on("change", "#year_leave", function () {
		dataTableLeave.draw();
	});
	//Leave
	dataTableLeave = $('#table-leave').DataTable( {
		processing: true,
		serverSide: true,
		filter:false,
		info:false,
		lengthChange:true,
		responsive: true,
		order: [[ 1, "asc" ]],
		ajax: {
			url: "{{ route('employees.leave') }}",
			type: "GET",
			data:function(data){
				data.employee_id = {{$employee->id}};
				data.year = $('#year_leave').val();
			}
		},
		columnDefs:[
			{
				orderable: false,targets:[0,1,2,3,4,7]
			},
			{ className: "text-right", targets: [0] },
			{ className: "text-center", targets: [5,6,7] },
			{ render: function(data, type, row) {
				if (row.balance == -1) {
					return `∞`;
				} else {
					return `${row.balance}`;
				}
			}, targets:[2]},
			{ render: function(data, type, row) {
				if (row.balance == -1) {
					return `∞`;
				} else {
					return `${row.remaining_balance}`;
				}
			}, targets:[4]},
			{ render: function(data, type, row) {
					return `${row.from_balance} s/d ${row.to_balance}`;
			}, targets:[6]},
			{ render: function ( data, type, row ) {
				return `<div class="dropdown">
					<button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-bars"></i>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
						<li><a class="dropdown-item detailleave" href="#" data-leavesetting_id="${row.leavesetting_id}" data-leave_name="${row.leave_name}"><i class="fas fa-search mr-2"></i> Detail</a></li>
					</ul>
					</div>`
			},targets: [7]}
		],
		columns: [
			{ data: "no" },
			{ data: "leave_name" },
			{ data: "balance", className: "editbalance text-center" },
			{ data: "used_balance" },
			{ data: "remaining_balance" },
			{ data: "over_balance" },
			{ data: "year_balance" },
			{ data: "leavesetting_id" }
		]
	});
	dataTableDetailLeave = $('#table-view-detail-leave').DataTable( {
		processing: true,
		serverSide: true,
		filter:false,
		info:false,
		lengthChange:true,
		responsive: true,
		order: [[ 1, "asc" ]],
		ajax: {
			url: "{{ route('employees.leavedetail') }}",
			type: "GET",
			data:function(data){
				data.employee_id = {{$employee->id}};
				data.leavesetting_id = $('#leavesetting_id').val();
			}
		},
		columnDefs:[
			{
				orderable: false,targets:[0]
			},
			{ className: "text-right", targets: [0] },
			{ className: "text-center", targets: [1,2,3] },
			{ render: function(data, type, row) {
				if (row.type == 'fullday') {
					return `Fullday`;
				} else {
					return `Hour`;
				}
			}, targets:[2]},
			{ render: function(data, type, row) {
				if (row.status == -1) {
					return `<span class="badge badge-secondary">Draft</span>`;
				} else if (row.status == 0) {
					return `<span class="badge badge-warning">Waiting Approval</span>`;
				} else if (row.status == 1) {
					return `<span class="badge badge-success">Approved</span>`;
				} else {
					return `<span class="badge badge-danger">Reject</span>`;
				}
			}, targets:[3]}
		],
		columns: [
			{ data: "no" },
			{ data: "date" },
			{ data: "type" },
			{ data: "status" }
		]
	});
	$(document).on('click','.detailleave',function(){
		$('#leavesetting_id').attr('value', $(this).data('leavesetting_id'));
		var leave_name = $(this).data('leave_name');
		dataTableDetailLeave.draw();
		$('#view-detail-leave').modal('show');
		$('#view-detail-leave-title').text('View Detail Leave ' + leave_name);
	});

	$('#table-leave').on('click','.editbalance',function(){
		var data = dataTableLeave.row(this).data();
		if (data) {
			$('#edit-leave-balance').modal('show');
			$('#balance_leavesetting_id').val(data.id);
			$('#leave_balance').val(data.balance);
		}
	});
	$("#form-edit-balance").validate({
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
				url:$('#form-edit-balance').attr('action'),
				method:'post',
				data: new FormData($('#form-edit-balance')[0]),
				processData: false,
				contentType: false,
				dataType: 'json',
				beforeSend:function(){
						$('.overlay').removeClass('d-none');
				}
			}).done(function(response){
						$('.overlay').addClass('d-none');
						if(response.status){
							dataTableLeave.draw();
							$('#leave_balance').val('');
							$('#edit-leave-balance').modal('hide');
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
	$('.add_leave').on('click',function(){
		var a = $('#form_leave input[name=userser]').val();
		$('#form_leave')[0].reset();
		$('#form_leave').attr('action',"#");
		$('#form_leave input[name=_method]').attr('value','POST');
		$('#form_leave input[name=employee_id]').attr('value',{{ $employee->id }});
		$('#form_leave input[name=amount]').attr('value','');
		$('#form_leave input[name=description]').attr('value','');
		$('#form_leave input[name=user_id]').attr('value', a);
		$('#form_leave .invalid-feedback').each(function () { $(this).remove(); });
		$('#form_leave .form-group').removeClass('has-error').removeClass('has-success');
		$('#add_leave .modal-title').html('Add Salary');
		$('#add_leave').modal('show');
	});
	$(document).on('click','.editsalary',function(){
		var id = $(this).data('id');
		$.ajax({
			url:`{{url('admin/salaryemployee')}}/${id}/edit`,
			method:'GET',
			dataType:'json',
			beforeSend:function(){
				$('#box-menu .overlay').removeClass('d-none');
			},
		}).done(function(response){
			$('#box-menu .overlay').addClass('d-none');
			if(response.status){
				$('#add_leave .modal-title').html('Edit Salary');
				$('#add_leave').modal('show');
				$('#form_leave')[0].reset();
				$('#form_leave .invalid-feedback').each(function () { $(this).remove(); });
				$('#form_leave .form-group').removeClass('has-error').removeClass('has-success');
				$('#form_leave input[name=_method]').attr('value','PUT');
				$('#form_leave input[name=employee_id]').attr('value',{{$employee->id}});
				$('#form_leave input[name=amount]').attr('value',response.data.amount);
				$('#form_leave input[name=description]').attr('value',response.data.description);
				$('#form_leave').attr('action',`{{url('admin/leaveemployee/')}}/${response.data.id}`);
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
	$(document).on('click','.deleteleave',function(){
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
		title:'Menghapus Data Salary?',
		message:'Data yang telah dihapus tidak dapat dikembalikan',
		callback: function(result) {
			if(result) {
			var data = {
							_token: "{{ csrf_token() }}",
							id: id
						};
			$.ajax({
				url: `{{url('admin/leaveemployee')}}/${id}`,
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
					dataTableDocument.ajax.reload( null, false );
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
	// End Leave
});
</script>
@endpush