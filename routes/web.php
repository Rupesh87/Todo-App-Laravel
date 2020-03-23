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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/tasks','TaskController@index')->name('task.show') ;

Route::get('/tasks/view/{id}','TaskController@view')->name('task.view') ;

Route::get('/tasks/create', 'TaskController@create')->name('task.create'); 

Route::post('/tasks/store', 'TaskController@store')->name('task.store');

Route::get('/tasks/edit/{id}','TaskController@edit')->name('task.edit');

Route::get('/tasks/delete/{id}', 'TaskController@destroy')->name('task.delete') ;

Route::post('/tasks/update/{id}', 'TaskController@update')->name('task.update') ;

Route::get('/tasks/completed/{id}','TaskController@completed')->name('task.completed');


Route::get('/users', 'UserController@index')->name('user.index'); 
Route::get('/users/create', 'UserController@create')->name('user.create'); 

Route::post('/users/store', 'UserController@store')->name('user.store'); 
Route::get('/users/edit/{id}', 'UserController@edit')->name('user.edit'); 
Route::post('/users/update/{id}', 'UserController@update')->name('user.update') ;
Route::get('/users/delete/{id}', 'UserController@destroy')->name('user.delete') ;

Route::get('/staff/tasks', 'TaskController@staff')->name('task.staff') ;