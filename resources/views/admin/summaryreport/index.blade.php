@extends('admin.layouts.app')

@section('title', 'Summary Report')
@section('stylesheets')
<link href="{{ asset('adminlte/component/dataTables/css/datatables.min.css') }}" rel="stylesheet">
<style>
  .flex-work {
    width: 100%;
    height: 5px;
    background: #8A8B97;
    border-radius: 5px;
  }

  .flex-work::before {
    content: "";
    display: block;
    padding-top: 25%;
  }

  .flex-present {
    width: 100%;
    height: 5px;
    background: #2DB60B;
    border-radius: 5px;
  }

  .flex-present::before {
    content: "";
    display: block;
    padding-top: 25%;
  }

  .flex-holiday {
    width: 100%;
    height: 5px;
    background: #001AFF;
    border-radius: 5px;
  }

  .flex-holiday::before {
    content: "";
    display: block;
    padding-top: 25%;
  }

  .flex-absent {
    width: 100%;
    height: 5px;
    background: #FF0000;
    border-radius: 5px;
  }

  .flex-absent::before {
    content: "";
    display: block;
    padding-top: 25%;
  }

  .flex-duty {
    width: 100%;
    height: 5px;
    background: #00FFFF;
    border-radius: 5px;
  }

  .flex-duty::before {
    content: "";
    display: block;
    padding-top: 25%;
  }

  .flex-paid {
    width: 100%;
    height: 5px;
    background: #FF00E5;
    border-radius: 5px;
  }

  .flex-paid::before {
    content: "";
    display: block;
    padding-top: 25%;
  }

  .flex-unpaid {
    width: 100%;
    height: 5px;
    background: #FF9900;
    border-radius: 5px;
  }

  .flex-paid::before {
    content: "";
    display: block;
    padding-top: 25%;
  }
</style>
@endsection

@push('breadcrump')
<li class="breadcrumb-item active">Summary Report</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        {{-- Title, Button Approve & Search --}}
        <div class="card-header">
          <h3 class="card-title">List Summary Report</h3>
          <div class="pull-right card-tools">
            <a href="#" onclick="filter()" class="btn btn-default btn-sm"><i class="fa fa-search"></i></a>
          </div>
        </div>
        {{-- .Title, Button Approve & Search --}}
        <div class="card-body">
          <table class="table table-striped table-bordered datatable" style="width: 100%">
            <thead>
              <tr>
                <th width="10">#</th>
                <th width="10">Employee</th>
                <th width="10">Position</th>
                <th width="10">Sep 01<br>Sun</th>
                <th width="10">Sep 02<br>Mon</th>
                <th width="10">Sep 03<br>Tue</th>
                <th width="10">Sep 04<br>Wed</th>
                <th width="10">Sep 05<br>Thu</th>
                <th width="10">Sep 06<br>Fri</th>
                <th width="10">Sep 07<br>Sat</th>
                <th width="2">#</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Ester<br>01-EMP-0005</td>
                <td>Business Analys<br>IT Dept</td>
                <td class="text-center">
                  <h4 class="text-bold">W</h4>
                  <div class="flex-work"></div>
                </td>
                <td class="text-center">
                  <h4 class="text-bold">P</h4>
                  <div class="flex-present"></div>
                </td>
                <td class="text-center">
                  <h4 class="text-bold">P</h4>
                  <div class="flex-present"></div>
                </td>
                <td class="text-center">
                  <h4 class="text-bold">P</h4>
                  <div class="flex-present"></div>
                </td>
                <td class="text-center">
                  <h4 class="text-bold">P</h4>
                  <div class="flex-present"></div>
                </td>
                <td class="text-center">
                  <h4 class="text-bold">P</h4>
                  <div class="flex-present"></div>
                </td>
                <td class="text-center">
                  <h4 class="text-bold">W</h4>
                  <div class="flex-work"></div>
                </td>
                <td class="text-center">
                  <div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                      <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                      <li><a class="dropdown-item detail" href="#" data-id=""><i class="fas fa-search mr-2"></i>
                          Detail</a></li>
                    </ul>
                  </div>
                </td>
              </tr>
              <tr>
                <td>1</td>
                <td>Bagus Mertha P<br>01-EMP-0002</td>
                <td>IT Manager<br>IT Dept</td>
                <td class="text-center">
                  <h4 class="text-bold">W</h4>
                  <div class="flex-work"></div>
                </td>
                <td class="text-center">
                  <h4 class="text-bold">H</h4>
                  <div class="flex-holiday"></div>
                </td>
                <td class="text-center">
                  <h4 class="text-bold">OD</h4>
                  <div class="flex-duty"></div>
                </td>
                <td class="text-center">
                  <h4 class="text-bold">PL</h4>
                  <div class="flex-paid"></div>
                </td>
                <td class="text-center">
                  <h4 class="text-bold">A</h4>
                  <div class="flex-absent"></div>
                </td>
                <td class="text-center">
                  <h4 class="text-bold">P</h4>
                  <div class="flex-present"></div>
                </td>
                <td class="text-center">
                  <h4 class="text-bold">UL</h4>
                  <div class="flex-unpaid"></div>
                </td>
                <td class="text-center">
                  <div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                      <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                      <li><a class="dropdown-item detail" href="#" data-id=""><i class="fas fa-search mr-2"></i>
                          Detail</a></li>
                    </ul>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="card-footer">
          <div class="row">
            <div class="col-sm-6 row">
              <div class="col-sm-3">
                <h6><b>P</b> - Present</h6>
                <div class="flex-present w-25"></div>
              </div>
              <div class="col-sm-3">
                <h6><b>H</b> - Holiday</h6>
                <div class="flex-holiday w-25"></div>
              </div>
              <div class="col-sm-3">
                <h6><b>W</b> - Weekend</h6>
                <div class="flex-work w-25"></div>
              </div>
              <div class="col-sm-3">
                <h6><b>A</b> - Absent</h6>
                <div class="flex-absent w-25"></div>
              </div>
            </div>
            <div class="col-sm-6 row">
              <div class="col-sm-3">
                <h6><b>OD</b> - On Duty</h6>
                <div class="flex-duty w-25"></div>
              </div>
              <div class="col-sm-3">
                <h6><b>PL</b> - Paid Leave</h6>
                <div class="flex-paid w-25"></div>
              </div>
              <div class="col-sm-3">
                <h6><b>UL</b> - Unpaid Leave</h6>
                <div class="flex-unpaid w-25"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="add-filter" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Filter</h4>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body">
          <form id="form-search" autocomplete="off">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label" for="name">Employee Name</label>
                  <input type="text" name="name" class="form-control" placeholder="Employee Name">
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label" for="position">Position</label>
                  <input type="text" name="position" class="form-control" placeholder="Position">
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button form="form-search" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="Apply"><i
              class="fa fa-search"></i></button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script type="text/javascript">
  function filter(){
		$('#add-filter').modal('show');
	}
  // $(function() {
    //   dataTable = $('.datatable').DataTable({
    //     stateSave:true,
    //     processing: true,
    //     serverSide: true,
    //     filter:false,
    //     info:false,
    //     lengthChange:true,
    //     responsive: true,
    //     order: [[ 4, "asc" ]],
    //   })
    // })
</script>
@endpush