<?php

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

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/', 'StoresController@index');
Route::get('mangaoh', 'StoresController@mangaoh_scraping')->name('stores.mangaoh_scraping');
Route::get('melonbooks', 'StoresController@melonbooks_scraping')->name('stores.melonbooks_scraping');
Route::get('publisher_delete', 'StoresController@publisher_delete')->name('stores.publisher_delete');

//Route::resource('books', 'BooksController', ['only' => ['index']]);
Route::resource('stores', 'StoresController', ['only' => ['index', 'show']]);
