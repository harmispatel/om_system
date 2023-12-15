<?php

use App\Http\Controllers\{AuthController,DashboardController,GeneralSettingController, RoleController, AdminController, OrderController, ReportController,QrCodeController,DompdfController, TaskManageController,types_workController};
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\facades\Session;


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

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('admin.login');
});

// Cache Clear Route
Route::get('config-clear', function ()
{
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    return redirect()->route('admin.dashboard');
});

// Admin Auth Routes
Route::get('admin/login', [AuthController::class,'showAdminLogin'])->name('admin.login');
Route::post('admin/do/login', [AuthController::class,'Adminlogin'])->name('admin.do.login');
// Route::get('admin/logout',[AuthController::class,'Adminlogout'])->name('user.logout');
Route::get('/logout', [AuthController::class, function() {

        Auth::guard('admin')->logout();

    return redirect()->route('admin.login')->with('success', 'User Logout SuccessFully...');
}])->name('admin.logout');

  //print
   Route::get('/orders/show/print/{id}',[OrderController::class ,'oneForm'])->name('order.show.print');
   Route::get('/orders/print/{id}',[OrderController::class ,'printOrder'])->name('order.print');


// Admin Routes
Route::group(['prefix' => 'admin'], function ()
{
    // If Auth Login
    Route::group(['middleware' => ['is_admin']], function ()
    {
        // Logout Route

        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });

      // General Setting
        Route::get('General/create',[GeneralSettingController::class,'create'])->name('General.create');
        Route::post('General/store',[GeneralSettingController::class,'store'])->name('General.store');


        // Dashboard
        Route::get('dashboard', [DashboardController::class,'index'])->name('admin.dashboard');

        //task-management
        Route::get('task-management',[TaskManageController::class,'index'])->name('task-management');
        Route::post('task-management/create',[TaskManageController::class,'create'])->name('task-manage.create');
        Route::get('task-management/list',[TaskManageController::class,'list'])->name('task-manage.list');
        Route::get('task-management/edit/{id}',[TaskManageController::class,'edit'])->name('task-manage.edit');
        Route::post('task-management/update',[TaskManageController::class,'update'])->name('task-manage.update');
        Route::post('task-management/list/destroy',[TaskManageController::class,'destroy'])->name('task-manage.destroy');

        // User
        Route::get('users',[AdminController::class,'index'])->name('users');
        Route::get('users/profile',[AdminController::class,'userProfile'])->name('users.profile');
        Route::get('users/load',[AdminController::class,'loadUsers'])->name('users.load');
        Route::get('users/create',[AdminController::class,'create'])->name('users.create');
        Route::post('users/store',[AdminController::class,'store'])->name('users.store');
        Route::post('users/status',[AdminController::class,'status'])->name('users.status');
        Route::post('users/update',[AdminController::class,'update'])->name('users.update');
        Route::get('users/edit/{id}',[AdminController::class,'edit'])->name('users.edit');
        Route::post('users/destroy',[AdminController::class,'destroy'])->name('users.destroy');

        // Roles
        Route::get('departments',[RoleController::class,'index'])->name('roles');
        Route::get('departments/create',[RoleController::class,'create'])->name('roles.create');
        Route::post('departments/store',[RoleController::class,'store'])->name('roles.store');
        Route::get('departments/edit/{id}',[RoleController::class,'edit'])->name('roles.edit');
        Route::post('departments/update',[RoleController::class,'update'])->name('roles.update');
        Route::post('departments/destroy',[RoleController::class,'destroy'])->name('roles.destroy');

        // Orders
        Route::get('orders', [OrderController::class,'index'])->name('order');
        Route::get('orders/create', [OrderController::class,'create'])->name('orders.create');
        Route::post('orders/store', [OrderController::class,'store'])->name('orders.store');
        Route::get('ordersDetail/{id}',[OrderController::class,'show'])->name('order.show');
        Route::get('orders/retrive/{id}',[OrderController::class,'retriveOrder'])->name('order.retrive');
        Route::post('orders/destroy',[OrderController::class,'destroy'])->name('order.destroy');
        route::get('/orders/issue/design/{id}',[OrderController::class,'issueToDesign'])->name('orders.issue.design');
        route::get('/orders/issue/waxing/{id}',[OrderController::class,'issueToWaxing'])->name('orders.issue.waxing');
        route::get('/orders/receive/design/{id}',[OrderController::class,'receiveForDesign'])->name('orders.rec.design');
        route::get('/orders/receive/waxing/{id}',[OrderController::class,'receiveForWaxing'])->name('orders.rec.waxing');
        route::get('/orders/issue/casting/{id}',[OrderController::class,'issueForCasting'])->name('orders.iss.casting');
        route::get('/orders/receive/casting/{id}',[OrderController::class,'receiveForCasting'])->name('orders.rec.casting');
        route::get('/orders/issue/hisab/{id}',[OrderController::class,'issueForHisab'])->name('orders.iss.hisab');
        route::get('/orders/receive/hisab/{id}',[OrderController::class,'receiveForHisab'])->name('orders.rec.hisab');
        route::get('/orders/issue/central/{id}',[OrderController::class,'issueForCentral'])->name('orders.iss.central');
        route::get('/orders/receive/central/{id}',[OrderController::class,'receiveForCentral'])->name('orders.rec.central');
        route::get('/orders/issue/ready/{id}',[OrderController::class,'issueForReady'])->name('orders.iss.ready');
        route::get('/orders/receive/ready/{id}',[OrderController::class,'receiveForReady'])->name('orders.rec.ready');
        route::get('/orders/issue/delivery/{id}',[OrderController::class,'issueForDelivery'])->name('orders.iss.delivery');
        route::get('/orders/receive/delivery/{id}',[OrderController::class,'receiveForDelivery'])->name('orders.rec.delivery');
        route::get('/orders/issue/packing/{id}',[OrderController::class,'issueForPacking'])->name('orders.iss.packing');
        route::get('/orders/receive/packing/{id}',[OrderController::class,'receiveForPacking'])->name('orders.rec.packing');
        route::get('/orders/complete/delivery/{id}',[OrderController::class,'completeDelivery'])->name('orders.delivery');
        route::get('/orders/issue/saleing/{id}',[OrderController::class,'issueForSaleing'])->name('orders.iss.saleing');

        //getdata
        Route::post('/get-data',[OrderController::class,'getData'])->name('getdata');
        Route::get('/fetch-data',[OrderController::class,'fetchdata'])->name('fetchdata');


        //Reports
        Route::get('reports/order-history/', [ReportController::class,'orderHistoryReport'])->name('reports.order_history');
        Route::get('reports/order-history/details/{id}', [ReportController::class,'orderHistoryReportDetails'])->name('reports.order_history_details');
        Route::get('reports/department-pending-orders/', [ReportController::class,'departmentPendingOrdersReport'])->name('reports.department_pending_orders');
        Route::get('reports/typesofworks-pending/', [ReportController::class,'typesOfWorksPendingReport'])->name('reports.typesofworks_pending');

        Route::get('reports/department-performance/', [ReportController::class,'departmentPerformanceReport'])->name('reports.performance');

        //qrpage

        route::get('/qrcode-home',function(){
            return view('admin.qrpages.open-qrcode');
        })->name('qrpage');

         //mobile
         Route::post('/customer-name',[OrderController::class,'mobileSetName'])->name('mobileSetName');


        //types of work

        Route::get('/types_of_work',[types_workController::class,'index'])->name('types_work');
        Route::get('edit/types_of_work/{id}',[types_workController::class,'edit'])->name('edit.types_work');
        Route::post('update/types_of_work',[types_workController::class,'update'])->name('update.types_work');



    });
});
