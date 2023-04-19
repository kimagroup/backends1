<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BridgeItController;
use App\Http\Controllers\PbController;
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
//General app routes
Route::get('/', [BridgeItController::class, 'index']);
Route::get('/check', [BridgeItController::class, 'check']);
//App backend routes
Route::get('/update', [BridgeItController::class, 'update']);
Route::get('/sessionid', [BridgeItController::class, 'session']);
Route::get('/minfo', [BridgeItController::class, 'info']);
Route::post('/memail', [BridgeItController::class, 'addemail']);
Route::post('/mregister', [BridgeItController::class, 'registration']);
//Postback handling
Route::get('/redirects', [PbController::class, 'redirec']);
Route::get('/ck', [PbController::class, 'checkclick']);
Route::get('/redirect', [PbController::class, 'redirect']);
Route::get('/set', [PbController::class, 'set']);
Route::get('/saveclick', [PbController::class, 'saveclick']);
Route::get('/click', function () {
    return view('click');
});
Route::get('/pb', [PbController::class, 'postback']);
Route::get('/pbtest', [PbController::class, 'postbacktest']);
Route::get('/c', [PbController::class, 'click']);
Route::get('/optimize', function(){
	
  \Artisan::call('route:cache');
	  \Artisan::call('view:cache');
	 \Artisan::call('config:cache');
});
Route::get('/clear', function(){
	  \Artisan::call('clear');
});

Route::get('/migrate', function(){
    \Artisan::call('migrate');
    dd('migrated!');
});