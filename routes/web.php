<?php

use App\Http\Controllers\Ajax\DashboardController as AjaxDashboardController;
use App\Http\Controllers\Ajax\LocationController;
use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\UserCatalogueController;
use App\Http\Controllers\Backend\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')
    ->as('admin.')
    ->middleware('admin')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        Route::prefix('users')
            ->as('users.')
            ->group(function () {
                Route::get('index',                 [UserController::class, 'index'])->name('index');
                Route::get('create',                [UserController::class, 'create'])->name('create');
                Route::post('store',                [UserController::class, 'store'])->name('store');
                Route::get('edit/{user}',           [UserController::class, 'edit'])->where(['id' => '[0-9]+'])->name('edit');
                Route::put('udpate/{user}',         [UserController::class, 'udpate'])->where(['id' => '[0-9]+'])->name('udpate');
                Route::get('delete/{user}',         [UserController::class, 'delete'])->where(['id' => '[0-9]+'])->name('delete');
                Route::delete('destroy/{user}',     [UserController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('destroy');
            });

            Route::prefix('user_catalogue')
            ->as('user_catalogue.')
            ->group(function () {
                Route::get('index',                             [UserCatalogueController::class, 'index'])->name('index');
                Route::get('create',                            [UserCatalogueController::class, 'create'])->name('create');
                Route::post('store',                            [UserCatalogueController::class, 'store'])->name('store');
                Route::get('edit/{user_catalogue}',             [UserCatalogueController::class, 'edit'])->where(['id' => '[0-9]+'])->name('edit');
                Route::put('udpate/{user_catalogue}',           [UserCatalogueController::class, 'udpate'])->where(['id' => '[0-9]+'])->name('udpate');
                Route::get('delete/{user_catalogue}',           [UserCatalogueController::class, 'delete'])->where(['id' => '[0-9]+'])->name('delete');
                Route::delete('destroy/{user_catalogue}',       [UserCatalogueController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('destroy');
            });
        Route::get('ajax/location/getlocation',         [LocationController::class, 'getLocation'])->name('ajax.location.index');
        Route::post('ajax/dashboard/changeStatus',      [AjaxDashboardController::class, 'changeStatus'])->name('ajax.dashboard.changeStatus');
        Route::post('ajax/dashboard/changeStatusAll',   [AjaxDashboardController::class, 'changeStatusAll'])->name('ajax.dashboard.changeStatusAll');
    });

// Route::get('admin/dashboard', [DashboardController::class, 'index'])->name('dashboard.index')->middleware('admin');


/* User */
// Route::group(['prefix' => 'user'], function () {
//     Route::get('index',    [UserController::class, 'index'])->name('user.index')->middleware('admin');
//     Route::get('create',    [UserController::class, 'create'])->name('user.create')->middleware('admin');
// });

/* AJAX */

// Route::get('ajax/location/getlocation', [LocationController::class, 'getLocation'])->name('ajax.location.index')->middleware('login');



Route::get('admin', [AuthController::class, 'index'])->name('auth.admin')->middleware('login');
Route::post('login', [AuthController::class, 'login'])->name('auth.login');
Route::get('logout', [AuthController::class, 'logout'])->name('auth.logout');
