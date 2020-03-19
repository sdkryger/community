<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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
  if(Auth::user())
    return redirect('/home');
  else
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/groups/getJoinRequests/{id}','GroupController@getJoinRequests');
Route::get('/groups/processJoinRequest/{id}','GroupController@processJoinRequest');
Route::get('/groups/join', 'GroupController@join');
Route::post('/groups/joinRequest', 'GroupController@joinRequest');
Route::get('/groups/members/{id}', 'GroupController@members');
Route::get('/groups/setAdmin', 'GroupController@setAdmin');
Route::resource('groups', 'GroupController');

Route::get('/resources','ResourceController@listResources');
Route::get('/myResources','ResourceController@myResources');
Route::get('/resources/groupAssignment','ResourceController@groupAssignment');
Route::get('/resources/{id}','ResourceController@show');

