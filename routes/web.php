<?php

use Illuminate\Support\Facades\Route;

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
    return redirect('http://web.wellhistorydoc.com/');
});

//Route Pindah Laman
Route::post('/api/photos', 'ViewController@upload');
Route::get('/api/photos','ViewController@photos');
Route::post('/api/update/','ViewController@update');
Route::delete('/api/photos/{id}','ViewController@delete');
Route::get('/api/photos/{id}','ViewController@detail');
Route::get('/api/spinneritems/','ViewController@spinner');
Route::get('/api/excelreport','ViewController@excel');

Route::post('/api/login', 'ViewController@login');