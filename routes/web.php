<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use \App\Http\Controllers\CompanyController;
use \App\Http\Controllers\EmployeeController;

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

Auth::routes();
Route::middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/companies/list', [App\Http\Controllers\CompanyController::class, 'list'])->name('companies.list');
    Route::get('/employees/list', [App\Http\Controllers\EmployeeController::class, 'list'])->name('employees.list');
    Route::resource('/companies', CompanyController::class)->missing(function (Request $request) {
            return Redirect::route('companies.list');
        });
    Route::resource('/employees', EmployeeController::class)->missing(function (Request $request) {
            return Redirect::route('employees.list');
        });
    //Route::resource('/api/v1/companies', CompanyController::class);
    //Route::resource('/api/v1/employees', EmployeeController::class);

    Route::get('/api/v1/companies', [CompanyController::class, 'index']);
    Route::post('/api/v1/companies', [CompanyController::class, 'store']);
    Route::get('/api/v1/companies/{id}', [CompanyController::class, 'show']);
    Route::patch('/api/v1/companies/{id}', [CompanyController::class, 'update']);
    Route::delete('/api/v1/companies/{id}', [CompanyController::class, 'destroy']);

    Route::get('/api/v1/employees', [EmployeeController::class, 'index']);
    Route::post('/api/v1/employees', [EmployeeController::class, 'store']);
    Route::get('/api/v1/employees/{id}', [EmployeeController::class, 'show']);
    Route::patch('/api/v1/employees/{id}', [EmployeeController::class, 'update']);
    Route::delete('/api/v1/employees/{id}', [EmployeeController::class, 'destroy']);
});
