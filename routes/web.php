<?php

use App\Http\Controllers\Ajax\DashboardController as AjaxDashboardController;
use App\Http\Controllers\Ajax\AttributeController as AjaxAttributeController;
use App\Http\Controllers\Ajax\MenuController as AjaxMenuController;
use App\Http\Controllers\Ajax\LocationController;
use App\Http\Controllers\Backend\AttributeCatalogueController;
use App\Http\Controllers\Backend\AttributeController;
use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\GenerateController;
use App\Http\Controllers\Backend\LanguageController;
use App\Http\Controllers\Backend\MenuController;
use App\Http\Controllers\Backend\PermissionsController;
use App\Http\Controllers\Backend\PostCatalogueController;
use App\Http\Controllers\Backend\PostController;
use App\Http\Controllers\Backend\ProductCatalogueController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\SlideController;
use App\Http\Controllers\Backend\SystemController;
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
    ->middleware(['admin', 'locale', 'backend_default_locale'])
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
                Route::get('permission',                        [UserCatalogueController::class, 'permission'])->name('permission');
                Route::post('updatePermission',                 [UserCatalogueController::class, 'updatePermission'])->name('updatePermission');
            });

        Route::prefix('permissions')
            ->as('permissions.')
            ->group(function () {
                Route::get('index',                             [PermissionsController::class, 'index'])->name('index');
                Route::get('create',                            [PermissionsController::class, 'create'])->name('create');
                Route::post('store',                            [PermissionsController::class, 'store'])->name('store');
                Route::get('edit/{permission}',                 [PermissionsController::class, 'edit'])->where(['id' => '[0-9]+'])->name('edit');
                Route::put('udpate/{permission}',               [PermissionsController::class, 'udpate'])->where(['id' => '[0-9]+'])->name('udpate');
                Route::get('delete/{permission}',               [PermissionsController::class, 'delete'])->where(['id' => '[0-9]+'])->name('delete');
                Route::delete('destroy/{permission}',           [PermissionsController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('destroy');
            });

        Route::prefix('language')
            ->as('language.')
            ->group(function () {
                Route::get('index',                                 [LanguageController::class, 'index'])->name('index');
                Route::get('create',                                [LanguageController::class, 'create'])->name('create');
                Route::post('store',                                [LanguageController::class, 'store'])->name('store');
                Route::get('edit/{language}',                       [LanguageController::class, 'edit'])->where(['id' => '[0-9]+'])->name('edit');
                Route::put('udpate/{language}',                     [LanguageController::class, 'udpate'])->where(['id' => '[0-9]+'])->name('udpate');
                Route::get('delete/{language}',                     [LanguageController::class, 'delete'])->where(['id' => '[0-9]+'])->name('delete');
                Route::delete('destroy/{language}',                 [LanguageController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('destroy');
                Route::get('switch/{language}',                     [LanguageController::class, 'swicthBackendLanguage'])->where(['id' => '[0-9]+'])->name('switch');
                Route::get('translate/{id}/{languageId}/{model}',   [LanguageController::class, 'translate'])->where(['id' => '[0-9]+', 'languageId' => '[0-9]+'])->name('translate');
                Route::post('storeTranslate',                       [LanguageController::class, 'storeTranslate'])->name('storeTranslate');
            });

        Route::prefix('post_catalogue')
            ->as('post_catalogue.')
            ->group(function () {
                Route::get('index',                                     [PostCatalogueController::class, 'index'])->name('index');
                Route::get('create',                                    [PostCatalogueController::class, 'create'])->name('create');
                Route::post('store',                                    [PostCatalogueController::class, 'store'])->name('store');
                Route::get('edit/{post_catalogue}',                     [PostCatalogueController::class, 'edit'])->where(['id' => '[0-9]+'])->name('edit');
                Route::put('udpate/{post_catalogue}',                   [PostCatalogueController::class, 'udpate'])->where(['id' => '[0-9]+'])->name('udpate');
                Route::get('delete/{post_catalogue}',                   [PostCatalogueController::class, 'delete'])->where(['id' => '[0-9]+'])->name('delete');
                Route::delete('destroy/{post_catalogue}',               [PostCatalogueController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('destroy');
            });

        Route::prefix('posts')
            ->as('posts.')
            ->group(function () {
                Route::get('index',                             [PostController::class, 'index'])->name('index');
                Route::get('create',                            [PostController::class, 'create'])->name('create');
                Route::post('store',                            [PostController::class, 'store'])->name('store');
                Route::get('edit/{post}',                       [PostController::class, 'edit'])->where(['id' => '[0-9]+'])->name('edit');
                Route::put('udpate/{post}',                     [PostController::class, 'udpate'])->where(['id' => '[0-9]+'])->name('udpate');
                Route::get('delete/{post}',                     [PostController::class, 'delete'])->where(['id' => '[0-9]+'])->name('delete');
                Route::delete('destroy/{post}',                 [PostController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('destroy');
            });

        Route::prefix('generates')
            ->as('generates.')
            ->group(function () {
                Route::get('index',                                  [GenerateController::class, 'index'])->name('index');
                Route::get('create',                                 [GenerateController::class, 'create'])->name('create');
                Route::post('store',                                 [GenerateController::class, 'store'])->name('store');
                Route::get('edit/{generates}',                       [GenerateController::class, 'edit'])->where(['id' => '[0-9]+'])->name('edit');
                Route::put('udpate/{generates}',                     [GenerateController::class, 'udpate'])->where(['id' => '[0-9]+'])->name('udpate');
                Route::get('delete/{generates}',                     [GenerateController::class, 'delete'])->where(['id' => '[0-9]+'])->name('delete');
                Route::delete('destroy/{generates}',                 [GenerateController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('destroy');
            });

        Route::prefix('product_catalogue')
            ->as('product_catalogue.')
            ->group(function () {
                Route::get('index',                                           [ProductCatalogueController::class, 'index'])->name('index');
                Route::get('create',                                          [ProductCatalogueController::class, 'create'])->name('create');
                Route::post('store',                                          [ProductCatalogueController::class, 'store'])->name('store');
                Route::get('edit/{product_catalogue}',                        [ProductCatalogueController::class, 'edit'])->where(['id' => '[0-9]+'])->name('edit');
                Route::put('udpate/{product_catalogue}',                      [ProductCatalogueController::class, 'udpate'])->where(['id' => '[0-9]+'])->name('udpate');
                Route::get('delete/{product_catalogue}',                      [ProductCatalogueController::class, 'delete'])->where(['id' => '[0-9]+'])->name('delete');
                Route::delete('destroy/{product_catalogue}',                  [ProductCatalogueController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('destroy');
            });

        Route::prefix('product')
            ->as('product.')
            ->group(function () {
                Route::get('index',                                     [ProductController::class, 'index'])->name('index');
                Route::get('create',                                    [ProductController::class, 'create'])->name('create');
                Route::post('store',                                    [ProductController::class, 'store'])->name('store');
                Route::get('edit/{product}',                            [ProductController::class, 'edit'])->where(['id' => '[0-9]+'])->name('edit');
                Route::put('udpate/{product}',                          [ProductController::class, 'update'])->where(['id' => '[0-9]+'])->name('udpate');
                Route::get('delete/{product}',                          [ProductController::class, 'delete'])->where(['id' => '[0-9]+'])->name('delete');
                Route::delete('destroy/{product}',                      [ProductController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('destroy');
            });

        Route::prefix('attribute_catalogue')
            ->as('attribute_catalogue.')
            ->group(function () {
                Route::get('index',                                     [AttributeCatalogueController::class, 'index'])->name('index');
                Route::get('create',                                    [AttributeCatalogueController::class, 'create'])->name('create');
                Route::post('store',                                    [AttributeCatalogueController::class, 'store'])->name('store');
                Route::get('edit/{attribute_catalogue}',                [AttributeCatalogueController::class, 'edit'])->where(['id' => '[0-9]+'])->name('edit');
                Route::put('udpate/{attribute_catalogue}',              [AttributeCatalogueController::class, 'udpate'])->where(['id' => '[0-9]+'])->name('udpate');
                Route::get('delete/{attribute_catalogue}',              [AttributeCatalogueController::class, 'delete'])->where(['id' => '[0-9]+'])->name('delete');
                Route::delete('destroy/{attribute_catalogue}',          [AttributeCatalogueController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('destroy');
            });

        Route::prefix('attribute')
            ->as('attribute.')
            ->group(function () {
                Route::get('index',                           [AttributeController::class, 'index'])->name('index');
                Route::get('create',                          [AttributeController::class, 'create'])->name('create');
                Route::post('store',                          [AttributeController::class, 'store'])->name('store');
                Route::get('edit/{attribute}',                [AttributeController::class, 'edit'])->where(['id' => '[0-9]+'])->name('edit');
                Route::put('udpate/{attribute}',              [AttributeController::class, 'update'])->where(['id' => '[0-9]+'])->name('udpate');
                Route::get('delete/{attribute}',              [AttributeController::class, 'delete'])->where(['id' => '[0-9]+'])->name('delete');
                Route::delete('destroy/{attribute}',          [AttributeController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('destroy');
            });

        Route::prefix('system')
            ->as('system.')
            ->group(function () {
                Route::get('index',                           [SystemController::class, 'index'])->name('index');
                Route::post('store',                          [SystemController::class, 'store'])->name('store');
                Route::get('translate/{languageId}',          [SystemController::class, 'translate'])->where(['languageId' => '[0-9]+'])->name('translate');
                Route::post('saveTranslate/{languageId}',     [SystemController::class, 'saveTranslate'])->where(['languageId' => '[0-9]+'])->name('saveTranslate');
            });

        Route::prefix('menu')
            ->as('menu.')
            ->group(function () {
                Route::get('index',                      [MenuController::class, 'index'])->name('index');
                Route::get('create',                     [MenuController::class, 'create'])->name('create');
                Route::post('store',                     [MenuController::class, 'store'])->name('store');
                Route::get('edit/{menu}',                [MenuController::class, 'edit'])->where(['menu' => '[0-9]+'])->name('edit');
                Route::get('editMenu/{menu}',            [MenuController::class, 'editMenu'])->where(['menu' => '[0-9]+'])->name('editMenu');
                Route::put('udpate/{menu}',              [MenuController::class, 'update'])->where(['menu' => '[0-9]+'])->name('udpate');
                Route::get('delete/{menu}',              [MenuController::class, 'delete'])->where(['menu' => '[0-9]+'])->name('delete');
                Route::delete('destroy/{menu}',          [MenuController::class, 'destroy'])->where(['menu' => '[0-9]+'])->name('destroy');
                Route::get('children/{id}',              [MenuController::class, 'children'])->where(['id' => '[0-9]+'])->name('children');
                Route::post('saveChildren/{id}',         [MenuController::class, 'saveChildren'])->where(['id' => '[0-9]+'])->name('saveChildren');
                Route::get('translate/{languageId}/{id}',[MenuController::class, 'translate'])->where(['languageId' => '[0-9]+', 'id' => '[0-9]+'])->name('translate');
                Route::post('saveTranslate/{languageId}',[MenuController::class, 'saveTranslate'])->where(['languageId' => '[0-9]+'])->name('saveTranslate');
            });

        Route::prefix('slide')
            ->as('slide.')
            ->group(function () {
                Route::get('index',                      [SlideController::class, 'index'])->name('index');
                Route::get('create',                     [SlideController::class, 'create'])->name('create');
                Route::post('store',                     [SlideController::class, 'store'])->name('store');
                Route::get('edit/{slide}',               [SlideController::class, 'edit'])->where(['menu' => '[0-9]+'])->name('edit');
                Route::put('udpate/{slide}',             [SlideController::class, 'update'])->where(['menu' => '[0-9]+'])->name('udpate');
                Route::get('delete/{slide}',             [SlideController::class, 'delete'])->where(['menu' => '[0-9]+'])->name('delete');
                Route::delete('destroy/{slide}',         [SlideController::class, 'destroy'])->where(['menu' => '[0-9]+'])->name('destroy');
            });

        Route::get('ajax/location/getlocation',         [LocationController::class, 'getLocation'])->name('ajax.location.index');
        Route::post('ajax/dashboard/changeStatus',      [AjaxDashboardController::class, 'changeStatus'])->name('ajax.dashboard.changeStatus');
        Route::post('ajax/dashboard/changeStatusAll',   [AjaxDashboardController::class, 'changeStatusAll'])->name('ajax.dashboard.changeStatusAll');
        Route::get('ajax/dashboard/getMenu',            [AjaxDashboardController::class, 'getMenu'])->name('ajax.dashboard.getMenu');
        Route::get('ajax/attribute/getAttribute',       [AjaxAttributeController::class, 'getAttribute'])->name('ajax.attribute.getAttribute');
        Route::get('ajax/attribute/loadAttribute',      [AjaxAttributeController::class, 'loadAttribute'])->name('ajax.attribute.loadAttribute');
        Route::post('ajax/menu/createCatalogue',        [AjaxMenuController::class, 'createCatalogue'])->name('ajax.menu.createCatalogue');
        Route::post('ajax/menu/drag',                   [AjaxMenuController::class, 'drag'])->name('ajax.menu.drag');
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
