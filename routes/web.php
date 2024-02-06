<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/register', 'App\Http\Controllers\HomeController@registerUser')->name('register');
Auth::routes();
Route::get('/', [LoginController::class, 'loginPage']);


Route::post('/logout', [LoginController::class, 'logout']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/admin', 'App\Http\Controllers\HomeController@index')->name('home');
// admin page
Route::get('/dashboard', 'App\Http\Controllers\HomeController@adminDashboardPage')->name('dashboard')->middleware('superAdmin');
Route::get('/management-stock', 'App\Http\Controllers\HomeController@managementStockPage')->name('management-stock')->middleware('superAdmin');
Route::get('/transaction', 'App\Http\Controllers\HomeController@managementTransactionPage')->name('transaction-page')->middleware('superAdmin');

// data
Route::get('/items', 'App\Http\Controllers\ManagementStockController@getItems')->name('items');
Route::post('/items/create', 'App\Http\Controllers\ManagementStockController@createItem')->name('items-create');
Route::post('/items/delete', 'App\Http\Controllers\ManagementStockController@deleteItem')->name('items-delete');
Route::get('/transactions', 'App\Http\Controllers\TransactionController@getTransactions')->name('transactions');
Route::post('/transactions/save', 'App\Http\Controllers\TransactionController@createTransaction')->name('transactions-save');