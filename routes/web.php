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

// Auth
Route::get('login')->name('login')->uses('Auth\LoginController@showLoginForm')->middleware('guest');
Route::post('login')->name('login.attempt')->uses('Auth\LoginController@login')->middleware('guest');
// Route::post('logout')->name('logout')->uses('Auth\LoginController@logout');
Route::get('register')->name('register')->uses('Auth\RegisterController@showRegisterForm')->middleware('guest');

Route::get('/pinfo', function (Request $request) {
    return phpinfo();
});

// Users
require base_path('routes/web/users/users.php');

// Images
Route::get('/img/{path}', 'ImagesController@show')->where('path', '.*');

// Dashboard
require base_path('routes/web/dashboard/dashboard.php');

// Organizations
require base_path('routes/web/organizations/organizations.php');

// Contacts
require base_path('routes/web/contacts/contacts.php');

// Checkin
// require base_path('routes/web/checkin/checkin.php');

// Teams
require base_path('routes/web/teams/teams.php');

// RangeShift
require base_path('routes/web/rangeshift/rangeshift.php');

// 500 error
Route::get('500', function () {
    echo $fail;
});
