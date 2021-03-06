<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
Route::post('/resources/addImage','ResourceController@addImage');
Route::get('/resources/deleteImage','ResourceController@deleteImage');
Route::get('/resources/images/{id}','ResourceController@getImages');
Route::get('/myResources','ResourceController@myResources');
Route::get('/resources/groupAssignment','ResourceController@groupAssignment');
Route::get('/resources/groups/{id}','ResourceController@listGroups');
Route::get('/resources/scheduleList/{id}','ResourceController@scheduleList');
Route::get('/resources/scheduleRequest','ResourceController@scheduleRequest');
Route::get('/resources/myRequests','ResourceController@myRequests');
Route::get('/resources/scheduleRequestProcess','ResourceController@scheduleRequestProcess');
Route::get('/resources/view/{id}','ResourceController@show');
Route::get('/resources/{id}','ResourceController@edit');
Route::put('/resources/{id}','ResourceController@update');
Route::delete('/resources/{id}','ResourceController@destroy');

Route::get('/images/{filename}',function($filename){
  $contents = Storage::get('images/'.$filename);
  $response = Response::make($contents,200);
  $response->header("Content-Type",'image/png');
  return $response;
});


