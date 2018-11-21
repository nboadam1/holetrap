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

Auth::routes();


Route::get('/', function () {
    return view('welcome');
});



Route::get('/home', 'HomeController@index')->name('home');


//Routes

Route::middleware(['auth'])->group(function(){
	//Roles
	Route::post('roles/store','RoleController@store')
			->name('roles.store')
			->middleware('permission:roles.create');

	Route::get('roles','RoleController@index')
			->name('roles.index')
			->middleware('permission:roles.index');

	Route::get('roles/create','RoleController@create')
			->name('roles.create')
			->middleware('permission:roles.create');

	Route::put('roles/{role}','RoleController@update')
			->name('roles.update')
			->middleware('permission:roles.edit');

	Route::get('roles/{role}','RoleController@show')
			->name('roles.show')
			->middleware('permission:roles.show');

	Route::delete('roles/{role}','RoleController@destroy')
			->name('roles.destroy')
			->middleware('permission:roles.destroy');

	Route::get('roles/{role}/edit','RoleController@edit')
			->name('roles.edit')
			->middleware('permission:roles.edit');

	//Reportes
	Route::post('reports/store','ReportController@store')
			->name('reports.store')
			->middleware('permission:reporte.create');

	Route::get('reports','ReportController@index')
			->name('reports.index')
			->middleware('permission:reporte.index');

	Route::get('reports/create','ReportController@create')
			->name('reports.create')
			->middleware('permission:reporte.create');

	Route::put('reports/{report}','ReportController@update')
			->name('reports.update')
			->middleware('permission:reporte.edit');

	Route::get('reports/{report}','ReportController@show')
			->name('reports.show')
			->middleware('permission:reporte.show');

	Route::delete('reports/{report}','ReportController@destroy')
			->name('reports.destroy')
			->middleware('permission:reporte.destroy');

	Route::get('reports/{report}/edit','ReportController@edit')
			->name('reports.edit')
			->middleware('permission:reporte.edit');

	//Users
	Route::get('users','UserController@index')
			->name('users.index')
			->middleware('permission:users.index');

	Route::put('users/{user}','UserController@update')
			->name('users.update')
			->middleware('permission:users.edit');

	Route::get('users/{user}','UserController@show')
			->name('users.show')
			->middleware('permission:users.show');

	Route::delete('users/{user}','UserController@destroy')
			->name('users.destroy')
			->middleware('permission:users.destroy');

	Route::get('users/{user}/edit','UserController@edit')
			->name('users.edit')
			->middleware('permission:users.edit');
});

