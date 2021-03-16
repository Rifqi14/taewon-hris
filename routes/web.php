<?php
ini_set('max_execution_time', 3600);
error_reporting(E_ALL ^ E_DEPRECATED);


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route Customer

use App\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});
Route::get('lang/{language}', 'LocalizationController@switch')->name('localization.switch');
Auth::routes();
Route::get('/overtimescheme/approved/{from}/{to}', 'Admin\CronController@generateOvertimeApprove')->name('overtime.approved');
Route::get('/overtimescheme/date', 'Admin\CronController@generateOvertimeScheme')->name('overtime.date');
Route::get('/leavepenalty/penalty', 'Admin\CronController@generateLeavePenalty')->name('leavepenalty.penalty');
Route::get('/employeesalary/updatecreated', 'Admin\CronController@generateCreatedDateEmployeeSalary')->name('employeesalary.updatecreated');
Route::get('/employeeallowance/generate', 'Admin\CronController@generate_allowance')->name('employeeallowance.generate');
Route::get('/employeeallowance/copy', 'Admin\CronController@copy_allowance')->name('employeeallowance.copy');
Route::get('/check/document', 'Admin\CronController@check_expired_document')->name('check.document');
Route::get('/check/contract', 'Admin\CronController@check_expired_contract')->name('check.contract');
Route::get('/check/reset_leave', 'Admin\CronController@check_reset_leave')->name('check.reset_leave');
//Route Admin
Route::group(['prefix' => 'admin'], function () {
    Route::get('/', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/login', 'Auth\AdminLoginController@login')->name('admin.login.post');
    Route::post('/logout', 'Auth\AdminLoginController@logout')->name('admin.logout');
    Route::group(['middleware' => 'auth:admin'], function () {
        // Route::get('/dashboard', function () {
        //     return view('admin.dashboard');
        // })->name('admin.home');
        // Route Dashboard
        Route::get('/dashboard', 'Admin\DashboardController@index')->name('admin.home');
        Route::get('/dashboard/read', 'Admin\DashboardController@readcontract')->name('dashboard.readcontract');
        Route::get('/dashboard/readdocument', 'Admin\DashboardController@readdocument')->name('dashboard.readdocument');
        Route::get('/dashboard/departmentdetail', 'Admin\DashboardController@departmentdetail')->name('dashboard.departmentdetail');
        //Route Province
        Route::get('/province/select', 'Admin\ProvinceController@select')->name('province.select');
        Route::get('/province/read', 'Admin\ProvinceController@read')->name('province.read');
        Route::resource('/province', 'Admin\ProvinceController')->except(['show']);
        //Route Region
        Route::get('/region/select', 'Admin\RegionController@select')->name('region.select');
        Route::get('/region/read', 'Admin\RegionController@read')->name('region.read');
        Route::resource('/region', 'Admin\RegionController')->except(['show']);
        //Route District
        Route::get('/district/select', 'Admin\DistrictController@select')->name('district.select');
        Route::get('/district/read', 'Admin\DistrictController@read')->name('district.read');
        Route::resource('/district', 'Admin\DistrictController')->except(['show']);
        //Route Expedition
        Route::resource('/expedition', 'Admin\ExpeditionController')->except(['show']);
        //Route Config
        Route::get('/config', 'Admin\ConfigController@index')->name('config.index');
        Route::put('/config', 'Admin\ConfigController@update')->name('config.update');
        //Route Site
        Route::get('/site/set/{id}', 'Admin\SiteController@set');
        Route::get('/site/read', 'Admin\SiteController@read')->name('site.read');
        Route::resource('/site', 'Admin\SiteController')->except(['show']);
        //Route User Site
        Route::get('/siteuser/read', 'Admin\SiteUserController@read')->name('siteuser.read');
        Route::get('/siteuser/select', 'Admin\SiteUserController@select')->name('siteuser.select');
        Route::post('/siteuser/store', 'Admin\SiteUserController@store')->name('siteuser.store');
        Route::delete('/siteuser/{id}', 'Admin\SiteUserController@destroy')->name('siteuser.destroy');
        //Route Menu
        Route::post('/menu/order', 'Admin\MenuController@order');
        Route::resource('/menu', 'Admin\MenuController')->only(['index', 'store', 'edit', 'update', 'destroy']);;
        //Route Role
        Route::get('/role/read', 'Admin\RoleController@read')->name('role.read');
        Route::get('/role/select', 'Admin\RoleController@select')->name('role.select');
        Route::resource('/role', 'Admin\RoleController');
        //Route Role Menu
        Route::post('/rolemenu/update', 'Admin\RoleMenuController@update');
        //Route User
        Route::get('/user/log', 'Admin\UserController@log')->name('user.log');
        Route::get('/user/read', 'Admin\UserController@read')->name('user.read');
        Route::get('/user/info', 'Admin\UserController@info')->name('user.info');
        Route::put('/user/updatepassword/{user}', 'Admin\UserController@updatepassword')->name('user.updatepassword');
        Route::resource('/user', 'Admin\UserController');
        // Route UOMCategory
        Route::get('/uomcategory/select', 'Admin\UomCategoryController@select')->name('uomcategory.select');
        Route::get('/uomcategory/read', 'Admin\UomCategoryController@read')->name('uomcategory.read');
        Route::resource('/uomcategory', 'Admin\UomCategoryController')->except(['show']);
        //Route Uom
        Route::get('/uom/read', 'Admin\UomController@read')->name('uom.read');
        Route::get('/uom/select', 'Admin\UomController@select')->name('uom.select');
        Route::resource('/uom', 'Admin\UomController');
        //Route Department
        Route::get('/department/read', 'Admin\DepartmentController@read')->name('department.read');
        Route::get('/department/select', 'Admin\DepartmentController@select')->name('department.select');
        Route::resource('/department', 'Admin\DepartmentController');
        //Route Title
        Route::get('/title/read', 'Admin\TitleController@read')->name('title.read');
        // Route::get('/title/employee', 'Admin\TitleController@employee')->name('title.employee');
        Route::get('/title/select', 'Admin\TitleController@select')->name('title.select');
        Route::resource('/title', 'Admin\TitleController');
        //Route Employee
        // Route::get('/employee/read', 'Admin\EmployeeController@read')->name('employee.read');
        // Route::get('/employee/select', 'Admin\EmployeeController@select')->name('employee.select');
        // Route::resource('/employee', 'Admin\EmployeeController');
        // Route CustomerGroup
        Route::get('/customergroup/select', 'Admin\CustomerGroupController@select')->name('customergroup.select');
        Route::get('/customergroup/read', 'Admin\CustomerGroupController@read')->name('customergroup.read');
        Route::resource('/customergroup', 'Admin\CustomerGroupController')->except(['show']);
        // Route Customer
        Route::get('/customer/select', 'Admin\CustomerController@select')->name('customer.select');
        Route::get('/customer/read', 'Admin\CustomerController@read')->name('customer.read');
        Route::resource('/customer', 'Admin\CustomerController')->except(['show']);
        // Route Contact
        Route::get('/customercontact/read', 'Admin\CustomerContactController@read')->name('customercontact.read');
        Route::resource('/customercontact', 'Admin\CustomerContactController')->except(['show']);
        // Route Addresss
        Route::get('/customeraddress/read', 'Admin\CustomerAddressController@read')->name('customeraddress.read');
        Route::resource('/customeraddress', 'Admin\CustomerAddressController')->except(['show']);
        //Route Principle
        Route::get('/principle/read', 'Admin\principleController@read')->name('principle.read');
        Route::resource('principle', 'Admin\PrincipleController');
        // Route Supplier
        Route::get('/supplier/read', 'Admin\SupplierController@read')->name('supplier.read');
        Route::resource('/supplier', 'Admin\SupplierController')->except(['show']);
        // Route ProductCategory
        Route::get('/productcategory/read', 'Admin\ProductCategoryController@read')->name('productcategory.read');
        Route::get('/productcategory/select', 'Admin\ProductCategoryController@select')->name('productcategory.select');
        Route::resource('/productcategory', 'Admin\ProductCategoryController')->except(['show']);
        Route::post('productcategory/archive/{id}', 'Admin\ProductCategoryController@archive')->name('productcategory.archive');
        //Route Product
        Route::get('/product/selectcategory', 'Admin\ProductController@selectCategory')->name('productcategory.select');
        Route::get('/product/selectuom', 'Admin\ProductController@selectUom')->name('uom.select');
        Route::get('/product/read', 'Admin\ProductController@read')->name('product.read');
        Route::get('/product/draft', 'Admin\ProductController@draft')->name('product.draft');
        Route::post('/product/subcat', 'Admin\ProductController@subcat')->name('subcat');
        Route::resource('/product', 'Admin\ProductController');
        // Route::Grade
        Route::get('/grade/read', 'Admin\GradeController@read')->name('grade.read');
        Route::get('/grade/select', 'Admin\GradeController@select')->name('grade.select');
        Route::resource('/grade', 'Admin\GradeController');
        // Route::Departments
        Route::get('departments/read', 'Admin\DepartmentsController@read')->name('departments.read');
        Route::resource('/departments', 'Admin\DepartmentsController');
        // Route Employee Allowance
        Route::get('/employeeallowance/read', 'Admin\EmployeeAllowanceController@read')->name('employeeallowance.read');
        Route::get('/employeeallowance/select', 'Admin\EmployeeAllowanceController@select')->name('employeeallowance.select');
        Route::get('/employeedetailallowance/readdetail', 'Admin\EmployeeAllowanceController@readdetail')->name('employeedetailallowance.readdetail');
        Route::post('/employeeallowance/generate', 'Admin\EmployeeAllowanceController@generate')->name('employeeallowance.generate');
        Route::resource('/employeeallowance', 'Admin\EmployeeAllowanceController')->except(['show']);
        // Route Employees
        Route::get('employees/select', 'Admin\EmployeesController@select')->name('employees.select');
        Route::get('employees/read', 'Admin\EmployeesController@read')->name('employees.read');
        Route::get('employees/leave', 'Admin\EmployeesController@leave')->name('employees.leave');
        Route::get('employees/penalty', 'Admin\EmployeesController@penalty')->name('penalty.read');
        Route::get('employees/leavedetail', 'Admin\EmployeesController@leavedetail')->name('employees.leavedetail');
        Route::get('employees/selectleave', 'Admin\EmployeesController@selectleave')->name('selectleave.select');
        Route::get('employees/showleave', 'Admin\EmployeesController@showleave')->name('employees.showleave');
        Route::put('/employees/update_allowances', 'Admin\EmployeesController@update_allowances')->name('employees.update_allowances');
        Route::put('/employees/update_penalty', 'Admin\EmployeesController@updatePenalty')->name('employees.update_penalty');
        Route::get('/employees/detail', 'Admin\EmployeesController@detail')->name('employees.detail');
        Route::get('/employees/readattendance', 'Admin\EmployeesController@readattendance')->name('employees.readattendance');
        Route::get('/employees/import', 'Admin\EmployeesController@import')->name('employees.import');
        Route::post('/employees/export', 'Admin\EmployeesController@export')->name('employees.export');
        Route::post('/employees/preview', 'Admin\EmployeesController@preview')->name('employees.preview');
        Route::post('employees/notactive/{id}', 'Admin\EmployeesController@notactive')->name('employees.notactive');
        Route::post('/employees/storemass', 'Admin\EmployeesController@storemass')->name('employees.storemass');
        Route::resource('/employees', 'Admin\EmployeesController');
        // Route Salary Employees
        Route::get('/salaryemployee/read', 'Admin\SalaryEmployeeController@read')->name('salaryemployee.read');
        Route::resource('/salaryemployee', 'Admin\SalaryEmployeeController')->except(['show']);
        // Route Employees Contract
        Route::get('/employeecontract/read', 'Admin\EmployeeContractController@read')->name('employeecontract.read');
        Route::resource('/employeecontract', 'Admin\EmployeeContractController')->except(['show']);
        // Route Employees Document
        Route::get('/employeedocument/read', 'Admin\EmployeeDocumentController@read')->name('employeedocument.read');
        Route::resource('/employeedocument', 'Admin\EmployeeDocumentController')->except(['show']);
        // Route Employees Career
        Route::get('/employeecareer/read', 'Admin\EmployeeCareerController@read')->name('employeecareer.read');
        Route::resource('/employeecareer', 'Admin\EmployeeCareerController')->except(['show']);
        // Route Employees Education
        Route::get('/employeeeducation/read', 'Admin\EmployeeEducationController@read')->name('employeeeducation.read');
        Route::resource('/employeeeducation', 'Admin\EmployeeEducationController')->except(['show']);
        // Route Employees Training
        Route::get('/employeetraining/read', 'Admin\EmployeeTrainingController@read')->name('employeetraining.read');
        Route::resource('/employeetraining', 'Admin\EmployeeTrainingController')->except(['show']);
        // Route Employees BPjs
        Route::get('/employeebpjs/read', 'Admin\EmployeeBpjsController@read')->name('employeebpjs.read');
        Route::resource('/employeebpjs', 'Admin\EmployeeBpjsController')->except(['show']);
        // Route Employees Member
        Route::get('/employeemember/read', 'Admin\EmployeeMemberController@read')->name('employeemember.read');
        Route::resource('/employeemember', 'Admin\EmployeeMemberController')->except(['show']);
        // Route Employees Experience
        Route::get('/employeeexperience/read', 'Admin\EmployeeExperienceController@read')->name('employeeexperience.read');
        Route::resource('/employeeexperience', 'Admin\EmployeeExperienceController')->except(['show']);
        // Route Allowance
        Route::get('/allowance/read', 'Admin\AllowanceController@read')->name('allowance.read');
        Route::get('/allowance/select', 'Admin\AllowanceController@select')->name('allowance.select');
        Route::resource('/allowance', 'Admin\AllowanceController')->except(['show']);
         // Route Group Allowance
        Route::get('/groupallowance/read', 'Admin\GroupAllowanceController@read')->name('groupallowance.read');
        Route::get('/groupallowance/select', 'Admin\GroupAllowanceController@select')->name('groupallowance.select');
        Route::resource('/groupallowance', 'Admin\GroupAllowanceController')->except(['show']);
        // Route Allowance_Rules
        Route::get('/allowancerule/read', 'Admin\AllowanceRuleController@read')->name('allowancerule.read');
        Route::get('/allowancerule/select', 'Admin\AllowanceRuleController@select')->name('allowancerule.select');
        Route::resource('/allowancerule', 'Admin\AllowanceRuleController')->except(['show']);
        // Route Allowance Report
        Route::get('/allowancereport/{id}/detail', 'Admin\AllowanceReportController@show')->name('allowancereport.detail');
        Route::post('/allowancereport/export', 'Admin\AllowanceReportController@export')->name('allowancereport.export');
        Route::get('/allowancereport/read', 'Admin\AllowanceReportController@read')->name('allowancereport.read');
        Route::resource('/allowancereport', 'Admin\AllowanceReportController')->except(['show']);
        //Route Allowance Report Detail
        Route::get('/allowancereportdetail/read', 'Admin\AllowanceReportDetailController@read')->name('allowancereportdetail.read');
        Route::resource('/allowancereportdetail', 'Admin\AllowanceReportDetailController');
        // Route::Base-Sallary
        Route::get('/basesallary/read', 'Admin\BestsallaryController@read')->name('basesallary.read');
        Route::get('/basesallary/select', 'Admin\BestsallaryController@select')->name('basesallary.select');
        Route::resource('/basesallary', 'Admin\BestsallaryController');
        // Route Sallary & Allowance
        Route::resource('/sallaryallowance', 'Admin\SallaryController');
        // Route Working Time
        Route::get('/workingtime/read', 'Admin\WorkingTimeController@read')->name('workingtime.read');
        Route::get('/workingtime/select', 'Admin\WorkingTimeController@select')->name('workingtime.select');
        Route::resource('/workingtime', 'Admin\WorkingTimeController');
        // Route Account
        Route::get('/account/read', 'Admin\AccountController@read')->name('account.read');
        Route::get('/account/select', 'Admin\AccountController@select')->name('account.select');
        Route::resource('/account', 'Admin\AccountController')->except(['show']);
        // Route Calendar
        Route::get('/calendar/read', 'Admin\CalendarController@read')->name('calendar.read');
        Route::get('/calendar/select', 'Admin\CalendarController@select')->name('calendar.select');
        Route::get('/calendar/{id}/detail', 'Admin\CalendarController@detail')->name('calendar.detail');
        Route::resource('/calendar', 'Admin\CalendarController')->except(['show']);
        // Route Calendar_Exception
        Route::get('/calendarexc/read', 'Admin\CalendarExcController@read')->name('calendarexception.read');
        Route::get('/calendarexc/select', 'Admin\CalendarExcController@select')->name('calendarexception.select');
        Route::get('/calendarexc/{id}/calendar', 'Admin\CalendarExcController@calendar')->name('calendarexception.calendar');
        Route::post('/calendarexc/addcalendar', 'Admin\CalendarExcController@addcalendar')->name('calendarexc.addcalendar');
        Route::resource('/calendarexc', 'Admin\CalendarExcController')->except(['show']);
        // Route Work Group Combination
        Route::get('/workgroup/read', 'Admin\WorkgroupController@read')->name('workgroup.read');
        Route::get('/workgroup/select', 'Admin\WorkgroupController@select')->name('workgroup.select');
        Route::put('/workgroup/update_allowances', 'Admin\WorkgroupController@update_allowances')->name('workgroup.update_allowances');
        Route::resource('/workgroup', 'Admin\WorkgroupController')->except(['show']);
        // Route Work Group Allowance
        Route::get('/workgroupallowance/read', 'Admin\WorkgroupAllowanceController@read')->name('workgroupallowance.read');
        Route::get('/workgroupallowance/select', 'Admin\WorkgroupAllowanceController@select')->name('workgroupallowance.select');
        Route::resource('/workgroupallowance', 'Admin\WorkgroupAllowanceController')->except(['show']);
        // Route Attendance Approval
        Route::get('/attendanceapproval/read', 'Admin\AttendanceApprovalController@read')->name('attendanceapproval.read');
        Route::get('/attendanceapproval/attendance_log', 'Admin\AttendanceApprovalController@attendance_log')->name('attendanceapproval.attendance_log');
        Route::get('/attendanceapproval/select', 'Admin\AttendanceApprovalController@select')->name('attendanceapproval.select');
        Route::get('/attendanceapproval/selectworkingtime', 'Admin\AttendanceApprovalController@selectworkingtime')->name('attendanceapproval.selectworkingtime');
        Route::get('/attendanceapproval/selectscheme', 'Admin\AttendanceApprovalController@selectscheme')->name('attendanceapproval.selectscheme');
        Route::get('/attendanceapproval/{id}/edithistory', 'Admin\AttendanceApprovalController@edithistory')->name('attendanceapproval.edithistory');
        Route::get('/attendanceapproval/{id}/detail', 'Admin\AttendanceApprovalController@detail')->name('attendanceapproval.detail');
        Route::put('/attendanceapproval/{id}/updatehistory', 'Admin\AttendanceApprovalController@updatehistory')->name('attendanceapproval.updatehistory');
        Route::post('/attendanceapproval/approve', 'Admin\AttendanceApprovalController@approve')->name('attendanceapproval.approve');
        Route::post('/attendanceapproval/export', 'Admin\AttendanceApprovalController@export')->name('attendanceapproval.export');
        Route::post('/attendanceapproval/deletemass', 'Admin\AttendanceApprovalController@deletemass')->name('attendanceapproval.deletemass');
        Route::post('/attendanceapproval/setsessionfilter', 'Admin\AttendanceApprovalController@setsessionfilter')->name('attendanceapproval.setsessionfilter');
        Route::post('/attendanceapproval/quickupdate', 'Admin\AttendanceApprovalController@quickupdate')->name('attendanceapproval.quickupdate');
        Route::resource('/attendanceapproval', 'Admin\AttendanceApprovalController')->except(['show']);
        // Route Attendance
        Route::get('/attendance/read', 'Admin\AttendanceController@read')->name('attendance.read');
        Route::get('/attendance/select', 'Admin\AttendanceController@select')->name('attendance.select');
        Route::get('/attendance/import', 'Admin\AttendanceController@import')->name('attendance.import');
        Route::post('/attendance/preview', 'Admin\AttendanceController@preview')->name('attendance.preview');
        Route::post('/attendance/storemass', 'Admin\AttendanceController@storemass')->name('attendance.storemass');
        Route::post('/attendance/preview2', 'Admin\AttendanceController@preview2')->name('attendance.preview2');
        Route::post('/attendance/storemass2', 'Admin\AttendanceController@storemass2')->name('attendance.storemass2');
        Route::resource('/attendance', 'Admin\AttendanceController')->except(['show']);
        // Route Daily Report
        Route::get('/dailyreport/read', 'Admin\DailyReportController@read')->name('dailyreport.read');
        Route::get('/dailyreport/select', 'Admin\DailyReportController@select')->name('dailyreport.select');
        Route::get('/dailyreport/{id}/detail', 'Admin\DailyReportController@detail')->name('dailyreport.detail');
        Route::get('/dailyreport/detail', 'Admin\DailyReportController@detail')->name('dailyreport.detail');
        Route::post('/dailyreport/export', 'Admin\DailyReportController@export')->name('dailyreport.export');
        Route::resource('/dailyreport', 'Admin\DailyReportController')->except(['show']);
        // Route Summary Report
        Route::get('/summaryreport/read', 'Admin\SummaryReportController@read')->name('summaryreport.read');
        Route::get('/summaryreport/select', 'Admin\SummaryReportController@select')->name('summaryreport.select');
        Route::resource('/summaryreport', 'Admin\SummaryReportController')->except(['show']);
        // Route Workgroup Master
        Route::get('/workgroupmaster/read', 'Admin\WorkgroupMasterController@read')->name('workgroupmaster.read');
        Route::get('/workgroupmaster/select', 'Admin\WorkgroupMasterController@select')->name('workgroupmaster.select');
        Route::resource('/workgroupmaster', 'Admin\WorkgroupMasterController')->except(['show']);
        // Route Outsourcing
        Route::get('/outsourcing/select', 'Admin\OutsourcingController@select')->name('outsourcing.select');
        Route::get('/outsourcing/read', 'Admin\OutsourcingController@read')->name('outsourcing.read');
        Route::resource('/outsourcing', 'Admin\OutsourcingController')->except(['show']);
        // Route Outsourcing Address
        Route::get('/outsourcingaddress/read', 'Admin\OutsourcingAddressController@read')->name('outsourcingaddress.read');
        Route::resource('outsourcingaddress', 'Admin\OutsourcingAddressController');
        //Route Outsourcing Pic
        Route::get('/outsourcingpic/read', 'Admin\OutsourcingPicController@read')->name('outsourcingpic.read');
        Route::resource('outsourcingpic', 'Admin\OutsourcingPicController');
        //Route Outsourcing Document
        Route::get('/outsourcingdocument/read', 'Admin\OutsourcingDocumentController@read')->name('outsourcingdocument.read');
        Route::resource('outsourcingdocument', 'Admin\OutsourcingDocumentController');
        //Route Outsourcing Employee
        Route::get('/outsourcingemployee/read', 'Admin\OutsourcingEmployeeController@read')->name('outsourcingemployee.read');
        // Route Leave
        Route::get('/leave/read', 'Admin\LeaveController@read')->name('leave.read');
        Route::get('/leave/selectemployee', 'Admin\LeaveController@selectemployee')->name('leave.selectemployee');
        Route::get('/leave/selectleave', 'Admin\LeaveController@selectleave')->name('leave.selectleave');
        Route::post('/leave/createdate', 'Admin\LeaveController@createdate')->name('leave.createdate');
        Route::post('/leave/removedate', 'Admin\LeaveController@removedate')->name('leave.removedate');
        Route::post('/leave/quickupdate', 'Admin\LeaveController@quickupdate')->name('leave.quickupdate');
        Route::resource('/leave', 'Admin\LeaveController')->except(['show']);
        // Route Leave Approval
        Route::get('/leaveapproval/readapproval', 'Admin\LeaveApprovalController@readapproval')->name('leaveapproval.readapproval');
        Route::get('/leaveapproval', 'Admin\LeaveApprovalController@indexapproval')->name('leaveapproval.indexapproval');
        Route::get('/leaveapproval/{id}/editapproval', 'Admin\LeaveApprovalController@editapproval')->name('leaveapproval.editapproval');
        Route::put('/leaveapproval/approval/{id}', 'Admin\LeaveApprovalController@updateapprove')->name('leaveapproval.updateapprove');
        // Route Leave Report
        Route::get('/leavereport/read', 'Admin\LeaveReportController@read')->name('leavereport.read');
        Route::get('/leavereport/{id}/detail', 'Admin\LeaveReportController@show')->name('leavereport.detail');
        Route::post('/leavereport/export', 'Admin\LeaveReportController@export')->name('leavereport.export');
        Route::resource('/leavereport', 'Admin\LeaveReportController')->except(['show']);
        // Route Leave Balance
        Route::get('/leavebalance/read', 'Admin\LeaveBalanceController@read')->name('leavebalance.read');
        Route::get('/leavebalance/select', 'Admin\LeaveBalanceController@select')->name('leavebalance.select');
        Route::get('/leavebalance/getleavename', 'Admin\LeaveBalanceController@getLeaveName')->name('leavebalance.getleavename');
        Route::resource('/leavebalance', 'Admin\LeaveBalanceController')->except(['show']);
        // Route Leave Setting
        Route::get('/leavesetting/read', 'Admin\LeaveSettingController@read')->name('leavesetting.read');
        Route::get('/leavesetting/select', 'Admin\LeaveSettingController@select')->name('leavesetting.select');
        Route::resource('/leavesetting', 'Admin\LeaveSettingController')->except(['show']);
        // Route Driver Allowance
        Route::get('/driverallowance/read', 'Admin\DriverAllowanceController@read')->name('driverallowance.read');
        Route::get('/driverallowance/select', 'Admin\DriverAllowanceController@select')->name('driverallowance.select');
        Route::resource('/driverallowance', 'Admin\DriverAllowanceController')->except(['show']);
        // Route Delivery Order
        Route::get('/deliveryorder/read', 'Admin\DeliveryOrderController@read')->name('deliveryorder.read');
        Route::get('/deliveryorder/read_donumber', 'Admin\DeliveryOrderController@read_donumber')->name('deliveryorder.read_donumber');
        Route::get('/deliveryorder/select', 'Admin\DeliveryOrderController@select')->name('deliveryorder.select');
        Route::get('/deliveryorder/select_employee', 'Admin\DeliveryOrderController@select_employee')->name('deliveryorder.select_employee');
        Route::get('/deliveryorder/print/{id}', 'Admin\DeliveryOrderController@print');
        Route::resource('/deliveryorder', 'Admin\DeliveryOrderController')->except(['show']);
        // Route Daily Report Driver
        Route::get('/dailyreportdriver/read', 'Admin\DailyReportDriverController@read')->name('dailyreportdriver.read');
        Route::get('/dailyreportdriver/readcalculation', 'Admin\DailyReportDriverController@readcalculation')->name('dailyreportdriver.readcalculation');
        Route::get('/dailyreportdriver/readadditional', 'Admin\DailyReportDriverController@readadditional')->name('dailyreportdriver.readadditional');
        Route::get('/dailyreportdriver/select', 'Admin\DailyReportDriverController@select')->name('dailyreportdriver.select');
        Route::resource('/dailyreportdriver', 'Admin\DailyReportDriverController')->except(['show']);
        // Route Reimbursement
        Route::post('/reimbursement/updatestatuscalculation', 'Admin\ReimbursementController@updatestatuscalculation')->name('reimbursement.updatestatuscalculation');
        Route::post('/reimbursement/updatestatusallowance', 'Admin\ReimbursementController@updatestatusallowance')->name('reimbursement.updatestatusallowance');
        Route::post('/reimbursement/exportreimbursment', 'Admin\ReimbursementController@exportreimbursment')->name('reimbursement.exportreimbursment');
        Route::get('/reimbursement/readdailydriver', 'Admin\ReimbursementController@readdailydriver')->name('reimbursement.readdailydriver');
        Route::get('/reimbursement/getdata', 'Admin\ReimbursementController@getdata')->name('reimbursement.getdata');
        Route::get('/reimbursement/readallowance', 'Admin\ReimbursementController@readallowance')->name('reimbursement.readallowance');
        Route::get('/reimbursement/read', 'Admin\ReimbursementController@read')->name('reimbursement.read');
        Route::get('/reimbursement/select', 'Admin\ReimbursementController@select')->name('reimbursement.select');
        Route::get('/reimbursement/print', 'Admin\ReimbursementController@print')->name('reimbursement.print');
        Route::resource('/reimbursement', 'Admin\ReimbursementController')->except(['show']);
        // Route Adjustment Mass
        Route::post('/adjustmentmass/updatemass', 'Admin\AdjustmentMassController@updatemass')->name('adjustmentmass.updatemass');
        Route::get('/adjustmentmass/read', 'Admin\AdjustmentMassController@read')->name('adjustmentmass.read');
        Route::get('/adjustmentmass/select', 'Admin\AdjustmentMassController@select')->name('adjustmentmass.select');
        Route::get('/adjustmentmass/multi', 'Admin\AdjustmentMassController@multi')->name('adjustmentmass.multi');
        Route::resource('/adjustmentmass', 'Admin\AdjustmentMassController');
        // Route Overtime Scheme
        Route::get('/overtimescheme/read', 'Admin\OvertimeSchemeController@read')->name('overtimescheme.read');
        Route::get('/overtimescheme/select', 'Admin\OvertimeSchemeController@select')->name('overtimescheme.select');
        Route::resource('/overtimescheme', 'Admin\OvertimeSchemeController')->except(['show']);
        //Route Salary report
        Route::post('/salaryreport/exportsalary', 'Admin\SalaryReportController@exportsalary')->name('salaryreport.exportsalary');
        Route::get('/salaryreport/readapproval', 'Admin\SalaryReportController@readapproval')->name('salaryreport.readapproval');
        Route::get('/salaryreport/approval', 'Admin\SalaryReportController@indexapproval')->name('salaryreport.indexapproval');
        Route::get('/salaryreport/{id}/editapproval', 'Admin\SalaryReportController@editapproval')->name('salaryreport.editapproval');
        Route::put('/salaryreport/approval/{id}', 'Admin\SalaryReportController@updateapprove')->name('salaryreport.updateapprove');
        Route::get('/salaryreport/read', 'Admin\SalaryReportController@read')->name('salaryreport.read');
        Route::get('/salaryreport/{id}/detail', 'Admin\SalaryReportController@show')->name('salaryreport.detail');
        Route::get('/salaryreport/printmass', 'Admin\SalaryReportController@printmass')->name('salaryreport.printmass');
        Route::get('/salaryreport/print-pdf', 'Admin\SalaryReportController@print_pdf')->name('salaryreport.print-pdf');
        Route::get('/salaryreport/pdf/{id}', 'Admin\SalaryReportController@pdf');
        Route::post('/salaryreport/waitingapproval', 'Admin\SalaryReportController@waitingapproval')->name('salaryreport.waitingapproval');
        Route::post('/salaryreport/approve', 'Admin\SalaryReportController@approve')->name('salaryreport.approve');
        Route::resource('/salaryreport', 'Admin\SalaryReportController');
        //Route Salary report detail
        Route::get('/salaryreportdetail/read_gross', 'Admin\SalaryReportDetailController@read_gross')->name('salaryreportdetail.read_gross');
        Route::get('/salaryreportdetail/read_deduction', 'Admin\SalaryReportDetailController@read_deduction')->name('salaryreportdetail.read_deduction');
        Route::resource('/salaryreportdetail', 'Admin\SalaryReportDetailController');
        // Route Overtime
        Route::get('/overtime/read', 'Admin\OvertimeController@read')->name('overtime.read');
        Route::get('/overtime/read_overtime', 'Admin\OvertimeController@read_overtime')->name('overtime.read_overtime');
        Route::resource('/overtime', 'Admin\OvertimeController');
        // Route PPh
        Route::get('/pph/read', 'Admin\PphController@read')->name('pph.read');
        Route::get('/pph/{id}/detail', 'Admin\PphController@show')->name('pph.detail');
        Route::get('/pph/print-pdf', 'Admin\PphController@print_pdf')->name('pph.print-pdf');
        Route::get('/pph/pdf/{id}', 'Admin\PphController@pdf');
        Route::resource('/pph', 'Admin\PphController');
        //Route PPh Report Detail
        Route::get('/pphreportdetail/read_gross', 'Admin\PphDetailController@read_gross')->name('pphreportdetail.read_gross');
        Route::get('/pphreportdetail/read_deduction', 'Admin\PphDetailController@read_deduction')->name('pphreportdetail.read_deduction');
        Route::resource('/pphreportdetail', 'Admin\PphDetailController');
        //Route Break Time
        Route::get('/breaktime/read', 'Admin\BreakTimeController@read')->name('breaktime.read');
        Route::get('/breaktime/select', 'Admin\BreakTimeController@select')->name('breaktime.select');
        Route::get('/breaktime/multi', 'Admin\BreakTimeController@multi')->name('breaktime.multi');
        Route::resource('/breaktime', 'Admin\BreakTimeController');
        // Route Driver Allowance List
        Route::get('/driverallowancelist/read', 'Admin\DriverAllowanceListController@read')->name('driverallowancelist.read');
        Route::get('/driverallowancelist/read_detail', 'Admin\DriverAllowanceListController@read_detail')->name('driverallowancelist.read_detail');
        //Route Asset Category
        Route::get('/assetcategory/read', 'Admin\AssetCategoryController@read')->name('assetcategory.read');
        Route::get('/assetcategory/select', 'Admin\AssetCategoryController@select')->name('assetcategory.select');
        Route::post('assetcategory/archive/{id}', 'Admin\AssetCategoryController@archive')->name('assetcategory.archive');
        Route::post('assetcategory/nonarchive/{id}', 'Admin\AssetCategoryController@nonarchive')->name('assetcategory.archive');
        Route::resource('/assetcategory', 'Admin\AssetCategoryController');

        //Route Asset
        Route::post('/asset/stockupdate', 'Admin\AssetController@stockupdate')->name('asset.stockupdate');
        Route::get('/asset/select', 'Admin\AssetController@select')->name('asset.select');
        Route::get('/asset/selectcategory', 'Admin\AssetController@selectcategory')->name('asset.selectcategory');
        Route::get('/asset/read', 'Admin\AssetController@read')->name('asset.read');
        Route::get('/asset/draft', 'Admin\AssetController@draft')->name('asset.draft');
        Route::post('/asset/subcat', 'Admin\AssetController@subcat')->name('asset.subcat');
        Route::get('/asset/import', 'Admin\AssetController@import')->name('asset.import');
        Route::get('/asset/serial/{id}', 'Admin\AssetController@serial')->name('asset.serial');
        Route::put('/asset/serial/{id}/update', 'Admin\AssetController@serialupdate')->name('asset.serialupdate');
        Route::post('/asset/preview', 'Admin\AssetController@preview')->name('asset.preview');
        Route::post('/asset/storemass', 'Admin\AssetController@storemass')->name('asset.storemass');
        Route::post('/asset/export', 'Admin\AssetController@export')->name('asset.export');
        Route::resource('/asset', 'Admin\AssetController');

        //Route Asset Serial
        Route::get('/assetserial/selectemployee', 'Admin\AssetSerialController@selectemployee')->name('assetemployee.select');
        Route::delete('/assetserial/deleteserial/{id}', 'Admin\AssetSerialController@deleteserial');
        Route::get('/assetserial/read', 'Admin\AssetSerialController@read')->name('assetserial.read');
        Route::resource('/assetserial', 'Admin\AssetSerialController');

        //Route Asset Movement
        Route::get('/assetmovement/read', 'Admin\AssetMovementController@read')->name('assetmovement.read');
        Route::get('/assetmovement/assetserialselect', 'Admin\AssetMovementController@assetserialselect')->name('assetserial.select');
        Route::resource('/assetmovement', 'Admin\AssetMovementController');

        //Route Asset Movement
        Route::get('/documentmanagement/select', 'Admin\DocumentManagementController@select')->name('documentmanagement.select');
        Route::get('/documentmanagement/read', 'Admin\DocumentManagementController@read')->name('documentmanagement.read');
        Route::resource('/documentmanagement', 'Admin\DocumentManagementController');

        //Route Salary Increases
        Route::get('/salaryincreases/reademployee', 'Admin\SalaryIncreasesController@reademployee')->name('salaryincreases.reademployee');
        Route::get('/salaryincreases/read', 'Admin\SalaryIncreasesController@read')->name('salaryincreases.read');
        Route::get('/salaryincreases/{id}/show', 'Admin\SalaryIncreasesController@show')->name('salaryincreases.show');
        Route::resource('/salaryincreases', 'Admin\SalaryIncreasesController');
        //Route Detail Create Salary Increases
        Route::get('/salaryincreasedetail/read', 'Admin\SalaryIncreaseDetailController@read')->name('salaryincreasedetail.read');
        Route::resource('/salaryincreasedetail', 'Admin\SalaryIncreaseDetailController');
        //Route Asset
        Route::get('/vehicle/selectcategory', 'Admin\VehicleController@selectcategory')->name('vehicle.selectcategory');
        Route::get('/vehicle/driver', 'Admin\VehicleController@select_employee')->name('vehicle.driver');
        Route::get('/vehicle/read', 'Admin\VehicleController@read')->name('vehicle.read');
        Route::post('/vehicle/exportmaintenance', 'Admin\VehicleController@exportmaintenance')->name('vehicle.exportmaintenance');
        Route::post('/vehicle/exporthistory', 'Admin\VehicleController@exporthistory')->name('vehicle.exporthistory');
        Route::get('/vehicle/readmaintenance', 'Admin\VehicleController@readmaintenance')->name('vehicle.readmaintenance');
        Route::get('/vehicle/readhistories', 'Admin\VehicleController@readhistories')->name('vehicle.readhistories');
        Route::get('/vehicle/draft', 'Admin\VehicleController@draft')->name('vehicle.draft');
        Route::post('/vehicle/subcat', 'Admin\VehicleController@subcat')->name('vehicle.subcat');
        Route::get('/vehicle/import', 'Admin\VehicleController@import')->name('vehicle.import');
        Route::post('/vehicle/preview', 'Admin\VehicleController@preview')->name('vehicle.preview');
        Route::post('/vehicle/storemass', 'Admin\VehicleController@storemass')->name('vehicle.storemass');
        Route::resource('/vehicle', 'Admin\VehicleController');
        //Route Oil
        Route::post('/oil/stockupdate', 'Admin\OilController@stockupdate')->name('oil.stockupdate');
        Route::get('/oil/read', 'Admin\OilController@read')->name('oil.read');
        Route::get('/oil/readoil', 'Admin\OilController@consumeoil')->name('oil.readconsumeoil');
        Route::get('/oil/readconsumeoil', 'Admin\OilController@readconsumeoil')->name('oil.consumeoil');
        Route::get('/oil/readhistories', 'Admin\OilController@readhistories')->name('oil.readhistories');
        Route::get('/oil/draft', 'Admin\OilController@draft')->name('oil.draft');
        Route::post('/oil/subcat', 'Admin\OilController@subcat')->name('oil.subcat');
        Route::get('/oil/import', 'Admin\OilController@import')->name('oil.import');
        Route::post('/oil/preview', 'Admin\OilController@preview')->name('oil.preview');
        Route::post('/oil/export', 'Admin\OilController@export')->name('oil.export');
        Route::post('/oil/exportconsume', 'Admin\OilController@exportconsume')->name('oil.exportconsume');
        Route::post('/oil/exporthistory', 'Admin\OilController@exporthistory')->name('oil.exporthistory');
        Route::post('/oil/storemass', 'Admin\OilController@storemass')->name('oil.storemass');
        Route::resource('/oil', 'Admin\OilController');

        //Route Consume Oil
        Route::get('/consumeoil/read', 'Admin\ConsumeOilController@read')->name('consumeoil.read');
        Route::get('/consumeoil/readoil', 'Admin\ConsumeOilController@readoil')->name('consumeoil.readoil');
        Route::get('/consumeoil/readvehicle', 'Admin\ConsumeOilController@readvehicle')->name('consumeoil.readvehicle');
        Route::resource('/consumeoil', 'Admin\ConsumeOilController');

        //Route Maintenance
        Route::get('/maintenance/read', 'Admin\MaintananceController@read')->name('maintanance.read');
        Route::get('/maintenance/readvehicle', 'Admin\MaintananceController@readvehicle')->name('maintanance.readvehicle');
        Route::post('/maintenance/export', 'Admin\MaintananceController@export')->name('maintenance.export');
        Route::resource('/maintenance', 'Admin\MaintananceController');

        // Route Overtime Report
        Route::get('/overtimereport/read', 'Admin\OvertimeReportController@read')->name('overtimereport.read');
        Route::post('/overtimereport/export1', 'Admin\OvertimeReportController@export1')->name('overtimereport.export1');
        Route::post('/overtimereport/export2', 'Admin\OvertimeReportController@export2')->name('overtimereport.export2');
        Route::resource('/overtimereport', 'Admin\OvertimeReportController');
        
        // Route SPL
        Route::get('/spl/selectemployee', 'Admin\SPLController@selectemployee')->name('spl.selectemployee');
        Route::get('spl/read', 'Admin\SPLController@read')->name('spl.read');
        Route::get('/spl/import', 'Admin\SPLController@import')->name('spl.import');
        Route::post('/spl/preview', 'Admin\SPLController@preview')->name('spl.preview');
        Route::post('/spl/storemass', 'Admin\SPLController@storemass')->name('spl.storemass');
        Route::resource('/spl', 'Admin\SPLController');

        //Route Partner
        Route::get('/partner/read', 'Admin\PartnerController@read')->name('partner.read');
        Route::resource('/partner', 'Admin\PartnerController');
    });
});